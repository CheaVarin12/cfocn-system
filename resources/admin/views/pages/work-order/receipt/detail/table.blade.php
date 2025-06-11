<div id="printableArea">
    <div class="receiptDetailLayout" id="printPage">
        {{-- header --}}
        @include('admin::pages.work-order.receipt.detail.header')

        <div id="form" class="form-wrapper">
            {{-- header --}}
            @include('admin::pages.work-order.receipt.detail.headTable', [
                'wLeft' => 70,
                'wRight' => 30,
            ])
            {{-- header --}}
            <div class="form-body">
                {{-- New --}}
                <div class="row">
                    <div class="table customTable">
                        <div class="table-wrapper purchaseInvoice">
                            
                            <div class="table-header bgGray">
                                <div class="row table-row-5 text-center">
                                    <span>NO</span>
                                </div>
                                <div class="row table-row-19 text-center">
                                    <span>ITEM</span>
                                </div>
                                <div class="row table-row-30 text-center">
                                    <span>DESCRIPTION</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>QTY</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>UOM</span>
                                </div>
                                <div class="row table-row-13 text-center">
                                    <span>UNIT PRICE</span>
                                </div>
                                <div class="row table-row-13 text-center">
                                    <span>AMOUNT</span>
                                </div>
                            </div>

                            <div class="table-body">
                                {{-- body --}}
                                <template x-for="(item,index) in data?.details">
                                    <div class="column" id="columnIDInvoiceDetail">
                                        <div class="row table-row-5">
                                            <span class="text-center" x-text="index+1"></span>
                                        </div>
                                        <div class="row table-row-19">
                                            <span class="text-center lineHeight13" x-text="item?.service?.name ?? ' '"></span>
                                        </div>
                                        <div class="row table-row-30 ivDes">
                                            <span class="text-left" x-text="item?.des ?? ''"></span>
                                        </div>
                                        <div class="row table-row-10">
                                            <span class="text-center" x-text="item?.qty ?? ' '"></span>
                                        </div>
                                        <div class="row table-row-10">
                                            <span class="text-center" x-text="item?.uom ?? ' '"></span>
                                        </div>
                                        <div class="row table-row-13">
                                            <div class="text-spaceBetween">
                                                <span>$</span>
                                                <span x-text="item?.price ? numberFormat(item?.price):''"></span>
                                            </div>
                                        </div>
                                        <div class="row table-row-13">
                                            <div class="text-spaceBetween">
                                                <span>$</span>
                                                <span x-text="item?.amount ? numberFormat(item?.amount) : ''"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- footer --}}
                                <div class="column footerReceiptDetail">
                                    <div class="row table-row-74">
                                        <div class="text"></div>
                                        <div class="bold" style="margin-bottom: 30px;"></div>
                                        <div class="inputTextArea">
                                            <div x-text="'Amount in Word:'"></div>
                                            <div x-text="data?.note??''" style="word-break: break-word;"></div>
                                        </div>
                                    </div>
                                    <div class="row table-row-26 right">
                                        <div class="column" style="height: 25% !important;">
                                            <div class="row table-row-50">
                                                <div class="div bgGray">
                                                    <div>SUBTOTAL</div>
                                                </div>
                                            </div>
                                            <div class="row table-row-50">
                                                <div class="divTag text-spaceBetween">
                                                    <span>$</span>
                                                    <span x-text="numberFormat(numberRound(data?.total_price, 2))"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column" style="height: 25% !important;">
                                            <div class="row table-row-50">
                                                <div class="div bgGray">
                                                    <div>VAT(10%)</div>
                                                </div>
                                            </div>
                                            <div class="row table-row-50">
                                                <div class="divTag text-spaceBetween">
                                                    <span>$</span>
                                                    <span
                                                        x-text="numberFormat(numberRound(data?.vat, 2))"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column" style="height: 25% !important;">
                                            <div class="row table-row-50">
                                                <div class="div bgGray">
                                                    <div>TOTAL</div>
                                                </div>
                                            </div>
                                            <div class="row table-row-50">
                                                <div class="divTag text-spaceBetween">
                                                    <span>$</span>
                                                    <span x-text="numberFormat(numberRound(data?.total_grand, 2))"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column" style="height: 25% !important;">
                                            <div class="row table-row-50">
                                                <div class="div bgGray">
                                                    <div>PORTAIL PAYMENT</div>
                                                </div>
                                            </div>
                                            <div class="row table-row-50">
                                                <div class="divTag text-spaceBetween">
                                                    <span>$</span>
                                                    <span x-text="data?.partial_payment ? numberFormat(numberRound(data?.partial_payment, 2)) : 0"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- footer --}}
        @include('admin::pages.work-order.receipt.detail.footer')
    </div>
</div>
