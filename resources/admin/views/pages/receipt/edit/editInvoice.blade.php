<template x-data="{}" x-if="$store.editReceiptInvoice.active">
    <div class="dialog" x-data="xEditReceiptInvoice" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.editReceiptInvoice?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.editReceiptInvoice?.options?.title"></h3>
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
                                            <div class="row table-row-15 text-start">
                                                <span>UNIT PRICE</span>
                                            </div>
                                            <div class="row table-row-15 text-end">
                                                <span>AMOUNT</span>
                                            </div>
                                        </div>

                                        <div class="table-body">
                                            {{-- body --}}
                                            <template x-for="(item,index) in data?.invoices?.invoice_detail">
                                                <div class="column font13">
                                                    <div class="row table-row-5">
                                                        <span class="text-center" x-text="index+1"></span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <span class="text-center"
                                                            x-text="item?.service?.name??''"></span>
                                                    </div>
                                                    <div class="row table-row-30 text-start">
                                                        <span x-text="item?.des??''"></span>
                                                    </div>
                                                    <div class="row table-row-10">
                                                        <span class="text-center" x-text="item?.qty??''"></span>
                                                    </div>
                                                    <div class="row table-row-10">
                                                        <span class="text-center" x-text="item?.uom??''"></span>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <div class="divTag text-start">
                                                            <span>$</span>
                                                            <span
                                                                x-text="item?.price?numberRound(item?.price,2):''"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row table-row-15">
                                                        <div class="divTag text-start">
                                                            <span>$</span>
                                                            <span
                                                                x-text="item?.amount?numberRound(item?.amount,2):''"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- footer --}}
                                            <div class="column footerColumnReceipt">
                                                <div class="row table-row-70 left">
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

                                                <div class="row table-row-30 right">
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-50">
                                                            <div class="div font13 bgGray">
                                                                <div>Sub total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-50">
                                                            <div class="divTag font13">$</div>
                                                            <div class="divTag font13">
                                                                <input type="number" x-model="dataForm.total_price"
                                                                    placeholder="sub total ..."
                                                                    :disabled="dataForm.disable" min="0"
                                                                    step="any" @input="calculatorAmount()"
                                                                    class="height30"
                                                                    :class="inputTotalPriceErr ? 'err' : ''"
                                                                    style="padding: 5px 0;" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-50">
                                                            <div class="div font13 bgGray">
                                                                <div>VAT10%</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-50">
                                                            <div class="divTag font13">$</div>
                                                            {{-- <div class="divTag font13" x-text="dataForm?.vat?numberRound(dataForm?.vat,2):''"></div> --}}
                                                            <div class="divTag font13">
                                                                <input type="number" x-model="dataForm.vat"
                                                                    placeholder="0" :disabled="dataForm.disable" 
                                                                    min="0"
                                                                    step="any" @input="calculatorAmount()"
                                                                    class="height30"
                                                                    style="padding: 5px 0;" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-50">
                                                            <div class="div font13 bgGray">
                                                                <div>Grand Total</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-50">
                                                            <div class="divTag font13">$</div>
                                                            <div class="divTag font13"
                                                                x-text="dataForm?.total_grand?numberRound(dataForm?.total_grand,2):''">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="column" style="height: 25% !important;">
                                                        <div class="row table-row-50">
                                                            <div class="div font13 bgGray">
                                                                <div>Portail Payment</div>
                                                            </div>
                                                        </div>
                                                        <div class="row table-row-50">
                                                            <div class="divTag font13">$</div>
                                                            <div class="divTag font13">
                                                                <input type="number" x-model="dataForm.partial_payment"
                                                                    placeholder="partial payment ..."
                                                                    :disabled="dataForm.disable" min="0"
                                                                    step="any" @input="calculatorAmount()"
                                                                    class="height30"
                                                                    :class="inputTotalPartialErr ? 'err' : ''"
                                                                    style="padding: 5px 0;" />
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
                        <div class="form-footer">
                            <button type="button" class="primary" color="primary" @click="submitFrom()"
                                :disabled="dataForm.disable || inputTotalPriceErr">
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
    Alpine.data('xEditReceiptInvoice', () => ({
        loading: false,
        data: null,
        dataError: [],
        dataForm: {
            disable: false,
            receipt_number: null,
            customer_id: null,
            type_id: null,
            note: null,
            issue_date: null,
            vat: null,
            total_price: null,
            paid_amount: null,
            debt_amount: null,
            total_grand: null,
            partial_payment: null,
        },
        inputTotalPriceErr: false,
        inputTotalPartialErr: false,
        async init() {
            this.loading = true;
            await setTimeout(async () => {
                try {
                    this.data = this.$store.editReceiptInvoice.options.data;
                    //patchValue
                    this.dataForm.receipt_number = this.data.receipt_number;
                    this.dataForm.customer_id = this.data.customer_id;
                    this.dataForm.type_id = this.data?.invoices?.purchase?.type_id;
                    this.dataForm.issue_date = this.data.issue_date;
                    this.dataForm.total_price = this.data.total_price;
                    this.dataForm.vat = this.data?.vat ? this.numberRound(this.data?.vat, 2) : 0;
                    this.dataForm.total_grand = this.data.total_grand;
                    this.dataForm.paid_amount = this.data.paid_amount;
                    this.dataForm.debt_amount = this.data.debt_amount;
                    this.dataForm.partial_payment = this.data.partial_payment;
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
            // this.sub_total.dollar = 0;
            // this.sub_total.khmer = 0;
            // this.vat.dollar = 0;
            // this.vat.khmer = 0;
            // this.total_qty = 0;
            // this.dataForm.forEach((item) => {
            //     let qty = parseFloat(item.qty.value ? item.qty.value : 0);
            //     let price = parseFloat(item.price.value ? item.price.value : 0);
            //     let rate_first = parseFloat(item.rate_first.value ? item.rate_first.value : 0);
            //     let rate_second = parseFloat(item.rate_second.value ? item.rate_second.value : 0);

            //     if (type != "amount") {
            //         this.rate_calculate = rate_first > 0 ? (rate_first / 100) : 1;
            //         this.second_rate_calculate = rate_second > 0 ? (rate_second / 100) : 1;
            //         this.numDay = this.day_month > 0 ? this.day_month : this.numberDay_of_month;

            //         if (this.charge_type == 'day') {
            //             item.amount.value = this.numberRound((qty * price * 0) + (price * qty / this
            //                 .numDay * this.charge_number), 2);
            //         } else if (this.charge_type == 'quarter') {
            //             if (this.charge_number == null) {
            //                 this.charge_number = 4;
            //             }
            //             item.amount.value = this.numberRound((price * qty * this.rate_calculate *
            //                 this.second_rate_calculate) / this.charge_number, 2);
            //         } else if (this.charge_type == 'month' && this.charge_number) {
            //             // if number of charge over than 1 year 
            //             item.amount.value = this.numberRound(price * qty * this.rate_calculate *
            //                 this.second_rate_calculate / 12 * this.charge_number, 2);
            //         } else if (this.charge_type == 'month' && !this.charge_number) {
            //             item.amount.value = this.numberRound(price * qty * this
            //                 .rate_calculate * this.second_rate_calculate / 12, 2);
            //         } else {
            //             if (this.charge_number) {
            //                 item.amount.value = this.numberRound(price * qty * this
            //                     .charge_number, 2);
            //             } else {
            //                 item.amount.value = this.numberRound(price * qty, 2);
            //             }
            //         }

            //     }
            //     let amount = parseFloat(item.amount.value ? item.amount.value : 0);
            //     this.sub_total.dollar += amount;
            //     this.total_qty += qty;
            // });

            // //dollar
            // this.sub_total.dollar = this.numberRound(this.sub_total.dollar, 2);
            // this.vat.dollar = this.numberRound(Number(this.sub_total.dollar * (10 / 100)), 2);
            // this.grand_total.dollar = this.numberRound(Number(this.sub_total.dollar) + Number(this.vat
            //     .dollar), 2);

            // //khmer
            // this.grand_total.khmer = this.numberRound(Number(this.grand_total.dollar) * Number(this
            //     .exchang_rate));
            // this.sub_total.khmer = this.numberRound((this.grand_total.khmer / 1.1));
            // this.vat.khmer = this.numberRound(this.grand_total.khmer - this.sub_total.khmer);

        },
        submitFrom() {
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
                                data.status_type = "invoice";
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
                                        this.$store.editReceiptInvoice.options.afterClose(res);
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
            this.$store.editReceiptInvoice.active = false;
        },
        calculatorAmount() {
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
            this.data.total_grand > this.dataForm.total_price ? cb(false) : cb(true);
        },
        checkValidPartial(cb) {
            this.dataForm.total_price > this.dataForm.partial_payment ? cb(false) : cb(true);
        },
        numberFormat(num) {
            return new Intl.NumberFormat().format(num.toFixed(2));
        },
    }));
</script>
<script>
    Alpine.store('editReceiptInvoice', {
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
    window.editReceiptInvoice = (options) => {
        Alpine.store('editReceiptInvoice', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('editReceiptInvoice').options,
                ...options
            }
        });
    };
</script>
