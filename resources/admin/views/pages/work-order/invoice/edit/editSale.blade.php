<template x-data="{}" x-if="$store.EditInvoiceSale.active">
    <div class="dialog" x-data="xEditInvoiceSale" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Edit Invoice Sale</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper">
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
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Start Period Date</label>
                                    <input type="text" name="period_start" x-model="period_start" id="period_start"
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
                            </div>
                            <div class="row-2">
                                <div class="form-row">
                                    <label>INSTAL.# <span>*</span></label>
                                    <input type="number" name="charge_number" x-model="charge_number"
                                        placeholder="Enter number" min="0" oninput="validity.valid||(value='');"
                                        @input="calcuatorAmount">
                                    <template x-for="item in dataError?.charge_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Install Number<span>*</span></label>
                                    <input type="number" name="install_number" x-model="install_number"
                                        placeholder="Enter number" min="0" oninput="validity.valid||(value='');"
                                        @input="calcuatorAmount">
                                    <template x-for="item in dataError?.install_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                    <template x-if="charge_number_err">
                                        <div class="errorCenter">
                                            <span class="error">The value of install number must be less than or equal
                                                to INSTAL.#</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Tax Status <span>*</span></label>
                                    <select name="tax_status" x-model="tax_status" @change="calcuatorAmount">
                                        <template x-for="item in taxOptions">
                                            <option :value="item.key"><span x-text="item.text"></span></option>
                                        </template>
                                    </select>
                                    <template x-for="item in dataError?.tax_status">
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
                                            <div class="row table-row-3">
                                                <span class="font13">ល.រ</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span class="font13">ប្រភេទ</span>
                                            </div>
                                            <div class="row table-row-30 text-start">
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
                                            <div class="row table-row-10 text-end ">
                                                <span class="font13">ចំនួនលើក</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span class="font13">ថ្លៃទំនិញ($)</span>
                                            </div>
                                            <div class="row table-row-8 text-end">
                                                <span class="font13">Action</span>
                                            </div>
                                        </div>
                                        <div class="table-header">
                                            <div class="row table-row-3">
                                                <span>No</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span>Item</span>
                                            </div>
                                            <div class="row table-row-30 text-start">
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
                                            <div class="row table-row-10 text-end">
                                                <span>INSTAL.# </span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span>Amount($)</span>
                                            </div>
                                            <div class="row table-row-8 text-end">
                                                <span>Action</span>
                                            </div>
                                        </div>

                                        <div class="table-body">

                                            {{-- projectName --}}
                                            <div class="column">
                                                <div class="row table-row-3"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-30 text-start">
                                                    <span class="label"
                                                        x-text="data?.invoice?.order?.project?.name ?? '---'"></span>
                                                </div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-7"></div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-10 "></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-8"></div>
                                            </div>

                                            {{-- body --}}
                                            <template x-for="(item,index) in dataForm">
                                                <div class="column font13">
                                                    <div class="row table-row-3">
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
                                                    <div class="row table-row-30 text-start">
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
                                                    <div class="row table-row-10">
                                                        <span>
                                                            <div class="installNumber">
                                                                <span x-text="install_number?install_number:0"></span>
                                                                <span> / </span>
                                                                <span x-text="charge_number?charge_number:0"></span>
                                                            </div>
                                                        </span>

                                                    </div>
                                                    <div class="row table-row-15">
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
                                                        <label>Amount in Word (English & Khmer)<span>*</span></label>
                                                        <textarea x-model="note" class="font13"></textarea>
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
                                                            {{-- <div class="divTag font13"x-text="numberFormat(vat.dollar)"></div> --}}
                                                            <div class="divTag font13">
                                                                <input type="number" min="0.01" step="0.01"
                                                                    x-model="vat.dollar" @input.debounce.500="amountCalculateVat($el)">
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
                </div>
                <template x-if="submitLoading">
                    @include('admin::components.spinner')
                </template>
            </div>
        </div>
    </div>
    </div>
</template>
<script>
    Alpine.data('xEditInvoiceSale', () => ({
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
        charge_number_err: false,
        charge_type: null,
        list_order_details: [],
        current_date: null,
        numberDay_of_month: 0,
        exchang_rate: 0,
        total_qty: 0,
        invoice_number: null,
        note: null,
        remark:null,
        dataError: [],
        install_number: null,
        issue_date: null,
        period_start: null,
        period_end: null,
        data: null,
        List_service_in_type: [],
        invoice_detail_arr_id: [],
        formDisable: false,
        tax_status: null,
        taxOptions: @json(config('dummy.tax_status')),
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
            let dataStore = this.$store.EditInvoiceSale.options.data;
            this.current_date = moment(new Date).format('DD MMM YYYY');
            this.numberDay_of_month = moment(new Date).daysInMonth();
            let order = dataStore?.order;
            let url =
                `/admin/work-order/invoice/edit/${dataStore.id}?order_id=${order?.id}&order_type_id=${order?.type_id}`;
            setTimeout(async () => {
                try {
                    await this.fetchData(url, (res) => {
                        this.data = res;
                        this.charge_number = res?.invoice?.charge_number;
                        this.charge_type = res?.invoice?.charge_type;
                        this.invoice_number = res?.invoice?.invoice_number;
                        this.issue_date = res?.invoice?.issue_date;
                        this.period_start = res?.invoice?.period_start;
                        this.period_end = res?.invoice?.period_end;
                        this.note = res?.invoice?.note;
                        this.remark = res?.invoice?.remark;
                        this.charge_number = res?.invoice?.charge_number;
                        this.install_number = res?.invoice?.install_number;
                        this.tax_status = res?.invoice?.tax_status,
                        this.exchang_rate = res.invoice.exchange_rate ?? res.rate
                            ?.rate;
                        this.list_order_details = res.invoice?.invoice_detail ??
                            [];
                        this.getServiceByType(res?.invoice?.order?.type_id);
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
                        this.vat.dollar = res?.invoice?.vat;
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
            await Axios.get(`/admin/work-order/order/type-service/${type_id?type_id:null}`).then(resp => {
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
        calcuatorAmount(type = null) {
            this.sub_total.dollar = 0;
            this.sub_total.khmer = 0;
            this.vat.dollar = this.vat.dollar;
            this.vat.khmer = 0;
            this.total_qty = 0;

            this.dataForm.forEach((item) => {
                let qty = parseFloat(item.qty.value ? item.qty.value : 0);
                let price = parseFloat(item.price.value ? item.price.value : 0);
                this.install = this.charge_number > 0 ? this.charge_number : 1;
                if (type != "amount") {
                    item.amount.value = this.numberRound(price * qty / this.install, 2);
                }
                let amount = parseFloat(item.amount.value ? item.amount.value : 0);
                this.sub_total.dollar += amount;
                this.total_qty += qty;
            });

           //dollar
           this.sub_total.dollar = this.numberRound(this.sub_total.dollar, 2);
            if (this.tax_status != 2) {
                this.vat.dollar = this.numberRound(Number(this.sub_total.dollar * (10 /
                    100)), 2);
            } else {
                this.vat.dollar = 0;
            }
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(this.vat
                .dollar), 2);

            //khmer
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this
                .exchang_rate));

            if (this.tax_status != 2) {
                this.sub_total.khmer = this.numberRound((this.grand_total.khmer / 1.1));
                this.vat.khmer = this.numberRound(this.vat.dollar * this.exchang_rate);
            } else {
                this.sub_total.khmer = this.grand_total.khmer;
            }


        },
        amountCalculateVat(el) {
            let vatDollar = el.value;
            this.vat.khmer = this.numberRound(Number(vatDollar) * Number(this.exchang_rate));
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(vatDollar), 2);
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this.exchang_rate));
        },
        validationInstallNumber(cb) {
            if (Number(this.charge_number) < Number(this.install_number)) {
                cb(true);
            } else {
                cb(false);
            }
        },
        submitFrom() {
            this.dataError = [];
            this.checkValidation((valid) => {
                if (valid.length > 0) {
                    return true;
                } else {
                    this.validationInstallNumber(res => {
                        if (res) {
                            this.charge_number_err = true;
                        } else {
                            this.charge_number_err = false;
                            let dataStore = this.$store.EditInvoiceSale.options.data;
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
                                        let issue_date = this.$refs.issue_date
                                            .value;
                                        let period_start = this.$refs
                                            .period_start.value;
                                        let period_end = this.$refs.period_end
                                            .value;
                                        let data = {
                                            invoice_number: this
                                                .invoice_number,
                                            order_id: dataStore.order_id,
                                            customer_id: dataStore
                                                .customer_id,
                                            data_customer: dataStore
                                                ?.data_customer ?? null,
                                            total_price: this.sub_total
                                                .dollar,
                                            vat: this.vat.dollar,
                                            total_grand: this.grand_total
                                                .dollar,
                                            charge_number: this
                                                .charge_number,
                                            total_qty: this.total_qty,
                                            charge_type: this.charge_type,
                                            install_number: this
                                                .install_number,
                                            paid_status: 'Pending',
                                            issue_date: issue_date,
                                            exchange_rate: this
                                                .exchang_rate,
                                            invoice_period: null,
                                            period_start: period_start,
                                            period_end: period_end,
                                            note: this.note,
                                            remark:this.remark,
                                            status: 1,
                                            day_month: this.day_month,
                                            order_details: this.dataForm
                                                .length > 0 ?
                                                JSON.stringify(this
                                                    .dataForm) : [],
                                            invoice_detail_arr_delete: this
                                                .invoice_detail_arr_id,
                                                tax_status: this.tax_status,
                                        };
                                        setTimeout(() => {
                                            Axios({
                                                url: `{{ route('admin-work-order-invoice-update') }}`,
                                                method: 'POST',
                                                data: {
                                                    ...data,
                                                    id: dataStore
                                                        .id,
                                                    deleteItemID: [],
                                                }
                                            }).then((res) => {
                                                this.submitLoading =
                                                    false;
                                                this
                                            .dialogClose();
                                                let currentFullUrl =
                                                    '{!! url()->full() !!}';
                                                reloadUrl(
                                                    currentFullUrl
                                                    );
                                            }).catch((e) => {
                                                this.dataError =
                                                    e.response
                                                    ?.data
                                                    .errors;
                                                this.submitLoading =
                                                    false;

                                            }).finally(() => {
                                                this.submitLoading =
                                                    false;
                                            });
                                        }, 500);
                                    }
                                }
                            });
                        }
                    });
                }
            });
        },
        dialogClose() {
            this.$store.EditInvoiceSale.active = false;
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

    }));
</script>
<script>
    Alpine.store('EditInvoiceSale', {
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
    window.EditInvoiceSale = (options) => {
        Alpine.store('EditInvoiceSale', {
            active: true,
            options: {
                ...Alpine.store('EditInvoiceSale').options,
                ...options
            }
        });
    };
</script>
