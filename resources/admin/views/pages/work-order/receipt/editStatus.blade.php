<template x-data="{}" x-if="$store.editStatus.active">
    <div class="dialog" x-data="xEditStatus" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.editStatus?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-text="$store.editStatus?.options?.title"></h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#" method="POST">
                        <div class="form-body">
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Payment Method<span>*</span></label>
                                    <select name="payment_method" x-model="dataForm.payment_method"
                                        :disabled="dataForm.disable">
                                        <option value="">Select ...</option>
                                        <option value="bank">Bank</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                    <template x-for="item in dataError?.payment_method">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Payment Status<span>*</span></label>
                                    <select name="payment_status" x-model="dataForm.payment_status"
                                        :disabled="dataForm.disable">
                                        <option value="">Select ...</option>
                                        <option value="portal">Portal</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                    <template x-for="item in dataError?.payment_status">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Paid Date<span>*</span></label>
                                    <input type="text" name="paid_date" x-ref="paid_date" placeholder="Paid date"
                                        id="paidDate" autocomplete="off" x-model="dataForm.paid_date"
                                        :disabled="dataForm.disable">
                                    <template x-for="item in dataError?.paid_date">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Description <span>*</span></label>
                                    <textarea placeholder="Enter description" name="payment_des" rows="5" x-model="dataForm.payment_des"
                                        :disabled="dataForm.disable"></textarea>
                                    <template x-for="item in dataError?.payment_des">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button class="primary" type="button" @click="submitFrom()">
                                <i class='bx bx-save bx-tada-hover'></i>
                                <span>Submit</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()">
                                <i class='bx bx-x bx-tada-hover'></i>
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
    Alpine.data('xEditStatus', () => ({
        loading: false,
        des_reason: null,
        dataForm: {
            disable: false,
            payment_method: null,
            payment_status: null,
            paid_date: null,
            payment_des: null
        },
        data: {
            purchase_type: false
        },
        dataError: [],
        async init() {
            let dataStore = this.$store.editStatus.options.data;
            //patchFormValue
            this.dataForm.payment_method = dataStore?.payment_method;
            this.dataForm.payment_status = dataStore?.payment_status;
            this.dataForm.paid_date = dataStore?.paid_date;
            this.dataForm.payment_des = dataStore?.payment_des;

            $("#paidDate").datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd"
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
        dialogClose() {
            this.$store.editStatus.active = false;
        },
        add() {
            let data = {
                des_reason: this.des_reason
            }
            this.$store.editStatus.options.afterClose(data);
            this.dialogClose();
        },
        submitFrom() {
            let dataStore = this.$store.editStatus.options.data;
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
                        this.dataForm.disable = true;
                        this.loading = true;
                        let data = this.dataForm;
                        data.paid_date = this.$refs.paid_date.value;
                        setTimeout(() => {
                            Axios({
                                url: `{{ route('admin-work-order-receipt-update-status') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: dataStore.id
                                }
                            }).then((res) => {
                                this.loading = false;
                                this.dataForm.disable = false;
                                this.dialogClose();
                                this.$store.editStatus.options.afterClose('res');
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
        },
    }));
</script>

{{-- store --}}
<script>
    Alpine.store('editStatus', {
        active: false,
        dmcBtn: false,
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
    window.editStatus = (options) => {
        Alpine.store('editStatus', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('editStatus').options,
                ...options
            }
        });
    };
</script>
