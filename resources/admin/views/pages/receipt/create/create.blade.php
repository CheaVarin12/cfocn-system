<template x-data="{}" x-if="$store.create.active">
    <div class="dialog" x-data="xCreate" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.create?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.create?.options?.title"></h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#" x-show="!loading">
                        <div class="form-body">
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Receipt From</label>
                                    <select @change="onChangeReceiptFrom()" x-model="dataForm.receipt_from"
                                        :disabled="dataForm.disable">
                                        <option value="">Select receipt from...</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="invoice">Invoice</option>
                                        <option value="credit_note">Credit Note</option>
                                    </select>
                                </div>
                                <template x-if="!$store.create.options.data">
                                    <div class="form-row">
                                        <label>Ref invoice Number</label>
                                        <div class="pickup_invoice" style="width: 100%;"
                                            :class="invoice_number_err.incorrect || invoice_number_err.empty ? 'borderRed' : ''">
                                            <input x-show="!dataForm.receipt_from || dataForm.receipt_from == 'deposit'" type="text" placeholder="Select ref invoice number ..."
                                                x-model="dataForm.invoice_number" readonly />
                                            <input x-show="dataForm.receipt_from == 'invoice'" type="text" placeholder="Select ref invoice number ..."
                                                x-model="dataForm.invoice_number" @click="selectInvoice()" readonly />
                                            <input x-show="dataForm.receipt_from == 'credit_note'" type="text" placeholder="Select ref credit note number ..."
                                                x-model="dataForm.invoice_number" @click="selectCreditNote()" readonly />
                                            <button type="button" @click="PickUpInvoice()" :disabled="!dataForm.receipt_from || dataForm.receipt_from == 'deposit'">Pick up invoice</button>
                                            <template x-if="dataForm.invoice_number">
                                                <button type="button" class="bgRed"
                                                    @click="ResetPathInput()">Reset</button>
                                            </template>
                                        </div>
                                        <template x-for="item in dataError?.invoice_number">
                                            <div class="errorCenter">
                                                <span class="error" x-text="item">Error</span>
                                            </div>
                                        </template>
                                        <template x-if="invoice_number_err.incorrect">
                                            <div class="errorCenter">
                                                <span class="error" x-text="'Ref invoice number incorrect'"></span>
                                            </div>
                                        </template>
                                        <template x-if="invoice_number_err.empty">
                                            <div class="errorCenter">
                                                <span class="error" x-text="'Pls enter ref invoice number'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="$store.create.options.data">
                                    <div class="form-row">
                                        <div class="pickup_invoice">
                                            <span class="pickUpReadOnly" x-text="dataForm.invoice_number"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Receipt Number<span>*</span></label>
                                    <input type="text" name="receipt_number" x-model="dataForm.receipt_number"
                                        placeholder="Enter receipt number" :disabled="dataForm.disable">
                                    <template x-for="item in dataError?.receipt_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Issue Date<span>*</span> </label>
                                    <input type="text" name="issue_date" id="issue_date"
                                        x-model="dataForm.issue_date" x-ref="issue_date" autocomplete="off" readonly
                                        placeholder="Select issue date" :disabled="dataForm.disable">
                                    <template x-for="item in dataError?.issue_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <template x-if="!dataForm.invoice_number">
                                <div class="row-2">
                                    <div class="form-row">
                                        <label>Customer<span>*</span></label>
                                        <select id="customer_id" name="customer_id" x-model="dataForm.customer_id"
                                            x-init="fetchSelectCustomer()" :disabled="dataForm.disable">
                                            <option value="">Select customer...</option>
                                        </select>
                                        <template x-for="item in dataError?.customer_id">
                                            <div class="errorCenter">
                                                <span class="error" x-text="item">Error</span>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="form-row">
                                        <label>Service Type<span>*</span></label>
                                        <select id="type_id" name="type_id" x-model="dataForm.type_id"
                                            x-init="fetchSelectType()" :disabled="dataForm.disable">
                                            <option value="">Select service type...</option>
                                        </select>
                                        <template x-for="item in dataError?.type_id">
                                            <div class="errorCenter">
                                                <span class="error" x-text="item">Error</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            {{-- New --}}
                            <div class="row" style="margin-top:10px;">
                                <div class="table customTable">
                                    <div class="table-wrapper purchaseInvoice">
                                        {{-- header --}}
                                        <div class="table-header bgGray">
                                            <div class="row table-row-5">
                                                <span>NO</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span>ITEM</span>
                                            </div>
                                            <div class="row table-row-30 text-start">
                                                <span>DESCRIPTION</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>QTY</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>UOM</span>
                                            </div>
                                            <div class="row table-row-12 text-start">
                                                <span>UNIT PRICE</span>
                                            </div>
                                            <div class="row table-row-18 text-end">
                                                <span>AMOUNT</span>
                                            </div>
                                            <div class="row table-row-10"></div>
                                        </div>

                                        <div class="table-body">
                                            {{-- body --}}
                                            <template x-for="(item,index) in dataTable">
                                                <div class="column font13">
                                                    <div class="row table-row-5">
                                                        <span class="text-center" x-text="index+1"></span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span>
                                                            <select name="service_id[]"
                                                                x-model="item.service_id.value"
                                                                :class="item.service_id.error ? 'borderRed' : ''"
                                                                :disabled="dataForm.disable">
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
                                                            <textarea x-model="item.des.value" :class="item.des.error ? 'borderRed' : ''" :disabled="dataForm.disable"></textarea>
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-10">
                                                        <span>
                                                            <input type="number" placeholder="qty ..."
                                                                class="input-table" x-model="item.qty.value"
                                                                :class="item.qty.error ? 'borderRed' : ''"
                                                                min="0" step="any"
                                                                @input="inputChangeType(item,index,'qty')"
                                                                :disabled="dataForm.disable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-10">
                                                        <span>
                                                            <input type="text" x-model="item.uom.value"
                                                                :class="item.uom.error ? 'borderRed' : ''"
                                                                name="uom[]" placeholder="uom ..."
                                                                class="input-table" :disabled="dataForm.disable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-12">
                                                        <span>
                                                            <input type="number" x-model="item.price.value"
                                                                :class="item.price.error ? 'borderRed' : ''"
                                                                name="price[]" placeholder="price ..."
                                                                class="input-table" min="0" step="any"
                                                                @input="inputChangeType(item,index,'price')"
                                                                :disabled="dataForm.disable" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-18">
                                                        <span>
                                                            <input type="number" name="amount[]"
                                                                class="input-table inputReadOnly"
                                                                x-model="item.amount.value"
                                                                :class="item.amount.error ? 'borderRed' : ''"
                                                                @input="inputChangeType(item,index,'amount')"
                                                                min="0.01" step="0.01"
                                                                :disabled="dataForm.disable" readonly />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-10">
                                                        <span class="marginTop7">
                                                            <button type="button" class="delete"
                                                                @click="removeInput(index)"
                                                                :disabled="dataForm.disable">
                                                                <i class="material-symbols-outlined">delete</i>
                                                            </button>
                                                            <template x-if="!item.remove || dataTable.length <= 1">
                                                                <button type="button" class="add"
                                                                    @click="addItem(index)"
                                                                    :disabled="dataForm.disable">
                                                                    <i class="material-symbols-outlined">add</i>
                                                                </button>
                                                            </template>
                                                        </span>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- footer --}}
                                            <div class="column footerColumnReceipt">
                                                <div class="row table-row-60 left">
                                                    <div class="inputTextArea">
                                                        <label class="font13">
                                                            <p>Amount in Word</p>
                                                            <span>*</span>
                                                        </label>
                                                        <textarea class="font13" x-model="dataForm.note" style="min-height: 55px;"></textarea>
                                                        <template x-for="item in dataError?.note">
                                                            <div class="errorCenter">
                                                                <span class="error" x-text="item"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="row table-row-40 right">
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-37">
                                                            <div class="div font13 bgGray">
                                                                <div>Sub total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-42">
                                                            <div class="divTag font13">$</div>
                                                            <div class="divTag font13">
                                                                <input type="number" x-model="dataForm.total_price"
                                                                    placeholder="sub total ..."
                                                                    :disabled="dataForm.disable" min="0"
                                                                    step="any" @input="subTotalInput()"
                                                                    class="height30"
                                                                    :class="inputTotalPriceErr ? 'err' : ''"
                                                                    style="padding: 5px 0;" />
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-23"></div>
                                                    </div>
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-37">
                                                            <div class="div font13 bgGray">
                                                                <div>VAT10%</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-42">
                                                            <div class="divTag font13">$</div>
                                                            {{-- <div class="divTag font13" x-text="dataForm?.vat?numberRound(dataForm?.vat,2):0"></div> --}}
                                                            <div class="divTag font13">
                                                                <input type="number" x-model="dataForm.vat"
                                                                    placeholder="0" :disabled="dataForm.disable" 
                                                                    min="0"
                                                                    step="any" @input="subTotalInput()"
                                                                    class="height30"
                                                                    style="padding: 5px 0;" />
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-23"></div>
                                                    </div>
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-37">
                                                            <div class="div font13 bgGray">
                                                                <div>Grand Total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-42">
                                                            <div class="divTag font13">$</div>
                                                            <div class="divTag font13"
                                                                x-text="dataForm?.total_grand?numberRound(dataForm?.total_grand,2):0">
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-23"></div>
                                                    </div>
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-37">
                                                            <div class="div font13 bgGray">
                                                                <div>Portail Payment</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-42">
                                                            <div class="divTag font13">$</div>
                                                            <div class="divTag font13">
                                                                <input type="number" x-model="dataForm.partial_payment"
                                                                    placeholder="partial payment ..."
                                                                    :disabled="dataForm.disable" min="0"
                                                                    step="any" @input="subTotalInput()"
                                                                    class="height30"
                                                                    :class="inputTotalPartialErr ? 'err' : ''"
                                                                    style="padding: 5px 0;" />
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-23"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="button" class="primary" color="primary" @click="submitFrom()"
                                :disabled="dataForm.disable || inputTotalPriceErr || inputTotalPartialErr">
                                <i class='bx bx-save'></i>
                                <span>Save & Submit</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()"
                                :disabled="dataForm.disable">
                                <i class='bx bx-x'></i>
                                <span>Close</span>
                            </button>
                        </div>
                    </form>

                    <template x-if="loading">
                        @include('admin::components.spinner')
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data('xCreate', () => ({
        loading: false,
        data: null,
        dataError: [],
        dataForm: {
            disable: false,
            receipt_from: null, //'invoice',
            invoice_ref_id: null,
            invoice_number: null,
            receipt_number: null,
            customer_id: null,
            project_id: null,
            type_id: null,
            note: null,
            issue_date: null,
            vat: 0,
            total_qty: 0,
            total_price: 0,
            paid_amount: 0,
            debt_amount: 0,
            total_grand: 0,
            partial_payment: 0,
            sum_total: 0,
        },
        invoice_number_err: {
            empty: false,
            incorrect: false
        },
        inputTotalPriceErr: false,
        inputTotalPartialErr: false,
        List_service_in_type: [],
        dataTable: [{
            id: Number(moment().format('YYYYMMDDHHmmss')),
            service_id: {
                value: "",
                error: false
            },
            name: {
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
        }],
        imageLogoSelectOption: "data:image/png;base64,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",
        async init() {
            this.loading = true;
            await setTimeout(async () => {
                try {
                    this.data = this.$store.create.options.data;
                    //patchValue
                    this.dataForm.receipt_number = this.data.receipt_number;
                    this.dataForm.issue_date = this.data.issue_date;
                    this.dataForm.total_price = this.data.total_price;
                    this.dataForm.vat = this.data.vat ?? 0;
                    this.dataForm.total_grand = this.data.total_grand ?? 0;
                    this.dataForm.paid_amount = this.data.paid_amount ?? 0;
                    this.dataForm.debt_amount = this.data.debt_amount ?? 0;
                    this.dataForm.note = this.data.note;

                    this.loading = false;
                } catch (e) {
                    this.loading = false;
                };
            }, 500);
            $("#issue_date").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
            });
        },
        onChangeReceiptFrom() {
            this.dataForm.invoice_ref_id = null;
            this.dataForm.invoice_number = null;
            this.dataTable = [];
            this.increateItemOneEmptyData();
            this.calculatorAmount();
        },
        selectInvoice() {
            var queueSearch = 500;
            SelectOption({
                title: "Select invoice",
                placeholder: "Search invoice number ...",
                onReady: (callback_data) => {
                    Axios({
                            url: `{{ route('admin-select-invoice') }}`,
                            method: 'GET'
                        })
                        .then(response => {
                            const data = response?.data?.data.map(item => {
                                return {
                                    _id: item.id,
                                    _title: item?.invoice_number ? item.invoice_number : "",
                                    _image: this.imageLogoSelectOption,
                                    _description: item?.purchase?.project?.name ?? '',
                                    ...item,
                                }
                            });
                            callback_data(data);
                        });
                },
                onSearch: (value, callback_data) => {
                    clearTimeout(queueSearch);
                    queueSearch = setTimeout(() => {
                        Axios({
                                url: `{{ route('admin-select-invoice') }}`,
                                params: {
                                    search: value
                                },
                                method: 'GET'
                            })
                            .then(response => {
                                const data = response?.data?.data.map(
                                    item => {
                                        return {
                                            _id: item.id,
                                            _title: item?.invoice_number ? item.invoice_number : "",
                                            _image: this.imageLogoSelectOption,
                                            _description: item?.purchase?.project?.name ?? '',
                                            ...item,
                                        }
                                    });
                                callback_data(data);
                            });
                    }, 500);
                },
                afterClose: (res) => {
                    if (res) {
                        this.dataForm.invoice_ref_id = res.id;
                        this.dataForm.invoice_number = res.invoice_number;
                    }
                }
            });
        },
        selectCreditNote() {
            var queueSearch = 500;
            SelectOption({
                title: "Select credit note",
                placeholder: "Search credit note number ...",
                onReady: (callback_data) => {
                    Axios({
                            url: `{{ route('admin-select-credit-note') }}`,
                            method: 'GET'
                        })
                        .then(response => {
                            const data = response?.data?.data.map(item => {
                                return {
                                    _id: item.id,
                                    _title: item?.credit_note_number ? item.credit_note_number : "",
                                    _image: this.imageLogoSelectOption,
                                    _description: item?.purchase?.project?.name ?? '',
                                    ...item,
                                }
                            });
                            callback_data(data);
                        });
                },
                onSearch: (value, callback_data) => {
                    clearTimeout(queueSearch);
                    queueSearch = setTimeout(() => {
                        Axios({
                                url: `{{ route('admin-select-credit-note') }}`,
                                params: {
                                    search: value
                                },
                                method: 'GET'
                            })
                            .then(response => {
                                const data = response?.data?.data.map(
                                    item => {
                                        return {
                                            _id: item.id,
                                            _title: item?.credit_note_number ? item.credit_note_number : "",
                                            _image: this.imageLogoSelectOption,
                                            _description: item?.purchase?.project?.name ?? '',
                                            ...item,
                                        }
                                    });
                                callback_data(data);
                            });
                    }, 500);
                },
                afterClose: (res) => {
                    if (res) {
                        this.dataForm.invoice_ref_id = res.id;
                        this.dataForm.invoice_number = res.credit_note_number;
                    }
                }
            });
        },
        async PickUpInvoice() {
            this.loading = true;
            let dataTimeOut = null;
            let dataForm = this.dataForm;
            this.invoice_number_err.empty = this.dataForm?.invoice_number ? false : true;
            clearTimeout(dataTimeOut);
            dataTimeOut = setTimeout(async () => {
                try {
                    let pickUpInvoiceUrl = `/admin/credit-note/pick-up-invoice/${this.dataForm?.invoice_number}`;
                    let pickUpCreditNoteUrl = `/admin/credit-note/pick-up-credit-note/${this.dataForm?.invoice_number}`;
                    await this.fetchData(this.dataForm.receipt_from == 'invoice' ? pickUpInvoiceUrl : pickUpCreditNoteUrl,
                        async (res) => {
                            this.data = res.data;
                            this.dataTable = [];
                            let dataSet = {
                                ...res?.data,
                                invoice_id: res?.data?.id ?? null,
                                receipt_from: this.dataForm?.receipt_from,
                                invoice_number: this.dataForm?.receipt_from == 'invoice' 
                                    ? res?.data?.invoice_number 
                                    : res?.data?.credit_note_number, 
                            };
                            this.dataForm = this.formSetValue(dataSet);
                            if (res.data) {
                                this.projectName = res?.data?.purchase?.project?.name;
                                this.purchase_type = res.data.purchase.type_id == 2 ? true : false;
                                this.invoice_number_err.incorrect = false;
                                await this.getServiceByType(res.data?.purchase?.type_id);
                                if (res.data?.invoice_detail?.length > 0) {
                                    res.data?.invoice_detail.forEach(val => {
                                        let item = {
                                            id: Number(moment().format(
                                                'YYYYMMDDHHmmss'
                                            )),
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
                                        this.dataTable.push(item);
                                    });
                                    this.dataTable[this.dataTable.length - 1].remove = false;
                                }
                                this.calculatorAmount();
                            } else {
                                this.invoice_number_err.incorrect = this.invoice_number_err.empty ? false : true;
                                this.increateItemOneEmptyData();
                            }
                            this.loading = false;
                        });
                } catch (e) {
                    this.loading = false;
                };
            }, 500);
        },
        formSetValue(data) {
            return {
                receipt_from: data?.receipt_from ?? 'invoice',
                invoice_ref_id: data?.invoice_id ?? null,
                invoice_number: data?.invoice_number ?? null,
                // credit_note_number: data?.credit_note_number ?? null,
                // po_id: data.po_id ?? null,
                // exchange_rate: data?.exchange_rate ?? data?.exchangeRateDefault?.rate,
                customer_id: data?.customer_id ?? null,
                // data_customer: data?.data_customer ?? null,
                // project_id: data?.purchase?.project_id ?? null,
                // issue_date: data?.issue_date ?? null,
                // period_start: data?.period_start ?? null,
                // period_end: data?.period_end ?? null,
                // pac_type: null,
                // length: null,
                // cores: null,
                type_id: data?.purchase?.type_id ?? null,
                // charge_type: data?.charge_type ?? null,
                // charge_number: data?.charge_number ?? null,
                // install_number: data?.install_number ?? null,
                // day_month: data?.day_month ?? null,
                // total_qty: data?.total_qty ?? 0,
                // total_price: data?.total_price ?? 0,
                // total_vat: data?.vat ?? 0,
                note: data?.note ?? null,
                remark: data?.remark ?? null,
                disable: false
            };
        },
        ResetPathInput() {
            this.loading = true;
            setTimeout(() => {
                //resertForm
                this.dataForm.receipt_from = null;
                this.dataForm.invoice_ref_id = null;
                this.dataForm.invoice_number = null;
                this.dataForm.customer_id = null;
                this.dataForm.type_id = null;
                this.dataForm.note = null;
                //resetTable
                this.dataTable = [];
                this.increateItemOneEmptyData();
                this.calculatorAmount();
                this.loading = false;
            }, 500);
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
        fetchSelectCustomer() {
            $('#customer_id').select2({
                placeholder: `Select customer...`,
                ajax: {
                    url: '{{ route('admin-select-customer') }}',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: (param) => {
                        return {
                            search: param.term
                        };
                    },
                    processResults: (data) => {
                        return {
                            results: $.map(data.data, (item) => {
                                return {
                                    text: item?.name_en ? item?.name_en : item?.name_kh,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            }).on('select2:open', (e) => {
                document.querySelector('.select2-search__field').focus();
            }).on('select2:close', async (eventClose) => {
                const _id = eventClose.target.value;
                this.dataForm.customer_id = _id;
            });
        },
        fetchSelectProject() {
            $('#project_id').select2({
                placeholder: `Select project...`,
                ajax: {
                    url: '{{ route('admin-select-project') }}',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: (param) => {
                        return {
                            search: param.term
                        };
                    },
                    processResults: (data) => {
                        return {
                            results: $.map(data.data, (item) => {
                                return {
                                    text: item?.name ?? item?.phone,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            }).on('select2:open', (e) => {
                document.querySelector('.select2-search__field').focus();
            }).on('select2:close', async (eventClose) => {
                const _id = eventClose.target.value;
                this.dataForm.project_id = _id;
            });
        },
        fetchSelectType() {
            $('#type_id').select2({
                placeholder: `Select service type...`,
                ajax: {
                    url: '{{ route('admin-select-type') }}',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: (param) => {
                        return {
                            search: param.term
                        };
                    },
                    processResults: (data) => {
                        return {
                            results: $.map(data.data, (item) => {
                                return {
                                    text: item?.name ? item?.name : '',
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            }).on('select2:open', (e) => {
                document.querySelector('.select2-search__field').focus();
            }).on('select2:close', async (eventClose) => {
                const _id = eventClose.target.value;
                this.dataForm.type_id = _id;
                this.getServiceByType(eventClose);
            });
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
        submitFrom() {
            this.dataError = [];
            this.checkValidation((valid) => {
                if (valid.length > 0) {
                    return true;
                } else {
                    this.checkValidAmount(res => {
                        if (res) {
                            this.inputTotalPriceErr = true;
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
                                        this.dataForm.disable = true;
                                        this.loading = true;
                                        let data = this.dataForm;
                                        data.issue_date = this.$refs.issue_date.value;
                                        data.status_type = "receipt";
                                        data.details = this.dataTable.length >
                                            0 ? JSON.stringify(this.dataTable) :
                                            [];
                                        let url = `/admin/receipt/save-new`;
                                        setTimeout(() => {
                                            Axios({
                                                url: url,
                                                method: 'POST',
                                                data: {
                                                    ...data
                                                }
                                            }).then((res) => {
                                                console.log(res);
                                                
                                                this.loading =
                                                    false;
                                                this.dataForm
                                                    .disable =
                                                    false;
                                                this
                                                    .dialogClose();
                                                this.$store
                                                    .create
                                                    .options
                                                    .afterClose(
                                                        res);
                                                        let status = res.data.status;
                                                    if (status == "success") {
                                                        Toast({
                                                            title: 'Receipt',
                                                            message: res.data.message,
                                                            status: 'success',
                                                            size: 'small',
                                                        });
                                                    }
                                            }).catch((e) => {
                                                console.log(e,
                                                    'u2424729472947'
                                                );
                                                this.dataError =
                                                    e.response
                                                    ?.data
                                                    .errors;
                                                this.loading =
                                                    false;
                                                this.dataForm
                                                    .disable =
                                                    false;
                                            }).finally(() => {
                                                this.loading =
                                                    false;
                                                this.dataForm
                                                    .disable =
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
            this.$store.create.active = false;
        },
        subTotalInput() {
            //this.dataForm.vat = (this.dataForm.total_price * 10 / 100).toFixed(2);
            this.dataForm.total_grand = (Number(this.dataForm.total_price) + Number(this.dataForm.vat)).toFixed(2);
            this.dataForm.debt_amount = (Number(this.dataForm.total_price) - Number(this.dataForm.total_grand)).toFixed(2);
            this.dataForm.paid_amount = this.dataForm.total_grand;
            this.checkValidAmount((res) => {
                this.inputTotalPriceErr = res;
            });

            this.checkValidPartial((res) => {
                this.inputTotalPartialErr = res;
            });
        },
        checkValidAmount(cb) {
            this.dataForm.sum_total > this.dataForm.total_price ? cb(false) : cb(true);
        },
        checkValidPartial(cb) {
            this.dataForm.total_price > this.dataForm.partial_payment ? cb(false) : cb(true);
        },
        numberFormat(num) {
            return new Intl.NumberFormat().format(num.toFixed(2));
        },
        checkValidation(callback) {
            let error = [];
            if (this.dataTable.length > 0) {
                this.dataTable.forEach(val => {
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

            this.dataTable.push(dataObject);
        },
        removeInput(index) {
            if (this.dataTable.length == 1) {
                this.dataTable = [];
                this.increateItemOneEmptyData();
            } else {
                this.dataTable.splice(index, 1);
            }
            this.dataTable[this.dataTable.length - 1].remove = false;
            this.calculatorAmount();
        },
        addItem(index) {
            this.checkValidation((res) => {
                if (res.length > 0) {
                    return false;
                } else {
                    this.dataTable[index].remove = true;
                    this.increateItemOneEmptyData();
                }
            });
        },
        async getServiceByType($event) {
            let id = $event?.target?.value ?? $event;
            await Axios.get(`/admin/purchase/type-service/${id?id:null}`).then(resp => {
                this.List_service_in_type = resp.data;
            });
            this.dataTable.map(item => {
                item.service_id.value = "";
                item.service_id.error = false;
            });
        },
        calculatorAmount(type = null) {
            this.dataForm.vat = 0;
            this.dataForm.total_price = 0;
            this.dataForm.total_qty = 0;
            this.dataTable.forEach(item => {
                let price = Number(item.price.value);
                let qty = Number(item.qty.value);
                let amount = this.numberRound(price * qty, 2);
                if (type == "amount") {
                    amount = item.amount.value;
                } else {
                    item.amount.value = amount;
                }
                this.dataForm.vat += Number(amount * 0.1);
                this.dataForm.total_price += Number(amount);
                this.dataForm.total_qty += qty;
            });
            this.dataForm.total_grand = this.numberRound(this.dataForm.vat + this.dataForm.total_price, 2);
            this.dataForm.debt_amount = (this.dataForm.total_price - this.dataForm.total_grand).toFixed(2);
            this.dataForm.sum_total = this.numberRound(this.dataForm.vat + this.dataForm.total_price, 2);
            this.dataForm.paid_amount = this.dataForm.total_grand;

            this.dataForm.total_price = this.numberRound(this.dataForm.total_price, 2);
            this.dataForm.total_qty = this.numberRound(this.dataForm.total_qty, 2);
            this.dataForm.vat = this.numberRound(this.dataForm.vat, 2);

            this.checkValidAmount((res) => {
                this.inputTotalPriceErr = res;
            });
        },
        inputChangeType(item, index, type) {
            this.calculatorAmount(type);
        },
    }));
</script>
<script>
    Alpine.store('create', {
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
    window.create = (options) => {
        Alpine.store('create', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('create').options,
                ...options
            }
        });
    };
</script>
