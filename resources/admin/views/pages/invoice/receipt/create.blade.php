<template x-data="{}" x-if="$store.createReceipt.active">
    <div class="dialog" x-data="xCreateReceipt" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.createReceipt?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.createReceipt?.options?.title"></h3>
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
                                            <div class="row table-row-12 text-start">
                                                <span>UNIT PRICE</span>
                                            </div>
                                            <div class="row table-row-18 text-end">
                                                <span>AMOUNT</span>
                                            </div>
                                        </div>

                                        <div class="table-body">
                                            {{-- body --}}
                                            <template x-for="(item,index) in data?.invoice_detail">
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
                                                    <div class="row table-row-12">
                                                        <div class="divTag text-start">
                                                            <span>$</span>
                                                            <span
                                                                x-text="item?.price?numberRound(item?.price,2):''"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row table-row-18">
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
                                                                    step="any" @input="calculatorAmount()"
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
                                                            <div class="divTag font13"
                                                                x-text="dataForm?.vat?numberRound(dataForm?.vat,2):0">
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
                                                                    step="any" @input="calculatorAmount()"
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
    Alpine.data('xCreateReceipt', () => ({
        loading: false,
        data: null,
        dataError: [],
        dataForm: {
            disable: false,
            receipt_number: null,
            note: null,
            issue_date: null,
            vat: null,
            total_price: null,
            paid_amount: null,
            debt_amount: null,
            total_grand: null,
            partial_payment: 0,
        },
        inputTotalPriceErr: false,
        inputTotalPartialErr: false,
        async init() {
            this.loading = true;
            await setTimeout(async () => {
                try {
                    this.data = this.$store.createReceipt.options.data;
                    
                    //patchValue
                    this.dataForm.receipt_number = this.data.receipt_number;
                    this.dataForm.total_price = this.data.total_price;
                    this.dataForm.vat = this.data.vat;
                    this.dataForm.total_grand = this.data.total_grand;
                    this.dataForm.paid_amount = this.data?.paid_amount;
                    this.dataForm.debt_amount = this.data?.debt_amount;
                    this.dataForm.note = this.data.note;

                    this.calculatorAmount();

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
                                data.invoice_id = this.data.id;
                                data.status_type = "invoice";
                                let url = `/admin/receipt/save`;
                                setTimeout(() => {
                                    Axios({
                                        url: url,
                                        method: 'POST',
                                        data: {
                                            ...data
                                        }
                                    }).then((res) => {
                                        this.loading = false;
                                        this.dataForm.disable = false;
                                        this.dialogClose();
                                        this.$store.edit.options.afterClose(res);
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
            this.$store.createReceipt.active = false;
        },
        calculatorAmount() {
            this.dataForm.vat = (this.dataForm.total_price * 10 / 100).toFixed(2);
            this.dataForm.total_grand = (Number(this.dataForm.total_price) + Number(this.dataForm.vat))
                .toFixed(2);
            this.dataForm.debt_amount = (Number(this.dataForm.total_price) - Number(this.dataForm
                .total_grand)).toFixed(2);
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
    Alpine.store('createReceipt', {
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
    window.createReceipt = (options) => {
        Alpine.store('createReceipt', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('createReceipt').options,
                ...options
            }
        });
    };
</script>
