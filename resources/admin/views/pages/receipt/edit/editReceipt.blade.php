<template x-data="{}" x-if="$store.edit.active">
    <div class="dialog" x-data="xEdit" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.edit?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.edit?.options?.title"></h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#" x-show="!loading">
                        <div class="form-body">
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Receipt Number <span>*</span></label>
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
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Customer<span>*</span></label>
                                    <select id="customer_id" name="customer_id" x-init="fetchSelectCustomer()"
                                        :disabled="dataForm.disable">
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
                                            <div class="row table-row-10"><i class='bx bx-dots-horizontal-rounded'></i>
                                            </div>
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
                                                            <select name="service_id[]" x-model="item.service_id.value"
                                                                :class="item.service_id.error ? 'borderRed' : ''"
                                                                :disabled="dataForm.disable">
                                                                <option value="">Select service...</option>
                                                                <template x-for="value in List_service_in_type">
                                                                    <option :value="value.id"
                                                                        :selected="value.id == item.service_id.value ? true : false"
                                                                        x-text="value.name">
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
                                                                placeholder="amount ..."
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
                                                                @click="removeInput(item,index)"
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
                                <span>Update</span>
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
    Alpine.data('xEdit', () => ({
        loading: false,
        data: null,
        dataError: [],
        dataForm: {
            disable: false,
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
        inputTotalPriceErr: false,
        inputTotalPartialErr: false,
        List_service_in_type: [],
        receipt_detail_arr_id: [],
        dataTable: [{
            id: Number(moment().format('YYYYMMDDHHmmss')),
            receipt_detail_id: null,
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
        async init() {
            this.loading = true;
            await setTimeout(async () => {
                try {
                    this.data = this.$store.edit.options.data;
                    let detailInvoices = this.data?.invoices?.invoice_detail ?? [];
                    this.data.details = this.data?.receipt_detail?.length > 0 ? this.data?.receipt_detail : detailInvoices;
                    let typeId = this.data.status_type == "receipt" ? this.data?.type?.id :this.data?.invoices?.purchase?.type?.id;

                    //patchValue
                    this.dataForm.customer_id = this.data.customer_id;
                    this.dataForm.receipt_number = this.data.receipt_number;
                    this.dataForm.type_id = typeId;
                    this.dataForm.issue_date = this.data.issue_date;
                    this.dataForm.total_price = this.data.total_price;
                    this.dataForm.vat = this.data.vat ?? 0;
                    this.dataForm.total_grand = this.data.total_grand ?? 0;
                    this.dataForm.paid_amount = this.data.paid_amount ?? 0;
                    this.dataForm.debt_amount = this.data.debt_amount ?? 0;
                    this.dataForm.sum_total = this.numberRound(this.dataForm.vat + this.dataForm.total_price, 2);
                    this.dataForm.note = this.data.note;
                    this.dataForm.partial_payment = this.data.partial_payment;

                    //checkCurrentSelect2
                    //customer
                    let customerID = this.data?.customer?.id ?? '';
                    let customerName = this.data?.customer?.name_en ?? '';
                    this.appendSelect2HtmlCurrentSelect('customer_id', customerID,
                        customerName);
                    //serviceType

                    let typeName = this.data.status_type == "receipt" ? this.data?.type
                        ?.name : this.data?.invoices?.purchase?.type?.name;
                    this.appendSelect2HtmlCurrentSelect('type_id', typeId, typeName);

                    await this.getServiceByType(typeId);

                    if (this.data.details.length > 0) {
                        this.dataTable = [];
                        this.data.details.forEach(val => {
                            let item = {
                                id: Number(moment().format('YYYYMMDDHHmmss')),
                                receipt_detail_id: val.id,
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
                            this.dataTable.push(item);
                        });
                        this.dataTable[this.dataTable.length - 1].remove = false;
                        //this.calculatorAmount();
                    }
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
                                    message: "Are you sure want to update?",
                                    btnClose: "Close",
                                    btnSave: "Yes",
                                },
                                afterClosed: (result) => {
                                    if (result) {
                                        this.dataForm.disable = true;
                                        this.loading = true;
                                        let data = this.dataForm;
                                        data.issue_date = this.$refs.issue_date
                                            .value;
                                        data.status_type = this.data
                                            .status_type == "receipt" ?
                                            "receipt" : "invoice";
                                        data.details = this.dataTable.length >
                                            0 ? JSON.stringify(this.dataTable) :
                                            [];
                                        data.receipt_detail_arr_delete = this
                                            .receipt_detail_arr_id
                                        let url =
                                            `/admin/receipt/update/${this.data.id}`;
                                        setTimeout(() => {
                                            Axios({
                                                url: url,
                                                method: 'POST',
                                                data: {
                                                    ...data,
                                                    id: this
                                                        .data
                                                        .id,
                                                }
                                            }).then((res) => {
                                                this.loading =
                                                    false;
                                                this.dataForm
                                                    .disable =
                                                    false;
                                                this
                                                    .dialogClose();
                                                this.$store.edit
                                                    .options
                                                    .afterClose(
                                                        res);
                                            }).catch((e) => {
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
        submitFromOld() {
            this.dataError = [];
            this.checkValidAmount(res => {
                if (res) {
                    return false;
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
                                let url = `/admin/receipt/update/${this.data.id}`;
                                setTimeout(() => {
                                    Axios({
                                        url: url,
                                        method: 'POST',
                                        data: {
                                            ...data,
                                            id: this.data.id
                                        }
                                    }).then((res) => {
                                        this.loading = false;
                                        this.dataForm.disable = false;
                                        this.dialogClose();
                                        this.$store.edit.options
                                            .afterClose(res);
                                    }).catch((e) => {
                                        this.dataError = e.response
                                            ?.data.errors;
                                        this.loading = false;
                                        this.dataForm.disable = false;

                                    }).finally(() => {
                                        this.loading = false;
                                        this.dataForm.disable = false;
                                    });
                                }, 500);
                            }
                        }
                    });
                }
            });
        },
        dialogClose() {
            this.$store.edit.active = false;
        },
        // calculatorAmount() {
        //     this.dataForm.vat = (this.dataForm.total_price * 10 / 100).toFixed(2);
        //     this.dataForm.total_grand = (Number(this.dataForm.total_price) + Number(this.dataForm.vat))
        //         .toFixed(2);
        //     this.dataForm.debt_amount = (Number(this.dataForm.total_price) - Number(this.dataForm
        //         .total_grand)).toFixed(2);
        //     this.dataForm.paid_amount = this.dataForm.total_grand;
        //     this.checkValidAmount((res) => {
        //         this.inputTotalPriceErr = res;
        //     });
        // },
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
            this.dataForm.total_grand >= this.dataForm.total_price ? cb(false) : cb(true);
        },
        checkValidPartial(cb) {
            this.dataForm.total_price >= this.dataForm.partial_payment ? cb(false) : cb(true);
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
                receipt_detail_id: null,
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
        removeInput(item, index) {
            if (this.dataTable.length == 1) {
                this.dataTable = [];
                this.increateItemOneEmptyData();
            } else {
                this.dataTable.splice(index, 1);
            }
            this.dataTable[this.dataTable.length - 1].remove = false;
            if (item?.receipt_detail_id) {
                this.receipt_detail_arr_id.push(item.receipt_detail_id);
            }
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
        appendSelect2HtmlCurrentSelect(select2ID, id, name) {
            var option = "<option selected></option>";
            var optionHTML = $(option).val(id ? id : null).text(name ? name : name);
            $(`#${select2ID}`).append(optionHTML).trigger('change');
        }
    }));
</script>
<script>
    Alpine.store('edit', {
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
    window.edit = (options) => {
        Alpine.store('edit', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('edit').options,
                ...options
            }
        });
    };
</script>
