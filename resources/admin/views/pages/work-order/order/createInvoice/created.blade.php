<template x-data="{}" x-if="$store.invoiceCreate.active">
    <div class="dialog" x-data="xInvoiceCreate" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Create Invoice</h3>
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
                                    <input type="text" name="issue_date" id="issue_date" x-ref="issue_date"
                                        autocomplete="off" readonly placeholder="Select issue date">
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
                                    <input type="text" name="period_start" id="period_start" x-ref="period_start"
                                        autocomplete="off" placeholder="Select start period date" readonly>
                                    <template x-for="item in dataError?.period_start">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>End Period Date</label>
                                    <input type="text" name="period_end" id="period_end" x-ref="period_end"
                                        autocomplete="off" placeholder="Select start period date" readonly>
                                    <template x-for="item in dataError?.period_end">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
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
                                            <div class="row table-row-15 text-start">
                                                <span class="font13">បរិមាណ</span>
                                            </div>
                                            <div class="row table-row-15 text-start">
                                                <span class="font13">ឯកតា</span>
                                            </div>
                                            <div class="row table-row-15 text-start">
                                                <span class="font13">ថ្លៃឯកតា($)</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span class="font13">ថ្លៃទំនិញ($)</span>
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
                                            <div class="row table-row-15 text-start">
                                                <span>Quality</span>
                                            </div>
                                            <div class="row table-row-15 text-start">
                                                <span>UOM</span>
                                            </div>
                                            <div class="row table-row-15 text-start">
                                                <span>Unit Price($)</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span>Amount($)</span>
                                            </div>
                                        </div>

                                        <div class="table-body">

                                            {{-- projectName --}}
                                            <div class="column">
                                                <div class="row table-row-5"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-25 text-start">
                                                    <span class="label" x-text="data?.order?.project?.name ?? '---'"></span>
                                                </div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-15"></div>
                                                <div class="row table-row-15"></div>
                                            </div>

                                            {{-- body --}}
                                            <template x-for="(item,index) in list_order_details">
                                                <div class="column font13">
                                                    <div class="row table-row-5">
                                                        <span x-text="index+1"></span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span x-text="item.service?.name ?? '-'" class="bgDisable"></span>
                                                        <input type="hidden" :value="item.service_id"
                                                            name="service_id[]" />
                                                    </div>
                                                    <div class="row table-row-25 text-start">
                                                        <span>
                                                            <textarea x-model="item.des"></textarea>
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span>
                                                            <input type="number" x-model="item.qty"
                                                                placeholder="qty ..." class="input-table"
                                                                min="0" step="any"
                                                                @input="inputChangeType(item,index,'qty')" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span>
                                                            <input type="text" x-model="item.uom" name="uom[]"
                                                                placeholder="uom ..." class="input-table" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span>
                                                            <input type="number" x-model="item.price" name="price[]"
                                                                placeholder="price ..." class="input-table"
                                                                min="0" step="any"
                                                                @input="inputChangeType(item,index,'price')" />
                                                        </span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span>
                                                            <input type="number" name="amount[]"
                                                                placeholder="amount ..." class="input-table"
                                                                x-model="item.amount"
                                                                @input="inputChangeType(item,index,'amount')"
                                                                min="0.01" step="0.01" />
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
                                                            <p>Amount in Word (English & Khmer)</p>
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
                                                        <div class="row table-row-50">
                                                            <div class="div font13">
                                                                <div>សរុប</div>
                                                                <div>Sub total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-33">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(sub_total.dollar)"></div>
                                                        </div>
                                                        <div class="row table-row-33">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(sub_total.khmer)"></div>
                                                        </div>
                                                    </div>
                                                    <div class="column">
                                                        <div class="row table-row-50">
                                                            <div class="div font13">
                                                                <div>អាករលើតម្លៃបន្ថែម១០%</div>
                                                                <div>VAT10%</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-33">
                                                            <div class="divTag font13" x-text="numberFormat(vat.dollar)">
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-33">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(vat.khmer)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="column">
                                                        <div class="row table-row-50">
                                                            <div class="div font13">
                                                                <div>សរុបរួម</div>
                                                                <div>Grand Total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-33">
                                                            <div class="divTag font13"
                                                                x-text="numberFormat(grand_total.dollar)"></div>
                                                        </div>
                                                        <div class="row table-row-33">
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
    Alpine.data('xInvoiceCreate', () => ({
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
        list_order_details: [],
        current_date: null,
        numberDay_of_month: 0,
        exchang_rate: 0,
        total_qty: 0,
        invoice_number: null,
        note: null,
        remark: null,
        dataError: [],
        data: null,
        tax_status: 1,
        taxOptions: @json(config('dummy.tax_status')),
        async init() {
            this.submitLoading = true;
            let dataStore = this.$store.invoiceCreate.options.data;
            this.current_date = moment(new Date).format('DD MMM YYYY');
            this.numberDay_of_month = moment(new Date).daysInMonth();

            setTimeout(async () => {
                try {
                    let url = `{!! route('admin-order-createInvoice', ['id' => ':id']) !!}`;
                    await this.fetchData(url.replace(':id', dataStore.id), (
                        res) => {
                        this.data = res;
                        this.exchang_rate = res.rate?.rate ?? 0;
                        this.list_order_details = res.order?.order_detail ?? [];
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
            this.list_order_details.forEach((item) => {
                let qty = Number(item.qty);
                this.sub_total.dollar += Number(item.amount);
                this.total_qty += item.qty;
            });

                 //dollar
                 this.sub_total.dollar = this.numberRound(this.sub_total.dollar, 2);
            if (this.tax_status != 2) {
                this.vat.dollar = this.numberRound(Number(this.sub_total.dollar * (10 / 100)), 2);
            }
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(this.vat
                .dollar), 2);

            //khmer
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this
                .exchang_rate));
          
            if (this.tax_status != 2) {
                this.sub_total.khmer = this.numberRound(this.grand_total.khmer / 1.1);
                this.vat.khmer = this.numberRound(this.grand_total.khmer - this.sub_total.khmer);
              
            }else{
                this.sub_total.khmer = this.grand_total.khmer;
            }

        },
        amountCalculateVat(el) {
            let vatDollar = el.value;
            this.vat.khmer = this.numberRound(Number(vatDollar) * Number(this.exchang_rate));
            this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(vatDollar), 2);
            this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this.exchang_rate));
        },
        submitFrom() {
            let dataStore = this.$store.invoiceCreate.options.data;
            this.dataError = [];
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
                            order_id: dataStore.id,
                            customer_id: dataStore.customer_id,
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
                            remark: this.remark,
                            status: 1,
                            day_month: this.day_month,
                            order_details: this.list_order_details.length ? JSON.stringify(this.list_order_details) : [],
                            tax_status: this.tax_status,
                        };
                        setTimeout(async () => {
                            await Axios({
                                url: `{{ route('admin-order-save-invoice') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: null,
                                    deleteItemID: [],
                                }
                            }).then(async (res) => {
                                let message = res.data.message;
                                if (message == "success") {
                                    this.dialogClose();
                                    Toast({
                                        title: 'Create invoice',
                                        message: 'success',
                                        status: 'success',
                                        size: 'small',
                                    });
                                } else if (message == "dateValid") {
                                    let dateValidErr = moment(
                                        issue_date,
                                        'YYYY MM DD').format(
                                        'YYYY MM');
                                    this.$store.DMCSubmitStatusDialog
                                        .open({
                                            data: {
                                                title: "Order invoice",
                                                message: `Invoice create isset date <b>${dateValidErr}</b> will disable !`,
                                                btnClose: "Close",
                                                btnSave: "Yes",
                                            }
                                        });
                                }
                            }).catch((e) => {
                                this.dataError = e.response?.data.errors;
                                this.submitLoading = false;
                            }).finally(() => {
                                this.submitLoading = false;
                            });
                        }, 500);
                    }
                }
            });
        },
        dialogClose() {
            this.$store.invoiceCreate.active = false;
        },
        jsonParse(data) {
            return JSON.parse(data);
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
            return date ? moment(date).format('DD MMM YYYY') : ' ';
        },
        dateFormatEn(date, type) {
            return date ? moment(date).format(type) : "";
        },
    }));
</script>
<script>
    Alpine.store('invoiceCreate', {
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
        },
        afterClose: () => {},
    });
    window.invoiceCreate = (options) => {
        Alpine.store('invoiceCreate', {
            active: true,
            options: {
                ...Alpine.store('invoiceCreate').options,
                ...options
            },
            afterClose: (res) => {
                console.log('close');
            },
        });
    };
</script>
