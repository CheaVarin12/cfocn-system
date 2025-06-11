<template x-data="{}" x-if="$store.editInvoice.active">
    <div class="dialog" x-data="xEditInvoice" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Edit Invoice</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#" method="POST">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Invoice Number <span>*</span></label>
                                    <input type="text" name="invoice_number" x-model="invoice_number"
                                        placeholder="Enter invoice number">
                                    <template x-for="item in dataError?.invoice_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Exchange rate<span>*</span></label>
                                    <input type="number" name="exchange_rate" x-model="exchang_rate"
                                        @input="calcuatorAmount" min="0" placeholder="Enter exchange rate">
                                    <template x-for="item in dataError?.exchange_rate">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Issue Date<span>*</span> </label>
                                    <input type="text" name="issue_date" id="issue_date" x-model="issue_date"
                                        x-ref="issue_date" autocomplete="off" readonly placeholder="Select issue date">
                                    <template x-for="item in dataError?.issue_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Start Period Date</label>
                                    <input type="text" name="period_start" id="period_start" x-model="period_start"
                                        x-ref="period_start" autocomplete="off" placeholder="Select start period date"
                                        readonly>
                                    <template x-for="item in dataError?.period_start">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>End Period Date</label>
                                    <input type="text" name="period_end" id="period_end" x-model="period_end"
                                        x-ref="period_end" autocomplete="off" placeholder="Select start period date"
                                        readonly>
                                    <template x-for="item in dataError?.period_end">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Charge Type</label>
                                    <select @change="calcuatorAmount" name="charge_type" x-model="charge_type">
                                        <option value="">Select type...</option>
                                        <option value="day">Day</option>
                                        <option value="month">Month</option>
                                        <option value="quarter">Quarter</option>
                                    </select>
                                    <template x-for="item in dataError?.charge_type">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Charge Number <span>*</span></label>
                                    <input type="number" name="charge_number" x-model="charge_number"
                                        placeholder="Enter number" min="0" @input="calcuatorAmount">
                                    <template x-for="item in dataError?.charge_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Day/Month</label>
                                    <input type="number" name="day_month" x-model="day_month"
                                        placeholder="Enter day / month" min="0" @input="calcuatorAmount">
                                    <template x-for="item in dataError?.day_month">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>PO/LO </label>
                                    <input type="text" name="po_number" x-model="po_number"  >
                                    <template x-for="item in dataError?.po_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- New --}}
                            <div class="row">
                                <div class="table customTable">
                                    <div class="table-wrapper purchaseInvoice">

                                        {{-- header --}}
                                        <div class="table-header">
                                            <div class="row table-row-4">
                                                <span class="font13">ល.រ</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span class="font13">ប្រភេទ</span>
                                            </div>
                                            <div class="row table-row-20 text-start">
                                                <span class="font13">បរិយាយមុខទំនិញ</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span class="font13">បរិមាណ</span>
                                            </div>
                                            <div class="row table-row-7 text-start">
                                                <span class="font13">ឯកតា</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span class="font13">ថ្លៃឯកតា($)</span>
                                            </div>
                                            <div class="row table-row-9 text-end ">
                                                <span class="font13">អត្រាប្រចាំឆ្នាំ (%)</span>
                                            </div>
                                            <div class="row table-row-9 text-end">
                                                <span class="font13">អត្រាប្រចាំឆ្នាំ(%)</span>
                                            </div>
                                            <div class="row table-row-13 text-end">
                                                <span class="font13">ថ្លៃទំនិញ($)</span>
                                            </div>
                                            <div class="row table-row-8 text-end">
                                                <span class="font13">Action</span>
                                            </div>
                                        </div>
                                        <div class="table-header">
                                            <div class="row table-row-4">
                                                <span>No</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span>Item</span>
                                            </div>
                                            <div class="row table-row-20 text-start">
                                                <span>Descriptiont</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>Quality</span>
                                            </div>
                                            <div class="row table-row-7 text-start">
                                                <span>UOM</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>Unit Price($)</span>
                                            </div>
                                            <div class="row table-row-9 text-end ">
                                                <span>Annual Rate (%)</span>
                                            </div>
                                            <div class="row table-row-9 text-end">
                                                <span>Annual Rate (%)</span>
                                            </div>
                                            <div class="row table-row-13 text-end">
                                                <span>Amount($)</span>
                                            </div>
                                            <div class="row table-row-8 text-end">
                                                <span>Action</span>
                                            </div>
                                        </div>

                                        <div class="table-body">

                                            {{-- projectName --}}
                                            <div class="column">
                                                <div class="row table-row-4"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-20 text-start">
                                                    <span class="label"
                                                        x-text="data?.invoice?.purchase?.project?.name ?? '---'"></span>
                                                </div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-7"></div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-9 "></div>
                                                <div class="row table-row-9"></div>
                                                <div class="row table-row-13"></div>
                                                <div class="row table-row-8"></div>
                                            </div>

                                            {{-- body --}}
                                            <template x-for="(item,index) in dataForm">
                                                <div class="column font13">
                                                    <div class="row table-row-4">
                                                        <span x-text="index+1"></span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span>
                                                            <select name="service_id[]"
                                                                x-model="item.service_id.value"
                                                                :class="item.service_id.error ? 'borderRed' : ''"
                                                                :disabled="formDisable">
                                                                <option value="">Select service...</option>
                                                                <template x-for="value in List_service_in_type">
                                                                    <option :value="value.id" x-text="value.name"
                                                                        :selected="item.service_id.value == value.id ? true : false">
                                                                    </option>
                                                                </template>
                                                            </select>
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-20 text-start">
                                                        <span>
                                                            <textarea x-model="item.des.value" :class="item.des.error ? 'borderRed' : ''" :disabled="formDisable"></textarea>
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-10">
                                                        <span>
                                                            <input type="number" placeholder="qty ..."
                                                                class="input-table" x-model="item.qty.value"
                                                                :class="item.qty.error ? 'borderRed' : ''"
                                                                min="0" step="any"
                                                                @input="inputChangeType(item,index,'qty')"
                                                                :disabled="formDisable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-7">
                                                        <span>
                                                            <input type="text" x-model="item.uom.value"
                                                                :class="item.uom.error ? 'borderRed' : ''"
                                                                name="uom[]" placeholder="uom ..."
                                                                class="input-table" :disabled="formDisable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-10">
                                                        <span>
                                                            <input type="number" x-model="item.price.value"
                                                                :class="item.price.error ? 'borderRed' : ''"
                                                                name="price[]" placeholder="price ..."
                                                                class="input-table" min="0" step="any"
                                                                @input="inputChangeType(item,index,'price')"
                                                                :disabled="formDisable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-9">
                                                        <span>
                                                            <input type="number" name="rate_first[]"
                                                                placeholder="rate ..." x-model="item.rate_first.value"
                                                                :class="item.rate_first.error ? 'borderRed' : ''"
                                                                class="input-table" min="0" step="any"
                                                                @input="inputChangeType(item,index,'rate_first')"
                                                                :disabled="formDisable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-9">
                                                        <span>
                                                            <input type="number" name="rate_second[]"
                                                                placeholder="rate ..." class="input-table"
                                                                min="0" step="any"
                                                                x-model="item.rate_second.value"
                                                                :class="item.rate_second.error ? 'borderRed' : ''"
                                                                @input="inputChangeType(item,index,'rate_second')"
                                                                :disabled="formDisable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-13">
                                                        <span>
                                                            <input type="number" name="amount[]"
                                                                placeholder="amount ..." class="input-table"
                                                                x-model="item.amount.value"
                                                                :class="item.amount.error ? 'borderRed' : ''"
                                                                @input="inputChangeType(item,index,'amount')"
                                                                min="0.01" step="0.01"
                                                                :disabled="formDisable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-8">
                                                        <span class="marginTop7">
                                                            <button type="button" class="delete"
                                                                @click="removeInput(item,index)"
                                                                :disabled="formDisable">
                                                                <i class="material-symbols-outlined">delete</i>
                                                            </button>
                                                            <template x-if="!item.remove || dataForm.length <= 1">
                                                                <button type="button" class="add"
                                                                    @click="addItem(index)" :disabled="formDisable">
                                                                    <i class="material-symbols-outlined">add</i>
                                                                </button>
                                                            </template>
                                                        </span>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- footer --}}
                                            <div class="column">
                                                <div class="row table-row-50 left">
                                                    <div class="text font13 invoiceRemark">
                                                        <span>Remark&nbsp;:</span>
                                                        <textarea type="text" x-model="remark"></textarea>
                                                    </div>
                                                    <div class="font13">
                                                        <span>Note: Ref.NBC Exchang Rate 1 USD = </span>
                                                        <span x-text="exchang_rate"></span>
                                                        <span>Riel</span>
                                                    </div>

                                                    <div class="inputTextArea">
                                                        <label class="font13">
                                                            <label>Amount in Word (English & Khmer)</label>
                                                            <span>*</span>
                                                        </label>
                                                        <textarea class="font13" x-model="note"></textarea>
                                                        <template x-for="item in dataError?.note">
                                                            <div class="errorCenter">
                                                                <span class="error" x-text="item"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="row table-row-50 right">
                                                    <div class="column">
                                                        <div class="row table-row-49">
                                                            <div class="div font13">
                                                                <div>សរុប</div>
                                                                <div>Sub total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(sub_total.dollar)"></div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(sub_total.khmer)"></div>
                                                        </div>
                                                    </div>
                                                    <div class="column">
                                                        <div class="row table-row-49">
                                                            <div class="div font13">
                                                                <div>អាករលើតម្លៃបន្ថែម១០%</div>
                                                                <div>VAT10%</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13">
                                                                <input type="number" min="0.01" step="0.01"
                                                                    x-model="vat.dollar" @input="amountCalVat()">
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(vat.khmer)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="column">
                                                        <div class="row table-row-49">
                                                            <div class="div font13">
                                                                <div>សរុបរួម</div>
                                                                <div>Grand Total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(grand_total.dollar)"></div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(grand_total.khmer)"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="button" class="primary" color="primary" @click="submitFrom()">
                                <i class='bx bx-save'></i>
                                <span>Save & Submit</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()">
                                <i class='bx bx-x'></i>
                                <span>Close</span>
                            </button>
                        </div>
                    </form>

                    <template x-if="submitLoading">
                        @include('admin::components.spinner')
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data('xEditInvoice', () => ({
        submitLoading: false,
        sub_total: {
            dollar: 0,
            khmer: 0
        },
        vat: {
            dollar: 0,
            khmer: 0
        },
        grand_total: {
            dollar: 0,
            khmer: 0
        },
        day_month: null,
        charge_number: null,
        charge_type: null,
        list_purchase_details: [],
        current_date: null,
        numberDay_of_month: 0,
        exchang_rate: 0,
        total_qty: 0,
        invoice_number: null,
        po_number: null,
        note: null,
        remark:null,
        dataError: [],
        issue_date: null,
        period_start: null,
        period_end: null,
        data: null,
        numDay: 0,
        formDisable: false,
        List_service_in_type: [],
        invoice_detail_arr_id: [],
        dataForm: [{
            id: Number(moment().format('YYYYMMDDHHmmss')),
            invoice_detail_id: null,
            service_id: {
                value: "",
                error: false
            },
            name: {
                value: ""
            },
            des: {
                value: "",
                error: false
            },
            uom: {
                value: "",
                error: false
            },
            qty: {
                value: "",
                error: false
            },
            price: {
                value: "",
                error: false
            },
            rate_first: {
                value: "",
                error: false
            },
            rate_second: {
                value: "",
                error: false
            },
            amount: {
                value: "",
                error: false
            },
            remove: false
        }],
        async init() {
            this.submitLoading = true;
            let dataStore = this.$store.editInvoice.options.data;
            this.current_date = moment(new Date).format('DD MMM YYYY');
            this.numberDay_of_month = moment(new Date).daysInMonth();
            let purchase = dataStore?.purchase;
            let url =
                `/admin/invoice/edit/${dataStore.id}?purchase_id=${purchase?.id}&purchase_type_id=${purchase?.type_id}`;
            setTimeout(async () => {
                try {
                    await this.fetchData(url, (res) => {
                        this.data = res;
                        this.charge_number = res?.invoice?.charge_number;
                        this.charge_type = res?.invoice?.charge_type;
                        this.invoice_number = res?.invoice?.invoice_number;
                        this.po_number = res?.invoice?.po_number;
                        this.issue_date = res?.invoice?.issue_date;
                        this.period_start = res?.invoice?.period_start;
                        this.period_end = res?.invoice?.period_end;
                        this.note = res?.invoice?.note;
                        this.remark = res?.invoice?.remark;
                        this.exchang_rate = res?.invoice?.exchange_rate ?? res.rate
                            ?.rate;
                        this.list_purchase_details = res?.invoice?.invoice_detail ??
                            [];
                        this.getServiceByType(res?.invoice?.purchase?.type_id);
                        if (res.invoice?.invoice_detail?.length > 0) {
                            this.dataForm = [];
                            res.invoice?.invoice_detail.forEach(val => {
                                let item = {
                                    id: Number(moment().format(
                                        'YYYYMMDDHHmmss')),
                                    invoice_detail_id: val.id,
                                    service_id: {
                                        value: val.service_id,
                                        error: false
                                    },
                                    des: {
                                        value: val.des,
                                        error: false
                                    },
                                    uom: {
                                        value: val.uom,
                                        error: false
                                    },
                                    qty: {
                                        value: val.qty,
                                        error: false
                                    },
                                    price: {
                                        value: val.price,
                                        error: false
                                    },
                                    rate_first: {
                                        value: val.rate_first,
                                        error: false
                                    },
                                    rate_second: {
                                        value: val.rate_second,
                                        error: false
                                    },
                                    amount: {
                                        value: val.amount,
                                        error: false
                                    },
                                    remove: true
                                };
                                this.dataForm.push(item);
                            });
                            this.dataForm[this.dataForm.length - 1].remove = false;
                        }
                        this.calcuatorAmount();
                        this.submitLoading = false;
                    });
                } catch (e) {
                    this.submitLoading = false;
                };
            }, 500);

            $("#period_start").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#period_end').datepicker('option', 'minDate', select)
                }
            });
            $("#period_end").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#period_start').datepicker('option', 'maxDate', select)
                }
            });
            $("#issue_date").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
            });
        },
        async getServiceByType(type_id) {
            await Axios.get(`/admin/purchase/type-service/${type_id?type_id:null}`).then(resp => {
                this.List_service_in_type = resp.data;
            });
        },
        async fetchData(url, callback) {
            await fetch(url, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                    }
                })
                .then(response => response.json())
                .then(response => {
                    callback(response);
                })
                .catch((e) => {})
                .finally(async (res) => {});
        },
        inputChangeType(item, index, type) {
            this.calcuatorAmount(type);
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
        calcuatorAmountOld(type = null) {
            this.sub_total.dollar = 0;
            this.sub_total.khmer = 0;
            this.vat.dollar = 0;
            this.vat.khmer = 0;
            this.total_qty = 0;
            this.list_purchase_details.forEach((item) => {

                let qty = Number(item.qty);
                if (type != "amount") {
                    if (item.rate_first > 0) {
                        this.rate_calculate = item.rate_first / 100;
                    } else {
                        this.rate_calculate = 1;
                    }
                    if (item.rate_second > 0) {
                        this.second_rate_calculate = item.rate_second / 100;
                    } else {
                        this.second_rate_calculate = 1;
                    }
                    if (this.day_month > 0) {
                        this.numDay = this.day_month;
                    } else {
                        this.numDay = this.numberDay_of_month
                    }
                    if (this.charge_type == 'day') {
                        item.amount = this.numberRound((qty * item.price * 0) + (item.price * qty /
                            this.numDay * this.charge_number), 2);

                    } else if (this.charge_type == 'quarter') {
                        if (this.charge_number == null) {
                            this.charge_number = 4;
                        }
                        item.amount = this.numberRound((item.price * qty * this.rate_calculate *
                                this
                                .second_rate_calculate) / this.charge_number,
                            2);
                    } else if (this.charge_type == 'month' && this.charge_number) {
                        /// if number of charge over than 1 year 
                        item.amount = this.numberRound(item.price * qty * this
                            .rate_calculate * this
                            .second_rate_calculate / 12 * this.charge_number, 2);
                    } else if (this.charge_type == 'month' && !this.charge_number) {
                        item.amount = this.numberRound(item.price * qty * this
                            .rate_calculate * this.second_rate_calculate / 12, 2);
                    } else {
                        if (this.charge_number) {
                            item.amount = this.numberRound(item.price * qty * this.charge_number,
                                2);
                        } else {
                            item.amount = this.numberRound(item.price * qty, 2);
                        }
                    }
                }
                this.sub_total.dollar += Number(item.amount);
                this.total_qty += item.qty;
            });
            //dollar
            this.sub_total.dollar = this.numberRound(this.sub_total.dollar, 2);
            this.vat.dollar = this.numberRound(Number(this.sub_total.dollar * (10 / 100)), 2);
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(this.vat
                .dollar), 2);

            //khmer
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this
                .exchang_rate));
            this.sub_total.khmer = this.numberRound((this.grand_total.khmer / 1.1));
            this.vat.khmer = this.numberRound(this.grand_total.khmer - this.sub_total.khmer);
        },
        calcuatorAmount(type = null) {
            this.sub_total.dollar = 0;
            this.sub_total.khmer = 0;
            this.vat.dollar = 0;
            this.vat.khmer = 0;
            this.total_qty = 0;
            this.dataForm.forEach((item) => {
                let qty = parseFloat(item.qty.value ? item.qty.value : 0);
                let price = parseFloat(item.price.value ? item.price.value : 0);
                let rate_first = parseFloat(item.rate_first.value ? item.rate_first.value : 0);
                let rate_second = parseFloat(item.rate_second.value ? item.rate_second.value : 0);

                if (type != "amount") {
                    this.rate_calculate = rate_first > 0 ? (rate_first / 100) : 1;
                    this.second_rate_calculate = rate_second > 0 ? (rate_second / 100) : 1;
                    this.numDay = this.day_month > 0 ? this.day_month : this.numberDay_of_month;

                    if (this.charge_type == 'day') {
                        item.amount.value = this.numberRound((qty * price * 0) + (price * qty / this
                            .numDay * this.charge_number), 2);
                    } else if (this.charge_type == 'quarter') {
                        if (this.charge_number == null) {
                            this.charge_number = 4;
                        }
                        item.amount.value = this.numberRound((price * qty * this.rate_calculate *
                            this.second_rate_calculate) / this.charge_number, 2);
                    } else if (this.charge_type == 'month' && this.charge_number) {
                        // if number of charge over than 1 year 
                        item.amount.value = this.numberRound(price * qty * this.rate_calculate *
                            this.second_rate_calculate / 12 * this.charge_number, 2);
                    } else if (this.charge_type == 'month' && !this.charge_number) {
                        item.amount.value = this.numberRound(price * qty * this
                            .rate_calculate * this.second_rate_calculate / 12, 2);
                    } else {
                        if (this.charge_number) {
                            item.amount.value = this.numberRound(price * qty * this
                                .charge_number, 2);
                        } else {
                            item.amount.value = this.numberRound(price * qty, 2);
                        }
                    }

                }
                let amount = parseFloat(item.amount.value ? item.amount.value : 0);
                this.sub_total.dollar += amount;
                this.total_qty += qty;
            });

            //dollar
            this.sub_total.dollar = this.numberRound(this.sub_total.dollar, 2);
            this.vat.dollar = this.numberRound(Number(this.sub_total.dollar * (10 / 100)), 2);
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(this.vat
                .dollar), 2);

            //khmer
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this
                .exchang_rate));
            this.sub_total.khmer = this.numberRound((this.grand_total.khmer / 1.1));
            this.vat.khmer = this.numberRound(this.grand_total.khmer - this.sub_total.khmer);

        },
        checkValidation(callback) {
            let error = [];
            if (this.dataForm.length > 0) {
                this.dataForm.forEach(val => {
                    this.checkValue(val.service_id) ? error.push(true) : false;
                    this.checkValue(val.des) ? error.push(true) : false;
                    this.checkValue(val.uom) ? error.push(true) : false;
                    this.checkValue(val.qty) ? error.push(true) : false;
                    this.checkValue(val.price) ? error.push(true) : false;
                    this.checkValue(val.amount) ? error.push(true) : false;
                });
            }
            callback(error);
        },
        checkValue(data) {
            if (!data.value) {
                data.error = true;
                return true;
            } else {
                data.error = false;
                return false;
            }
        },
        increateItemOneEmptyData() {
            let dataObject = {
                id: Number(moment().format('YYYYMMDDHHmmss')),
                invoice_detail_id: null,
                service_id: {
                    value: "",
                    error: false
                },
                des: {
                    value: "",
                    error: false
                },
                uom: {
                    value: "",
                    error: false
                },
                qty: {
                    value: "",
                    error: false
                },
                price: {
                    value: "",
                    error: false
                },
                rate_first: {
                    value: "",
                    error: false
                },
                rate_second: {
                    value: "",
                    error: false
                },
                amount: {
                    value: "",
                    error: false
                },
                remove: false
            };

            this.dataForm.push(dataObject);
        },
        addItem(index) {
            this.checkValidation((res) => {
                if (res.length > 0) {
                    return false;
                } else {
                    this.dataForm[index].remove = true;
                    this.increateItemOneEmptyData();
                }
            });
        },
        removeInput(item, index) {
            if (this.dataForm.length == 1) {
                this.dataForm = [];
                this.increateItemOneEmptyData();
            } else {
                this.dataForm.splice(index, 1);
            }
            this.dataForm[this.dataForm.length - 1].remove = false;
            if (item?.invoice_detail_id) {
                this.invoice_detail_arr_id.push(item.invoice_detail_id);
            }
            this.calcuatorAmount();
        },
        submitFrom() {
            let dataStore = this.$store.editInvoice.options.data;
            this.dataError = [];
            this.checkValidation((valid) => {
                if (valid.length > 0) {
                    return true;
                } else {
                    this.$store.confirmDialog.open({
                        data: {
                            title: "Message",
                            message: "Are you sure to save?",
                            btnClose: "Close",
                            btnSave: "Yes",
                        },
                        afterClosed: (result) => {
                            if (result) {
                                this.submitLoading = true;
                                let issue_date = this.$refs.issue_date.value;
                                let period_start = this.$refs.period_start.value;
                                let period_end = this.$refs.period_end.value;
                                let data = {
                                    invoice_number: this.invoice_number,
                                    po_number: this.po_number,
                                    po_id: dataStore.po_id,
                                    customer_id: dataStore.customer_id,
                                    data_customer: dataStore?.data_customer ?? null,
                                    total_price: this.sub_total.dollar,
                                    vat: this.vat.dollar,
                                    total_grand: this.grand_total.dollar,
                                    charge_number: this.charge_number,
                                    total_qty: this.total_qty,
                                    charge_type: this.charge_type,
                                    install_number: null,
                                    paid_status: 'Pending',
                                    issue_date: issue_date,
                                    exchange_rate: this.exchang_rate,
                                    invoice_period: null,
                                    period_start: period_start,
                                    period_end: period_end,
                                    note: this.note,
                                    remark:this.remark,
                                    status: 1,
                                    day_month: this.day_month,
                                    purchase_details: this.dataForm.length > 0 ?
                                        JSON.stringify(this.dataForm) : [],
                                    invoice_detail_arr_delete: this
                                        .invoice_detail_arr_id
                                };
                                setTimeout(() => {
                                    Axios({
                                        url: `{{ route('admin-invoice-update') }}`,
                                        method: 'POST',
                                        data: {
                                            ...data,
                                            id: dataStore.id,
                                            deleteItemID: [],
                                        }
                                    }).then((res) => {
                                        this.submitLoading = false;
                                        this.dialogClose();
                                        let currentFullUrl =
                                            '{!! url()->full() !!}';
                                        reloadUrl(currentFullUrl);
                                    }).catch((e) => {
                                        this.dataError = e.response
                                            ?.data.errors;
                                        this.submitLoading = false;

                                    }).finally(() => {
                                        this.submitLoading = false;
                                    });
                                }, 500);
                            }
                        }
                    });
                }
            });
        },
        dialogClose() {
            this.$store.editInvoice.active = false;
        }

    }));
</script>
<script>
    Alpine.store('editInvoice', {
        active: false,
        options: {
            data: null,
            selected: null,
            multiple: false,
            title: 'Choose an option',
            placeholder: 'Type to search...',
            allow_close: true,
            onReady: () => {},
            onSearch: () => {},
            beforeClose: () => {},
            afterClose: () => {}
        }
    });
    window.editInvoice = (options) => {
        Alpine.store('editInvoice', {
            active: true,
            options: {
                ...Alpine.store('editInvoice').options,
                ...options
            }
        });
    };
</script>
