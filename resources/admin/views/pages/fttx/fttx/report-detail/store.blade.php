<template x-data="{}" x-if="$store.storeFttxDetailDialog.active">
    <div class="dialog" x-data="xInsertFttxDetail" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-show="!dialogData">Create Fttx Detail</h3>
                    <h3 x-show="dialogData">Edit Fttx Detail</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Date<span>*</span></label>
                                    <input type="date" id="date" name="date" x-model="formSubmitData.date">
                                    <template x-for="item in dataError?.date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Expiry Date<span>*</span></label>
                                    <input type="date" id="expiry_date" name="expiry_date"
                                        x-model="formSubmitData.expiry_date">
                                    <template x-for="item in dataError?.expiry_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>New Installation Fee </label>
                                    <input @input="getTotalAmount()" type="number"
                                        x-model="formSubmitData.new_installation_fee"
                                        placeholder="Enter new installation fee">
                                    <template x-for="item in dataError?.new_installation_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Fiber Jumper Fee</label>
                                    <input @input="getTotalAmount()" type="number"
                                        x-model="formSubmitData.fiber_jumper_fee" placeholder="Enter fiber jumper fee">
                                    <template x-for="item in dataError?.fiber_jumper_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Digging Fee </label>
                                    <input @input="getTotalAmount()" type="number" x-model="formSubmitData.digging_fee"
                                        placeholder="Enter digging fee">
                                    <template x-for="item in dataError?.digging_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Rental Unit Price</label>
                                    <input @input="getTotalAmount()" type="number"
                                        x-model="formSubmitData.rental_unit_price"
                                        placeholder="Enter rental unit price">
                                    <template x-for="item in dataError?.rental_unit_price">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>ppcc</label>
                                    <input @input="getTotalAmount()" type="number" x-model="formSubmitData.ppcc"
                                        placeholder="Enter ppcc">
                                    <template x-for="item in dataError?.ppcc">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Pole rental Fee</label>
                                    <input @input="getTotalAmount()" type="number"
                                        x-model="formSubmitData.pole_rental_fee" placeholder="Enter pole rental fee">
                                    <template x-for="item in dataError?.pole_rental_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Other fee</label>
                                    <input @input="getTotalAmount()" type="number" x-model="formSubmitData.other_fee"
                                        placeholder="Enter other fee">
                                    <template x-for="item in dataError?.other_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Discount</label>
                                    <input @input="getTotalAmount()" type="number" x-model="formSubmitData.discount"
                                        placeholder="Enter discount">
                                    <template x-for="item in dataError?.discount">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Invoice Number</label>
                                    <input type="text" x-model="formSubmitData.invoice_number"
                                        placeholder="Enter invoice number">
                                    <template x-for="item in dataError?.invoice_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Receipt Number</label>
                                    <input type="text" x-model="formSubmitData.receipt_number"
                                        placeholder="Enter receipt number">
                                    <template x-for="item in dataError?.receipt_number">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Total Amount</label>
                                    <input type="number" x-model="formSubmitData.total_amount" readonly
                                        placeholder="Enter total amount">
                                    <template x-for="item in dataError?.total_amount">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row" style="flex: 2">
                                    <label>Remark</label>
                                    <input type="text" x-model="formSubmitData.remark" placeholder="Enter remark">
                                    <template x-for="item in dataError?.remark">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="button" class="primary" color="primary" @click="submitFrom()">
                                <i class='bx bx-save'></i>
                                <span>Save</span>
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
    Alpine.data('xInsertFttxDetail', () => ({
        submitLoading: false,
        dataError: [],
        formSubmitData: {
            fttx_id: null,
            customer_id: null,
            date: null,
            expiry_date: null,
            new_installation_fee: null,
            fiber_jumper_fee: null,
            digging_fee: null,
            rental_unit_price: null,
            ppcc: null,
            pole_rental_fee: null,
            other_fee: null,
            discount: null,
            remark: null,
            invoice_number: null,
            receipt_number: null,
            total_amount: null,
            disable: false
        },
        dialogData: null,
        async init() {
            this.formSubmitData.fttx_id = this.$store.storeFttxDetailDialog.options.fttx_id;
            let data = this.$store.storeFttxDetailDialog.options.data;
            this.dialogData = data;
            if (this.dialogData) {
                this.submitLoading = true;
                this.formSubmitData.fttx_id = this.dialogData.fttx_id;
                this.formSubmitData.customer_id = this.dialogData.customer_id;
                this.formSubmitData.date = this.dialogData.date;
                this.formSubmitData.expiry_date = this.dialogData.expiry_date;
                this.formSubmitData.new_installation_fee = this.dialogData.new_installation_fee;
                this.formSubmitData.fiber_jumper_fee = this.dialogData.fiber_jumper_fee;
                this.formSubmitData.digging_fee = this.dialogData.digging_fee;
                this.formSubmitData.rental_unit_price = this.dialogData.rental_unit_price;
                this.formSubmitData.ppcc = this.dialogData.ppcc;
                this.formSubmitData.pole_rental_fee = this.dialogData.pole_rental_fee;
                this.formSubmitData.other_fee = this.dialogData.other_fee;
                this.formSubmitData.discount = this.dialogData.discount;
                this.formSubmitData.remark = this.dialogData.remark;
                this.formSubmitData.invoice_number = this.dialogData.invoice_number;
                this.formSubmitData.receipt_number = this.dialogData.receipt_number;
                this.formSubmitData.total_amount = this.dialogData.total_amount;
                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");
            }
        },
        submitFrom() {
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
                        let id = this.dialogData ? this.dialogData.id : null;
                        this.submitLoading = true;
                        let data = this.formSubmitData ?? {};

                        setTimeout(() => {
                            Axios({
                                url: `{{ route('admin-fttx-store-fttx-detail') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: id,
                                }
                            }).then((res) => {
                                this.submitLoading = false;
                                let status = res.data.status;
                                if (status == "success") {
                                    Toast({
                                        title: 'Fttx',
                                        message: res.data.message,
                                        status: 'success',
                                        size: 'small',
                                    });
                                    setTimeout(() => {
                                        this.dialogClose();
                                    }, "500");

                                }
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
        },
        dialogClose() {
            this.$store.reportDetailDialog.active = false;
            setTimeout(() => {
                reportDetailDialog({
                    active: true,
                    data: this.$store.reportDetailDialog.options.data,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "97%",
                    }
                });
            }, "10");
        },
        getTotalAmount() {
            let newInstallationFee = this.formSubmitData.new_installation_fee ? this.formSubmitData
                .new_installation_fee : 0;
            let fiberJumperFee = this.formSubmitData.fiber_jumper_fee ? this.formSubmitData
                .fiber_jumper_fee : 0;
            let diggingFee = this.formSubmitData.digging_fee ? this.formSubmitData.digging_fee : 0;
            let rentalUnitPrice = this.formSubmitData.rental_unit_price ? this.formSubmitData
                .rental_unit_price : 0;
            let ppcc = this.formSubmitData.ppcc ? this.formSubmitData.ppcc : 0;
            let poleRentalFee = this.formSubmitData.pole_rental_fee ? this.formSubmitData.pole_rental_fee :
                0;
            let otherFee = this.formSubmitData.other_fee ? this.formSubmitData.other_fee : 0;
            let discount = this.formSubmitData.discount ? this.formSubmitData.discount : 0;

            this.formSubmitData.total_amount = (Number(newInstallationFee) + Number(fiberJumperFee) +
                Number(diggingFee) + Number(rentalUnitPrice) + Number(ppcc) + Number(poleRentalFee) +
                Number(otherFee)) - Number(discount);
            this.formSubmitData.total_amount = this.numberRound(this.formSubmitData.total_amount);
        },
        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
        },
    }));
</script>
<script>
    Alpine.store('storeFttxDetailDialog', {
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
    window.storeFttxDetailDialog = (options) => {
        Alpine.store('storeFttxDetailDialog', {
            active: true,
            options: {
                ...Alpine.store('storeFttxDetailDialog').options,
                ...options
            }
        });
    };
</script>
