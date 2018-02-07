    <PmtInf>
      <PmtInfId>{$group.reference}</PmtInfId>
      <PmtMtd>DD</PmtMtd>
      <BtchBookg>false</BtchBookg>
      <NbOfTxs>{$nbtransactions}</NbOfTxs>
      <CtrlSum>{$total}</CtrlSum>
      <PmtTpInf>
        <SvcLvl>
          <Cd>SEPA</Cd>
        </SvcLvl>
        <LclInstrm>
          <Cd>CORE</Cd>
        </LclInstrm>
        <SeqTp>{$group.type}</SeqTp>
      </PmtTpInf>
      <ReqdColltnDt>{$group.collection_date|crmDate:"%Y-%m-%d"}</ReqdColltnDt>
      <Cdtr>
        <Nm>{$creditor.name}</Nm>
      </Cdtr>
      <CdtrAcct>
        <Id>
          <IBAN>{$creditor.iban}</IBAN>
        </Id>
      </CdtrAcct>
      <CdtrAgt>
        <FinInstnId>
          <BIC>{$creditor.bic}</BIC>
        </FinInstnId>
      </CdtrAgt>
      <ChrgBr>SLEV</ChrgBr>
      <CdtrSchmeId>
        <Id>
          <PrvtId>
            <Othr>
              <Id>{$creditor.identifier}</Id>
              <SchmeNm>
                <Prtry>SEPA</Prtry>
              </SchmeNm>
            </Othr>
          </PrvtId>
        </Id>
      </CdtrSchmeId>

{foreach from=$contributions item="contribution"}
      <DrctDbtTxInf>
        <PmtId>
          <EndToEndId>{$contribution.id}</EndToEndId>
        </PmtId>
        <InstdAmt Ccy="{$contribution.currency}">{$contribution.total_amount}</InstdAmt>
        <DrctDbtTx>
          <MndtRltdInf>
            <MndtId>{$contribution.reference}</MndtId>
            <DtOfSgntr>{$contribution.validation_date|crmDate:"%Y-%m-%d"}</DtOfSgntr>
          </MndtRltdInf>
        </DrctDbtTx>
        <DbtrAgt>
          <FinInstnId>
            <BIC>{$contribution.bic}</BIC>
          </FinInstnId>
        </DbtrAgt>
        <Dbtr>
          <Nm>{$contribution.display_name}</Nm>
        </Dbtr>
        <DbtrAcct>
          <Id>
            <IBAN>{$contribution.iban}</IBAN>
          </Id>
        </DbtrAcct>
        <RmtInf>
          <Ustrd>{$contribution.message}</Ustrd>
        </RmtInf>
      </DrctDbtTxInf>
{/foreach}

    </PmtInf>
