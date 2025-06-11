<template x-data="{}" x-if="$store.insertCreditNote.active">
    <div class="dialog" x-data="xInsertCreditNote" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3><span x-text="$store.insertCreditNote.options.title"></span> Credit Note</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row">
                                <template x-if="!$store.insertCreditNote.options.data">
                                    <div class="form-row">
                                        <label>Ref invoice Number <span>*</span></label>
                                        <div class="pickup_invoice"
                                            :class="invoice_number_err.incorrect || invoice_number_err.empty ? 'borderRed' : ''">
                                            <input type="text" placeholder="Enter ref invoice number ..."
                                                x-model="formSubmitData.invoice_number" @click="selectInvoice()"
                                                readonly />
                                            <button type="button" @click="PickUpInvoice()">Pick up invoice</button>
                                            <template x-if="formSubmitData.invoice_number">
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

                                <template x-if="$store.insertCreditNote.options.data">
                                    <div class="form-row">
                                        <div class="pickup_invoice">
                                            <span class="pickUpReadOnly" x-text="formSubmitData.invoice_number"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Credit Note Number<span>*</span></label>
                                    <input type="text" name="credit_note_number"
                                        x-model="formSubmitData.credit_note_number" min="0"
                                        placeholder="Enter credit note number" :disabled="formDisable">
                                    <template x-for="item in dataError?.credit_note_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Exchange rate<span>*</span></label>
                                    <input type="number" name="exchange_rate" x-model="formSubmitData.exchange_rate"
                                        @input="calcuatorAmount" min="0" placeholder="Enter exchange rate"
                                        :disabled="formDisable">
                                    <template x-for="item in dataError?.exchange_rate">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Issue Date<span>*</span> </label>
                                    <input type="text" name="issue_date" id="issue_date"
                                        x-model="formSubmitData.issue_date" x-ref="issue_date" autocomplete="off"
                                        readonly placeholder="Select issue date" :disabled="formDisable">
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
                                    <input type="text" name="period_start" id="period_start"
                                        x-model="formSubmitData.period_start" x-ref="period_start" autocomplete="off"
                                        placeholder="Select start period date" readonly :disabled="formDisable">
                                    <template x-for="item in dataError?.period_start">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>End Period Date</label>
                                    <input type="text" name="period_end" id="period_end"
                                        x-model="formSubmitData.period_end" x-ref="period_end" autocomplete="off"
                                        placeholder="Select start period date" readonly :disabled="formDisable">
                                    <template x-for="item in dataError?.period_end">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- New --}}
                            <div class="row" style="margin-top:20px;">
                                <div class="table customTable">
                                    <div class="table-wrapper purchaseInvoice">
                                        {{-- header --}}
                                        <div class="table-header">
                                            <div class="row table-row-5">
                                                <span class="font13">ល.រ</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span class="font13">ប្រភេទ</span>
                                            </div>
                                            <div class="row table-row-25 text-start">
                                                <span class="font13">បរិយាយមុខទំនិញ</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span class="font13">បរិមាណ</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span class="font13">ឯកតា</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span class="font13">ថ្លៃឯកតា($)</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span class="font13">ថ្លៃទំនិញ($)</span>
                                            </div>
                                            <div class="row table-row-10 text-end">
                                                <span class="font13">Action</span>
                                            </div>
                                        </div>
                                        <div class="table-header">
                                            <div class="row table-row-5">
                                                <span>No</span>
                                            </div>
                                            <div class="row table-row-15">
                                                <span>Item</span>
                                            </div>
                                            <div class="row table-row-25 text-start">
                                                <span>Description</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>Quality</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>UOM</span>
                                            </div>
                                            <div class="row table-row-10 text-start">
                                                <span>Unit Price($)</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span>Amount($)</span>
                                            </div>
                                            <div class="row table-row-10 text-end">
                                                <span>Action</span>
                                            </div>
                                        </div>

                                        <div class="table-body">

                                            {{-- projectName --}}
                                            <div class="column">
                                                <div class="row table-row-5"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-25 text-start">
                                                    <span class="label" x-text="projectName ?? '---'"></span>
                                                </div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-10"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-10"></div>
                                            </div>

                                            {{-- body --}}
                                            <template x-for="(item,index) in dataForm">
                                                <div class="column">
                                                    <div class="row table-row-5">
                                                        <span x-text="index+1"></span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span>
                                                            <select name="service_id[]"
                                                                x-model="item.service_id.value"
                                                                :class="item.service_id.error ? 'borderRed' : ''"
                                                                :disabled="formDisable">
                                                                <option value="">Select service...</option>
                                                                <template x-for="value in ftthServices">
                                                                    <option :value="value.id" x-text="value.name"
                                                                        :selected="item.service_id.value == value.id ? true : false">
                                                                    </option>
                                                                </template>
                                                            </select>
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-25 text-start">
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
                                                    <div class="row table-row-10">
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
                                                    <div class="row table-row-10">
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
                                                        <textarea type="text" x-model="formSubmitData.remark"></textarea>
                                                    </div>
                                                    <div class="font13">
                                                        <span>Note: Ref.NBC Exchang Rate 1 USD = </span>
                                                        <span x-text="formSubmitData.exchange_rate"></span>
                                                        <span>Riel</span>
                                                    </div>
                                                    <div class="inputTextArea">
                                                        <label class="font13">Amount in Word (English & Khmer)<span>*</span></label>
                                                        <textarea class="font13" x-model="formSubmitData.note"></textarea>
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
                                                            <template x-if="!checkEdit">
                                                                <div class="divTag font13"
                                                                    x-text="numberFormat(vat.dollar)">
                                                                </div>
                                                            </template>
                                                            <template x-if="checkEdit">
                                                                <div class="divTag font13">
                                                                    <input type="number" min="0.01"
                                                                        step="0.01" x-model="vat.dollar"
                                                                        @input.debounce.500="amountCalculateVat($el)">
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13" x-text="numberFormat(vat.khmer)"></div>
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
                                                            <div class="divTag font13" x-text="numberFormat(grand_total.dollar)"></div>
                                                        </div>
                                                        <div class="row table-row-37">
                                                            <div class="divTag font13" x-text="numberFormat(grand_total.khmer)"></div>
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
                            <button type="button" class="close" @click="dialogClose()" :disabled="formDisable">
                                <i class='bx bx-x'></i>
                                <span>Close</span>
                            </button>
                            <button type="button" class="primary" color="primary" @click="submitForm()"
                                :disabled="formDisable">
                                <i class='bx bx-save'></i>
                                <span>Save & Submit</span>
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
    Alpine.data('xInsertCreditNote', () => ({
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
        tax_status: null,
        ftthServices: [],
        current_date: null,
        exchange_rate: 0,
        total_qty: 0,
        dataError: [],
        data: null,
        formDisable: false,
        invoice_number_err: {
            empty: false,
            incorrect: false
        },
        projectName: null,
        formSubmitData: {
            invoice_ref_id: null,
            invoice_number: null,
            credit_note_number: null,
            exchange_rate: null,
            customer_id: null,
            project_id: null,
            issue_date: null,
            period_start: null,
            period_end: null,
            dataTable: [],
            total_qty: 0,
            total_price: 0,
            total_vat: 0,
            note: null,
            remark:null,
            disable: false
        },
        dataForm: [{
            id: null,
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
            amount: {
                value: "",
                error: false
            },
            remove: false
        }],
        checkEdit: false,
        dataInvoice: null,
        imageLogoSelectOption: "data:image/png;base64,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",
        async init() {
            this.submitLoading = true;
            let dataStore = this.$store.insertCreditNote.options.data;
            this.checkEdit = dataStore ? true : false;
            this.current_date = moment(new Date).format('DD MMM YYYY');
            this.fetchFTTHService();
            if (dataStore) {
                dataStore.dataCustomer = dataStore?.data_customer ? this.jsonPase(dataStore.data_customer) : null;
                this.projectName = dataStore?.order?.project?.name;
                this.formSubmitData = this.formSetValue(dataStore);
                this.exchange_rate = this.formSubmitData?.exchange_rate ?? 0;
                if (dataStore?.credit_note_details?.length > 0) {
                    this.dataForm = [];
                    dataStore?.credit_note_details.forEach(val => {
                        let item = {
                            id: val.id,
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
                            amount: {
                                value: val.amount,
                                error: false
                            },
                            remove: true
                        };
                        this.dataForm.push(item);
                    });
                    this.dataForm[this.dataForm.length - 1].remove = false;
                    // this.calcuatorAmount();
                    this.amountCalculateWhenEdit(dataStore)
                }
            }
            await setTimeout(async () => {
                try {
                    this.submitLoading = false;
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
        jsonPase(data) {
            return JSON.parse(data);
        },
        selectInvoice() {
            var queueSearch = 500;
            SelectOption({
                title: "Select invoice",
                placeholder: "Search invoice number ...",
                onReady: (callback_data) => {
                    Axios({
                            url: `{{ route('admin-work-order-credit-note-select-invoice') }}`,
                            method: 'GET'
                        })
                        .then(response => {
                            const data = response?.data?.data.map(item => {
                                return {
                                    _id: item.id,
                                    _title: item?.invoice_number ? item.invoice_number : "",
                                    _image: this.imageLogoSelectOption,
                                    _description: item?.order?.project?.name ?? '',
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
                                url: `{{ route('admin-work-order-credit-note-select-invoice') }}`,
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
                                            _description: item?.order?.project?.name ?? '',
                                            ...item,
                                        }
                                    });
                                callback_data(data);
                            });
                    }, 500);
                },
                afterClose: (res) => {
                    if (res) {
                        this.formSubmitData.invoice_ref_id = res.id;
                        this.formSubmitData.invoice_number = res.invoice_number;
                    }
                }
            });
        },
        async fetchFTTHService() {
            await Axios.get(`/admin/work-order/invoice/fetch-ftth-service`).then(res => {
                this.ftthServices = res.data;
            });
        },
        async PickUpInvoice() {
            this.submitLoading = true;
            let dataTimeOut = null;
            let dataForm = this.formSubmitData;
            this.invoice_number_err.empty = this.formSubmitData?.invoice_number ? false : true;
            clearTimeout(dataTimeOut);
            dataTimeOut = setTimeout(async () => {
                try {
                    await this.fetchData(`/admin/work-order/credit-note/pick-up-invoice/${this.formSubmitData?.invoice_number}`,
                        async (res) => {
                            this.data = res;
                            this.tax_status = res.data.tax_status;
                            this.dataForm = [];
                            let dataSet = {
                                ...res?.data,
                                invoice_id: res?.data?.id ?? null
                            };
                            this.formSubmitData = this.formSetValue(dataSet);
                            if (res.data) {
                                this.projectName = res?.data?.order?.project?.name;
                                this.invoice_number_err.incorrect = false;
                                this.exchange_rate = res?.data?.exchange_rate ?? res.data?.exchangeRateDefault?.rate;
                                await this.fetchFTTHService(); 
                                if (res.data?.invoice_detail?.length > 0) {
                                    res.data?.invoice_detail.forEach(val => {
                                        let item = {
                                            id: null,
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
                                // this.calcuatorAmount();
                                this.amountCalculateWhenEdit(res.data)
                            } else {
                                this.invoice_number_err.incorrect = this.invoice_number_err.empty ? false : true;
                                this.increateItemOneEmptyData();
                            }
                            this.submitLoading = false;
                        });
                } catch (e) {
                    this.submitLoading = false;
                };
            }, 500);
        },
        formSetValue(data) {
            return {
                invoice_id: data?.invoice_id ?? null,
                invoice_number: data?.invoice_number ?? this.formSubmitData.invoice_number,
                credit_note_number: data?.credit_note_number ?? null,
                order_id: data?.order_id ?? null,
                exchange_rate: data?.exchange_rate ?? data?.exchangeRateDefault?.rate,
                customer_id: data?.customer_id ?? null,
                data_customer: data?.data_customer ?? null,
                issue_date: data?.issue_date ?? null,
                period_start: data?.period_start ?? null,
                period_end: data?.period_end ?? null,
                total_qty: data?.total_qty ?? 0,
                total_price: data?.total_price ?? 0,
                total_vat: data?.vat ?? 0,
                note: data?.note ?? null,
                remark: data?.remark ?? null,
                disable: false
            };
        },
        ResetPathInput() {
            this.loading = true;
            setTimeout(() => {
                //resertForm
                this.formSubmitData.invoice_ref_id = null;
                this.formSubmitData.invoice_number = null;
                this.formSubmitData.credit_note_number = null;
                this.formSubmitData.issue_date = null;
                this.formSubmitData.period_start = null;
                this.formSubmitData.period_end = null;
                this.formSubmitData.total_qty = 0;
                this.formSubmitData.total_price = 0;
                this.formSubmitData.total_vat = 0;
                this.formSubmitData.note = null;
                this.formSubmitData.remark = null;
                //resetTable
                this.dataTable = [];
                this.increateItemOneEmptyData();
                this.calcuatorAmount();
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
            this.vat.dollar = 0;
            this.vat.khmer = 0;
            this.total_qty = 0;
            let exchangeRate = this.exchange_rate ? parseFloat(this.exchange_rate) : 0;
            this.dataForm.forEach((item) => {
                let qty = parseFloat(item.qty.value ? item.qty.value : 0);
                let price = parseFloat(item.price.value ? item.price.value : 0);

                let amount = this.numberRound(price * qty, 2);

                    item.amount.value = amount;
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
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * exchangeRate);
            if (this.tax_status != 2) {
                this.sub_total.khmer = this.numberRound((this.grand_total.khmer / 1.1));
                this.vat.khmer = this.numberRound(this.grand_total.khmer - this.sub_total.khmer);
            } else {
                this.sub_total.khmer = this.grand_total.khmer;
            }
        },

        amountCalculateVat(el) {
            let exchangeRate = this.exchange_rate ? parseFloat(this.exchange_rate) : 0;
            let vatDollar = el.value;
            this.vat.khmer = this.numberRound(Number(vatDollar) * Number(exchangeRate));
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(vatDollar),
                2);
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(
                exchangeRate));
        },
        amountCalculateWhenEdit(data) {
            let exchangeRate = this.exchange_rate ? parseFloat(this.exchange_rate) : 0;
            //dollar
            this.sub_total.dollar = this.numberRound(data.total_price, 2);
            this.vat.dollar = this.numberRound(data.vat, 2);
            this.grand_total.dollar = this.numberRound(this.sub_total.dollar + this.vat.dollar, 2);
            //khmer
            this.grand_total.khmer = this.numberRound(this.grand_total.dollar * exchangeRate);
            this.sub_total.khmer = this.numberRound(this.grand_total.khmer / 1.1);
            this.vat.khmer = this.numberRound(this.grand_total.khmer - this.sub_total.khmer);

            if (this.numberRound((this.sub_total.dollar * (10 / 100)),2) != this.vat.dollar) {
                this.sub_total.khmer = this.numberRound(this.sub_total.dollar * exchangeRate);
                this.vat.khmer = this.numberRound(this.vat.dollar * exchangeRate);
            }
        },
        submitForm() {
            let dataStore = this.$store.insertCreditNote.options.data;
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
                                this.formDisable = true;
                                this.submitLoading = true;
                                let issue_date = this.$refs?.issue_date?.value ?? "";
                                let period_start = this.$refs?.period_start.value;
                                let period_end = this.$refs?.period_end.value;
                                let data = this.formSubmitData ?? {};

                                let dataItem = {
                                    credit_note_number: data.credit_note_number,
                                    invoice_id: data.invoice_id,
                                    invoice_number: data.invoice_number,
                                    order_id: data.order_id,
                                    customer_id: data.customer_id,
                                    data_customer: data?.data_customer ?? null,
                                    total_price: this.sub_total.dollar,
                                    vat: this.vat.dollar,
                                    total_grand: this.grand_total.dollar,
                                    total_qty: this.total_qty,
                                    issue_date: issue_date,
                                    exchange_rate: data.exchange_rate,
                                    period_start: period_start,
                                    period_end: period_end,
                                    note: data.note,
                                    remark: data?.remark,
                                    status: 1,
                                    credit_note_details: this.dataForm.length > 0 ? JSON.stringify(this.dataForm) : [],
                                };
                                setTimeout(() => {
                                    Axios({
                                        url: `{{ route('admin-work-order-credit-note-save') }}`,
                                        method: 'POST',
                                        data: {
                                            ...dataItem,
                                            id: dataStore?.id,
                                        }
                                    }).then((res) => {
                                        this.submitLoading = false;
                                        this.formDisable = false;
                                        this.dialogClose();
                                        reloadUrl("{!! url()->current() !!}");
                                    }).catch((e) => {
                                        this.dataError = e.response?.data.errors;
                                        this.submitLoading = false;
                                        this.formDisable = false;

                                    }).finally(() => {
                                        this.submitLoading = false;
                                        this.formDisable = false;
                                    });
                                }, 500);
                            }
                        }
                    });
                }
            });
        },
        dialogClose() {
            this.$store.insertCreditNote.active = false;
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
                id: null,
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
                amount: {
                    value: "",
                    error: false
                },
                remove: false
            };
            this.dataForm.push(dataObject);
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
    Alpine.store('insertCreditNote', {
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
    window.insertCreditNote = (options) => {
        Alpine.store('insertCreditNote', {
            active: true,
            options: {
                ...Alpine.store('insertCreditNote').options,
                ...options
            }
        });
    };
</script>
