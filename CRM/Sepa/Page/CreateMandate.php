<?php

require_once 'CRM/Core/Page.php';

class CRM_Sepa_Page_CreateMandate extends CRM_Core_Page {

  protected $IBAN_REFERENCE_TYPE = 754;       // "IBAN"
  protected $PAYMENT_INSTRUMENT_ID = 9000;    // SEPA
  protected $CONTRIBUTION_STATUS_ID = 2;      // "pending"

  function run() {
    if (isset($_REQUEST['mandate_type'])) {
      $contact_id = $_REQUEST['contact_id'];
      $this->assign("back_url", CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid=${contact_id}&selectedChild=contribute"));

      if ($_REQUEST['mandate_type']=='OOFF') {
        $this->createOOFFMandate();
      }

    } else if (isset($_REQUEST['cid'])) {
      $this->prepareCreateForm();

    }
    parent::run();
  }


  function createOOFFMandate() {
    // TODO: Sanity check

    // first create a contribution
    $contribution_data = array(
        'version'                   => 3,
        'contact_id'                => $_REQUEST['contact_id'],
        'total_amount'              => $_REQUEST['total_amount'],
        'campaign_id'               => $_REQUEST['campaign_id'],
        'financial_type_id'         => $_REQUEST['financial_type_id'],
        'payment_instrument_id'     => $this->PAYMENT_INSTRUMENT_ID,
        'contribution_status_id'    => $this->CONTRIBUTION_STATUS_ID,
        'receive_date'              => $_REQUEST['date'],
        'source'                    => $_REQUEST['source'],
        //'is_pay_later'              => 1,
      );

    $contribution = civicrm_api('Contribution', 'create', $contribution_data);
    if ($contribution['is_error']) {
      CRM_Core_Session::setStatus(sprintf(ts("Couldn't find contact #%s"), $cid), ts('Error'), 'error');
      $this->assign("error_title", ts("Couldn't create contribution"));
      $this->assign("error_message", ts($contribution['error_message']));
      return;
    }

    // create a note, if requested
    if ($_REQUEST['note']) {
      // add note
      $create_note = array(
        'version'                   => 3,
        'entity_table'              => 'civicrm_contribution',
        'entity_id'                 => $contribution['id'],
        'note'                      => $_REQUEST['note'],
        'privacy'                   => 0,
      );

      $create_note_result = civicrm_api('Note', 'create', $create_note);
      if ($create_note_result['is_error']) {
        // don't consider this a fatal error...
        CRM_Core_Session::setStatus(sprintf(ts("Couldn't create note for contribution #%s"), $contribution['id']), ts('Error'), 'alert');
        error_log($create_note_result['error_message']);
      }
    }

    // next, create mandate
    $mandate_data = array(
        'version'                   => 3,
        'debug'                     => 1,
        'reference'                 => "WILL BE SET BY HOOK",
        'contact_id'                => $_REQUEST['contact_id'],
        'entity_table'              => 'civicrm_contribution',
        'entity_id'                 => $contribution['id'],
        'creation_date'             => date('YmdHis'),
        'validation_date'           => date('YmdHis'),
        'date'                      => $_REQUEST['date'],
        'iban'                      => $_REQUEST['iban'],
        'bic'                       => $_REQUEST['bic'],
        'status'                    => 'OOFF',
        'type'                      => 'OOFF',
        'creditor_id'               => $_REQUEST['creditor_id'],
        'is_enabled'                => 1,
      );
    // call the hook for mandate generation
    // TODO: Hook not working: CRM_Utils_SepaCustomisationHooks::create_mandate($mandate_data);
    sepa_civicrm_create_mandate($mandate_data);

    $mandate = civicrm_api('SepaMandate', 'create', $mandate_data);
    if ($mandate['is_error']) {
      CRM_Core_Session::setStatus(sprintf(ts("Couldn't find contact #%s"), $cid), ts('Error'), 'error');
      $this->assign("error_title", ts("Couldn't create contribution"));
      $this->assign("error_message", ts($mandate['error_message']));
      return;
    }

    $this->assign("reference", $mandate_data['reference']);
  }





  function prepareCreateForm() {
    // load financial types
    $this->assign("financial_types", CRM_Contribute_PseudoConstant::financialType());
    $this->assign("today", date('Y-m-d'));

    // first, try to load contact
    $contact_id = $_REQUEST['cid'];
    $contact = civicrm_api('Contact', 'getsingle', array('version' => 3, 'id' => $contact_id));
    if ($contact['is_error']) {
      CRM_Core_Session::setStatus(sprintf(ts("Couldn't find contact #%s"), $cid), ts('Error'), 'error');
      $this->assign("display_name", "ERROR");
      return;
    }

    $this->assign("contact_id", $contact_id);
    $this->assign("display_name", $contact['display_name']);

    // look up campaigns
    $campaign_query = civicrm_api('Campaign', 'get', array('version'=>3, 'is_active'=>1));
    $campaigns = array();
    if (!$campaign_query['is_error']) {
      foreach ($campaign_query['values'] as $campaign_id => $campaign) {
        $campaigns[$campaign_id] = $campaign['name'];
      }
    }
    $this->assign('campaigns', $campaigns);

    // look up account in other SEPA mandates
    $known_accounts = array();
    $query_sql = "SELECT DISTINCT iban, bic FROM civicrm_sdd_mandate WHERE contact_id=$contact_id;";
    $old_mandates = CRM_Core_DAO::executeQuery($query_sql);
    while ($old_mandates->fetch()) {
      $value = $old_mandates->iban.'/'.$old_mandates->bic;
      array_push($known_accounts, 
        array("name" => $old_mandates->iban, "value"=>$value));
    }


    // look up account in CiviBanking (if enabled...)
    $accounts = civicrm_api('BankingAccount', 'get', array('version' => 3, 'contact_id' => $contact_id));
    if (!$accounts['is_error']) {
      foreach ($accounts['values'] as $account_id => $account) {
        $account_ref = civicrm_api('BankingAccountReference', 'getsingle', array('version' => 3, 'ba_id' => $account_id, 'reference_type_id' => $this->IBAN_REFERENCE_TYPE));
        if (!isset($account_ref['is_error'])) {
          $account_data = json_decode($account['data_parsed']);
          if (isset($account_data->BIC)) {
            // we have IBAN and BIC -> add:
            $value = $account_ref['reference'].'/'.$account_data->BIC;
            array_push($known_accounts, 
              array("name" => $account_ref['reference'], "value"=>$value));
          }
        }
      }
    }
    array_push($known_accounts, array("name" => ts("enter new account"), "value"=>"/"));
    $this->assign("known_accounts", $known_accounts);

    // look up creditors
    $creditor_query = civicrm_api('SepaCreditor', 'get', array('version' => 3));
    $creditors = array();
    if (!$creditor_query['is_error']) {
      foreach ($creditor_query['values'] as $creditor_id => $creditor) {
        $creditors[$creditor_id] = $creditor['name'];
      }
    }
    $this->assign('creditors', $creditors);
    
    // all seems to be ok.
    $this->assign("submit_url", CRM_Utils_System::url('civicrm/sepa/cmandate'));
  }
}