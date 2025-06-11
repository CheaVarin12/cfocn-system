@component('admin::components.dialog', ['dialog' => 'reportSaleJournalDetail'])
    <div x-data="reportSaleJournalDetail" class="dialog-form reportSaleJournalDetail" style="width: 800px">
        <div class="titleReportDetail">
            <h3>Sale journal detail</h3>
            <button type="button" @click="$store.reportSaleJournalDetail.close(false)" class="btnClose"><i
                    class='bx bx-x'></i></button>
        </div>
        <div class="dialog-form-body">
            <div class="itemText">
                <label>ថ្ងៃខែ</label>
                <span>:</span>
                <p x-text="dateFormat(data?.issue_date)"></p>
            </div>
            <div class="itemText">
                <label>លេខវិក្ក័យបត្រ</label>
                <span>:</span>
                <p x-text="data.type_invoice == 'invoice' ? data?.invoice_number : data?.credit_note_number"></p>
            </div>
            <div class="itemText">
                <label>អតិថិជន</label>
                <span>:</span>
                <p x-text="data?.dataCustomer?.name_en ?? ''"></p>
            </div>
            <div class="itemText">
                <label>លេខអត្តសញ្ញាណកម្មអតិថិជន</label>
                <span>:</span>
                <p x-text="data.customer_vat_tin ? data?.dataCustomer?.vat_tin :''"></p>
            </div>
            <div class="itemText">
                <label>បរិមាណ</label>
                <span>:</span>
                <p x-text="data?.total_qty ?? ''" class="redQty"></p>
            </div>
            <div class="itemText">
                <label>បរិយាយ</label>
                <span>:</span>
                <div class="div">
                    <template x-if="data.type_invoice == 'invoice'">
                        <template x-for="item in data?.invoice_detail">
                            <p x-text="item?.des ?? ''"></p>
                        </template>
                    </template>
                    <template x-if="data.type_invoice == 'credit_note'">
                        <template x-for="item in data?.credit_note_detail">
                            <p x-text="item?.des ?? ''"></p>
                        </template>
                    </template>
                </div>
            </div>
            <div class="itemText">
                <label>អត្រាប្តូរប្រាក់</label>
                <span>:</span>
                <p class="redQty">
                    <span>R&nbsp;</span>
                    <span x-text="numberFormat(numberRound(data?.exchange_rate))"></span>
                </p>
            </div>
        </div>
        <div class="tableLayoutCon">
            <div class="tableLy">
                <div class="tableCustomScroll">
                    <div class="table excel">
                        <div class="excel-body">
                            <table class="tableWidth">
                                <thead class="column">
                                    <tr>
                                        <th class="row table-row-50 text-center" colspan="11">ការលក់</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" rowspan="2" class="row table-row-15 text-center">
                                            លក់ចេញមិនជាប់អាករ
                                        </th>
                                        <th rowspan="2" rowspan="2" class="row table-row-15 text-center">នាំចេញ ឬ
                                            លក់ចេញក្រៅស្រុក</th>
                                        <th colspan="4" class="row table-row-15 text-center">លក់អោយបុគ្គលជាប់អាករ</th>
                                        <th colspan="4" class="row table-row-15 text-center">លក់អោយអ្នកប្រើប្រាស់</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">តំលៃជាប់អាករ</th>
                                        <th colspan="2">អាករ</th>
                                        <th colspan="2">តំលៃជាប់អាករ</th>
                                        <th colspan="2">អាករ</th>
                                    </tr>
                                    <tr>
                                        {{-- a --}}
                                        <th>US</th>
                                        <th>KHR</th>
                                        <th>US</th>
                                        {{-- a --}}
                                        {{-- b --}}
                                        <th>US</th>
                                        <th>KHR</th>
                                        <th>US</th>
                                        <th>KHR</th>
                                        {{-- b --}}
                                        {{-- c --}}
                                        <th>US</th>
                                        <th>KHR</th>
                                        <th>US</th>
                                        <th>KHR</th>
                                        {{-- c --}}

                                    </tr>
                                </thead>

                                <tbody>
                                    <tr :class="data?.credit_note_number ? 'bgRed' : ''">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        {{-- oneDerf --}}
                                        <td>
                                            <div x-show="data.customer_vat_tin">
                                                <span>$&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_price, 2))"></span>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div x-show="data.customer_vat_tin">
                                                <span>R&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_price_kh))"></span>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div x-show="data.customer_vat_tin">
                                                <span>$&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_rate, 2))"></span>
                                                </span>
                                            </div>
                                        <td>
                                            <div x-show="data.customer_vat_tin">
                                                <span>R&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_rate_kh))"></span>
                                                </span>
                                            </div>
                                        </td>

                                        {{-- twoDerf --}}
                                        <td>
                                            <div x-show="!data.customer_vat_tin">
                                                <span>$&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_price, 2))"></span>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div x-show="!data.customer_vat_tin">
                                                <span>R&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_price_kh))"></span>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div x-show="!data.customer_vat_tin">
                                                <span>$&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_rate, 2))"></span>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div x-show="!data.customer_vat_tin">
                                                <span>R&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_rate_kh))"></span>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="column tfootReportItem" style=" position: sticky;inset-inline-start: 0;">
                                    <tr>
                                        <td class="textRight" colspan="9">សរុបថ្លៃលក់រួម (US)&nbsp;:</td>
                                        <td colspan="2">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_grand,2))"></span>
                                                </span>
                                            </div>
                                        </td>
                                        {{-- <td>US</td>
                                        <td>KHR</td> --}}
                                    </tr>
                                    <tr>
                                        <td class="textRight" colspan="9">សរុបថ្លៃលក់រួម (KHR)&nbsp;:</td>
                                        <td colspan="2">
                                            <div>
                                                <span>R&nbsp;</span>
                                                <span>
                                                    <span x-text="(data?.type_invoice=='credit_note' ?'-':'')"></span>
                                                    <span x-text="numberFormat(numberRound(data?.total_grand_kh))"></span>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dialog-form-footer dmc-status-footer center">
            <button type="button" class="close" @click="$store.reportSaleJournalDetail.close(false)"
                x-text="data?.btnClose || 'Close'"></button>
        </div>
    </div>
    <script>
        Alpine.data("reportSaleJournalDetail", () => ({
            data: null,
            disabled: false,
            loading: false,
            timeCloseAuto: 0,
            init() {
                this.data = this.$store.reportSaleJournalDetail.data;
                this.data.dataCustomer = this.data?.data_customer ? this.jsonPase(this.data.data_customer) :
                    this.data?.customer;
                this.data.des = null;
                feather.replace();
            },
            jsonPase(data) {
                return JSON.parse(data);
            },
            onConfirm() {
                this.disabled = true;
                this.$store.reportSaleJournalDetail.close(true);
            },
            numberRound(num, decimalPlaces = null) {
                if (!decimalPlaces) {
                    return Math.round(num);
                }
                var p = Math.pow(10, decimalPlaces);
                return Math.round(num * p) / p;
            },
            numberFormat(num) {
                return new Intl.NumberFormat().format(num.toFixed(2));
            },
            dateFormat(date) {
                return date ? moment(date).locale('en').format('DD-MM-YYYY') : '---';
            },
        }))
    </script>
@endcomponent
