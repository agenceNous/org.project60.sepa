<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2019
 *
 * Generated from org.project60.sepa/xml/schema/CRM/Sepa/ContributionGroup.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:9411aecc2f873abbf0aa4a191e23e23b)
 */

/**
 * Database access object for the SEPAContributionGroup entity.
 */
class CRM_Sepa_DAO_SEPAContributionGroup extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_sdd_contribution_txgroup';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = FALSE;

  /**
   * primary key
   *
   * @var int
   */
  public $id;

  /**
   * FK to Contribution ID
   *
   * @var int
   */
  public $contribution_id;

  /**
   * FK to civicrm_sdd_txgroup
   *
   * @var int
   */
  public $txgroup_id;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_sdd_contribution_txgroup';
    parent::__construct();
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'contribution_id', 'civicrm_contribution', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'txgroup_id', 'civicrm_sdd_txgroup', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Sepa_ExtensionUtil::ts('primary key'),
          'where' => 'civicrm_sdd_contribution_txgroup.id',
          'table_name' => 'civicrm_sdd_contribution_txgroup',
          'entity' => 'SEPAContributionGroup',
          'bao' => 'CRM_Sepa_DAO_SEPAContributionGroup',
          'localizable' => 0,
        ],
        'contribution_id' => [
          'name' => 'contribution_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Sepa_ExtensionUtil::ts('FK to Contribution ID'),
          'where' => 'civicrm_sdd_contribution_txgroup.contribution_id',
          'table_name' => 'civicrm_sdd_contribution_txgroup',
          'entity' => 'SEPAContributionGroup',
          'bao' => 'CRM_Sepa_DAO_SEPAContributionGroup',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contribute_DAO_Contribution',
        ],
        'txgroup_id' => [
          'name' => 'txgroup_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Sepa_ExtensionUtil::ts('FK to civicrm_sdd_txgroup'),
          'where' => 'civicrm_sdd_contribution_txgroup.txgroup_id',
          'table_name' => 'civicrm_sdd_contribution_txgroup',
          'entity' => 'SEPAContributionGroup',
          'bao' => 'CRM_Sepa_DAO_SEPAContributionGroup',
          'localizable' => 0,
          'FKClassName' => 'CRM_Sepa_DAO_SEPATransactionGroup',
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'sdd_contribution_txgroup', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'sdd_contribution_txgroup', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [
      'contriblookup' => [
        'name' => 'contriblookup',
        'field' => [
          0 => 'contribution_id',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_sdd_contribution_txgroup::0::contribution_id',
      ],
      'txglookup' => [
        'name' => 'txglookup',
        'field' => [
          0 => 'txgroup_id',
        ],
        'localizable' => FALSE,
        'sig' => 'civicrm_sdd_contribution_txgroup::0::txgroup_id',
      ],
    ];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
