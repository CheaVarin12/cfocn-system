<div class="table-header tableHeaderCus">
    <div class="row table-row-{{$wLeft}}">
        <div class="txt">
            <span>ឈ្មោះក្រុមហ៊ុន&nbsp;:</span>
            <span x-text="data?.dataCustomer?.name_kh ?? ''"></span>
        </div>
    </div>
    <div class="row table-row-{{$wRight}}">
        <div class="txt">
            <span>លេខរៀងវិក្កយបត្រ/​ Invoice&nbsp;:</span>
            <span x-text="data?.invoice_number ?? ''"></span>
        </div>
    </div>
</div>
<div class="table-header tableHeaderCus">
    <div class="row table-row-{{$wLeft}}">
        <div class="txt"
            x-text="'Company name : '+(data.dataCustomer?.name_en ?? '')">
        </div>
    </div>
    <div class="row table-row-{{$wRight}}">
        <div class="txt">
            <span>កាលបរិច្ឆេទ/ Date&nbsp;:</span>
            <span x-text="dateFormat(data?.issue_date)"></span>
        </div>
    </div>
</div>
<div class="table-header tableHeaderCus">
    <div class="row table-row-{{$wLeft}}">
        <div class="txt">
            <span>អាស័យដ្ឋាន&nbsp;:</span>
            <span x-text="data?.dataCustomer?.address_kh ?? ''"></span>
        </div>
    </div>
    <div class="row table-row-{{$wRight}}">
        <div class="txt">
            <span>រយៈកាលបរិច្ឆេទ/ Invoice Period&nbsp;:</span>
            <span x-text="dateFormat(data?.period_start)"></span>
            <span x-text="data?.period_start && data?.period_end ? '-':''"></span>&nbsp;
            <span x-text="dateFormat(data?.period_end)"></span>
        </div>
    </div>
</div>
<div class="table-header tableHeaderCus">
    <div class="row table-row-{{$wLeft}}">
        <div class="txt"
            x-text="'Address : '+(data.dataCustomer?.address_en ?? '')">
        </div>
    </div>
    <div class="row table-row-{{$wRight}}">
        <div class="txt"><span>លេខកិច្ចសន្យា/ Contract No. :</span> <span x-text="data?.order && data?.order?.contract_number ? data?.order?.contract_number:''"></span></div>
    </div>
</div>
<div class="table-header tableHeaderCus">
    <div class="row table-row-{{$wLeft}}">
        <div class="txt">
            <span>ទូរសព្ទ័លេខ/Telephone&nbsp;:</span>
            <span x-text="data.dataCustomer?.phone ?? ''"></span>
        </div>
    </div>
    <div class="row table-row-{{$wRight}}">
        <div class="txt">
            <span>Order. Nº&nbsp;:</span>
            <span class="pon" x-text="data?.order?.order_number ?? ''"></span>
        </div>
    </div>
</div>
<div class="table-header tableHeaderCus">
    <div class="row table-row-{{$wLeft}}">
        <div class="txt"><span>អ្នកទទួល / Attention : </span><span x-text="data.dataCustomer?.attention ?? '' "></span></div>
    </div>
    <div class="row table-row-{{$wRight}}">
        <div class="txt">Ref&nbsp;: <span x-text="''"></span></div>
    </div>
</div>
<div class="table-header tableHeaderCus">
    <div class="row table-row-{{$wLeft}}">
        <div class="txt">
            <span>លេខអត្តសញ្ញាណកម្ម អតប (VATIN)&nbsp;: </span>
            <span x-text="data.dataCustomer?.vat_tin??''"></span>
        </div>
    </div>
    <div class="row table-row-{{$wRight}}">
        <div class="txt"></div>
    </div>
</div>
