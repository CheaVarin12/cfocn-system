
<div id="printableArea">
    <style>
        .table .table-header-custom .row {
            height: 60px !important;
        }
    </style>
    <div class="invoiceDetailLayout" id="printPage">
        {{-- header --}}
        @include('admin::pages.invoice.detail.header')

        <div id="form" class="form-wrapper">
            <div class="form-body" style="position: relative;">
                <div class="watermark" x-show="data.deleted_at">
                    <p class="bg-text">Void</p>
                </div>
                {{-- New --}}
                <div class="row" style="position: relative;">
                    <div class="table customTable">
                        <div class="table-wrapper purchaseInvoice">
                            {{-- header --}}
                            @include('admin::pages.invoice.detail.headerTable', [
                                'wLeft' => 50,
                                'wRight' => 50,
                            ])
                            {{-- header --}}
                            <div class="table-header table-header-custom" style="height: auto;">
                                <div class="row table-row-5 text-center">
                                    <span>ល.រ<br/>No</span>
                                </div>
                                <div class="row table-row-15 text-center">
                                    <span>ប្រភេទ<br/>Item</span>
                                </div>
                                <div class="row table-row-20 text-center">
                                    <span>បរិយាយមុខទំនិញ<br/>Description</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>បរិមាណ<br/>Quality</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>ឯកតា<br/>UOM</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>ថ្លៃឯកតា<br/>Unit Price($)</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>អត្រាប្រចាំឆ្នាំ<br/>Annual Rate(%)</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>អត្រាប្រចាំឆ្នាំ<br/>Annual Rate(%)</span>
                                </div>
                                <div class="row table-row-10 text-center">
                                    <span>ថ្លៃទំនិញ($)<br/>Amount($)</span>
                                </div>
                            </div>

                            <div class="table-body">

                                {{-- projectName --}}
                                <div class="column">
                                    <div class="row table-row-5"></div>
                                    <div class="row table-row-15"></div>
                                    <div class="row table-row-20 text-start">
                                        <span class="label" x-text="data?.purchase?.project?.name ?? ' '"></span>
                                    </div>
                                    <div class="row table-row-10"></div>
                                    <div class="row table-row-10 "></div>
                                    <div class="row table-row-10"></div>
                                    <div class="row table-row-10"></div>
                                    <div class="row table-row-10"></div>
                                    <div class="row table-row-10"></div>
                                </div>

                                {{-- body --}}
                                <template x-for="(item,index) in data?.invoice_detail">
                                    <div class="column" id="columnIDInvoiceDetail">
                                        <div class="row table-row-5">
                                            <span class="text-center" x-text="index+1"></span>
                                        </div>
                                        <div class="row table-row-15">
                                            <span class="text-center" x-text="item?.service?.name ?? ' '"></span>
                                        </div>
                                        <div class="row table-row-20 ivDes">
                                            <span class="text-left" x-text="item?.des ?? ''"></span>
                                        </div>
                                        <div class="row table-row-10">
                                            <span class="text-center" x-text="item?.qty ?? ' '"></span>
                                        </div>
                                        <div class="row table-row-10">
                                            <span class="text-center" x-text="item?.uom ?? ' '"></span>
                                        </div>
                                        <div class="row table-row-10">
                                            <div class="text-spaceBetween">
                                                <span>$</span>
                                                <span x-text="item?.price ? numberFormatEn(item?.price):''"></span>
                                            </div>
                                        </div>
                                        <div class="row table-row-10">
                                            <span class="text-center" x-text="item?.rate_first"></span>
                                        </div>
                                        <div class="row table-row-10">
                                            <span class="text-center" x-text="item?.rate_second"></span>
                                        </div>
                                        <div class="row table-row-10">
                                            <div class="text-spaceBetween">
                                                <span>$</span>
                                                <span x-text="item?.amount ? numberFormatEn(item?.amount) : ''"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- footer --}}
                                <div class="column footerInvoiceDetail">
                                    <div class="row table-row-50">
                                        <div class="text">
                                            <span x-text="'Remark : '+ (data?.remark??'')">&nbsp;:</span>
                                        </div>
                                        <div class="bold" style="margin-bottom: 30px;"></div>
                                        <div class="inputTextArea">
                                            <div
                                                x-text="'Note: Ref.NBC Exchang Rate On '+
                                        dateFormat(data?.issue_date) + ' 1 USD= '+ data?.exchange_rate + 'Riel'">
                                            </div>
                                            <div x-text="data?.note??' '" style="word-break: break-word;"></div>
                                        </div>
                                    </div>
                                    <div class="row table-row-50 right">
                                        <div class="column">
                                            <div class="row table-row-40">
                                                <div class="div">
                                                    <div>សរុប</div>
                                                    <div>Sub total</div>
                                                </div>
                                            </div>
                                            <div class="row table-row-30">
                                                <div class="divTag text-spaceBetween">
                                                    <span>$</span>
                                                    <span
                                                        x-text="numberFormatEn(numberRound(data?.total_price, 2))"></span>
                                                </div>
                                            </div>
                                            <div class="row table-row-30">
                                                <div class="divTag text-spaceBetween">
                                                    <span>R</span>
                                                    <span
                                                        x-text="numberFormatKh(numberRound(data?.total_price_kh))"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <div class="row table-row-40">
                                                <div class="div">
                                                    <div>អាករលើតម្លៃបន្ថែម១០%</div>
                                                    <div>VAT10%</div>
                                                </div>
                                            </div>
                                            <div class="row table-row-30">
                                                <div class="divTag text-spaceBetween">
                                                    <span>$</span>
                                                    <span x-text="numberFormatEn(numberRound(data?.vat, 2))"></span>
                                                </div>
                                            </div>
                                            <div class="row table-row-30">
                                                <div class="divTag text-spaceBetween">
                                                    <span>R</span>
                                                    <span x-text="numberFormatKh(numberRound(data?.vat_kh))"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <div class="row table-row-40">
                                                <div class="div">
                                                    <div>សរុបរួម</div>
                                                    <div>Grand Total</div>
                                                </div>
                                            </div>
                                            <div class="row table-row-30">
                                                <div class="divTag text-spaceBetween">
                                                    <span>$</span>
                                                    <span x-text="numberFormatEn(numberRound(data?.total_grand, 2))"></span>
                                                </div>
                                            </div>
                                            <div class="row table-row-30">
                                                <div class="divTag text-spaceBetween">
                                                    <span>R</span>
                                                    <span x-text="numberFormatKh(numberRound(data?.total_grand_kh))"></span>
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
        @include('admin::pages.invoice.detail.footer')
    </div>
</div>
