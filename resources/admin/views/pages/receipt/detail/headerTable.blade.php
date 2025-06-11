<div class="headerTableReceipt">
    <div class="HTRLeft">
        <div class="txtItem">
            <span>TO&nbsp;:&nbsp;</span>
            <span x-text="data?.dataCustomer?.name_en??''"></span>
        </div>
        <div class="txtItem">
            <span>Address&nbsp;:&nbsp;</span>
            <span x-text="data?.dataCustomer?.address_en??''"></span>
        </div>
        <div class="txtItem">
            <span>Telephone Nº&nbsp;:&nbsp;</span>
            <span x-text="data?.dataCustomer?.phone??''"></span>
        </div>
    </div>
    <div class="HTRRight">
        <div class="txtItem">
            <span>Receipt No.&nbsp;:&nbsp;</span>
            <span x-text="data?.receipt_number??''"></span>
        </div>
        <div class="txtItem">
            <span>Receipt Date&nbsp;:&nbsp;</span>
            <span x-text="dateFormatEn(data?.issue_date, 'YYYY-MM-DD')"></span>
        </div>
        <div class="txtItem">
            <span>Invoice Ref&nbsp;:&nbsp;</span>
            <span x-show="data?.receipt_from == 'credit_note'" x-text="data?.credit_note?.credit_note_number ??''"></span>
            <span x-show="data?.receipt_from != 'credit_note'" x-text="data?.invoices?.invoice_number ??''"></span>
        </div>
    </div>
</div>