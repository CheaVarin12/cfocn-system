<template x-data="{}" x-if="$store.updatePriceByMonthDialog.active">
    <div class="dialog" x-data="xupdatePriceByMonthDialog" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.updatePriceByMonthDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 x-show="dialogData">Edit Price By First Payment Period</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-2">
                                <div class="form-row">
                                    <label>Rental Price (Pay 6 Month)<span>*</span></label>
                                    <input type="text" x-model="formSubmitData.rental_price_six_month"
                                        placeholder="Enter price" required>
                                    <template x-for="item in dataError?.rental_price_six_month">
                                        <div class="errorCenter">
                                            <span style="position: static;" class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Rental Price (Pay 12 Month)<span>*</span></label>
                                    <input type="text" x-model="formSubmitData.rental_price_twelve_month"
                                        placeholder="Enter price" required>
                                    <template x-for="item in dataError?.rental_price_twelve_month">
                                        <div class="errorCenter">
                                            <span style="position: static;" class="error" x-text="item">Error</span>
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
    Alpine.data('xupdatePriceByMonthDialog', () => ({
        submitLoading: false,
        dataError: [],
        formSubmitData: {
            rental_price_six_month: null,
            rental_price_twelve_month: null,
            disable: false
        },
        dialogData: null,
        async init() {
            let data = this.$store.updatePriceByMonthDialog.options.data;
            this.dialogData = data;
            if (this.dialogData) {
                this.submitLoading = true;
                this.formSubmitData.rental_price_six_month = this.dialogData.rental_price_six_month;
                this.formSubmitData.rental_price_twelve_month = this.dialogData
                    .rental_price_twelve_month;
                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");
            }
        },
        dialogClose() {
            this.$store.updatePriceByMonthDialog.active = false;
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
                                url: `{{ route('admin-pos-speed-update-price-by-payment-period') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: id,
                                }
                            }).then((res) => {
                                this.submitLoading = false;
                                let status = res.data.status;
                                if (status == "success") {
                                    this.dialogClose();
                                    reloadUrl(
                                        "{!! url()->current() !!}"
                                    );
                                    Toast({
                                        title: 'Fttx',
                                        message: res.data.message,
                                        status: 'success',
                                        size: 'small',
                                    });
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
    }));
</script>
<script>
    Alpine.store('updatePriceByMonthDialog', {
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
    window.updatePriceByMonthDialog = (options) => {
        Alpine.store('updatePriceByMonthDialog', {
            active: true,
            options: {
                ...Alpine.store('updatePriceByMonthDialog').options,
                ...options
            }
        });
    };
</script>
