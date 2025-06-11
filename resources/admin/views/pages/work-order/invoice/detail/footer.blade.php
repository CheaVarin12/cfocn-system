<div id="footerInvoiceDetail">
    <div class="footerInvoiceText">
        <div>
            <span>Payment Instruction</span>
            <span>Please kindly remit payment to:</span>
            <span>(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK CO., LTD.</span>
            <template x-for="(item, index) in bankAccount">
                <span x-text="item.bank_name + ' No. ' + item.account_number"></span>
            </template>
            <template x-if="$store.invoiceDetail?.options?.typeBtnStatus == 'void'">
                <span><span x-text="des_reason"></span></span>
            </template>
        </div>
    </div>
    <div class="footerSignature">
        <div class="column" style="text-align: center">
            <hr>
            <span>ហត្ថលេខា និង ឈ្មោះអ្នកទិញ</span><br>
            <span>Customer's Signature &Name </span>
        </div>
        <div class="column" style="text-align: center">
            <hr>
            <span>ត្រួតពិនិត្យដោយ</span><br>
            <span>Approved by</span>
        </div>
        <div class="column" style="text-align: center">
            <hr>
            <span>ហត្ថលេខា​ និងឈ្មោះអ្នកលក់</span><br>
            <span>Seller's Signature & Name</span>
        </div>
    </div>
</div>
