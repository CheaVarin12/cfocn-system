<template x-data="{}" x-if="$store.renewalFttxDialog.active">
    <div class="dialog" x-data="xRenewalFttx" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm" style=" justify-content: center !important;">
            <div class="diglogForm" style="width: 50% !important">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Renewal</h3>
                    <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#">
                        <div class="form-body">
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Current deadline<span>*</span></label>
                                    <input @input="calculateNewDeadline()" type="date" id="deadline"
                                        x-model="formSubmitData.deadline" readonly>
                                    <template x-for="item in dataError?.deadline">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Number of month <span>*</span></label>
                                    <input type="number"  @input="calculateNewDeadline()" x-model="formSubmitData.number_of_month">
                                    <template x-for="item in dataError?.number_of_month">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>New deadline<span>*</span></label>
                                    <input type="date" id="new_deadline" x-model="formSubmitData.new_deadline"
                                        readonly>
                                    <template x-for="item in dataError?.new_deadline">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Rental Price<span>*</span></label>
                                    <input type="number" x-model="formSubmitData.rental_price" @input="getTotalPrice()"
                                        placeholder="Enter rental price">
                                    <template x-for="item in dataError?.rental_price">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Ppcc</label>
                                    <input type="number" x-model="formSubmitData.ppcc" @input="getTotalPrice()"
                                        placeholder="Enter ppcc">
                                    <template x-for="item in dataError?.ppcc">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Rental Pole</label>
                                    <input type="number" x-model="formSubmitData.rental_pole" @input="getTotalPrice()"
                                        placeholder="Enter price">
                                    <template x-for="item in dataError?.rental_pole">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Other Fee</label>
                                    <input type="number" x-model="formSubmitData.other_fee"
                                        placeholder="Enter other fee">
                                    <template x-for="item in dataError?.other_fee">
                                        <div class="errorCenter">
                                            <span class="error" x-text="item">Error</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="form-row">
                                    <label>Total<span>*</span></label>
                                    <input type="number" x-model="formSubmitData.total" placeholder="Enter total">
                                    <template x-for="item in dataError?.total">
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
    Alpine.data('xRenewalFttx', () => ({
        submitLoading: false,
        dataError: [],
        formSubmitData: {
            deadline: null,
            number_of_month: 1,
            new_deadline: null,
            rental_price: 0,
            ppcc: 0,
            rental_pole: 0,
            other_fee: 0,
            total: null,
            disable: false
        },
        dialogData: null,
        column: null,
        async init() {
            let id = this.$store.renewalFttxDialog.options.data;
            if (id) {
                await this.getFttx(id);
            }
            if (this.dialogData) {
                let currentRentalPrice = this.dialogData.rental_price;
                let currentPpcc = this.dialogData.ppcc;
                let currentRentalPole = this.dialogData.rental_pole;
                let customerId = this.dialogData.customer_id;
                let posSpeedId = this.dialogData.pos_speed_id;

                this.formSubmitData.deadline = this.dialogData.deadline;
                await this.getPriceFttx(customerId, posSpeedId, currentRentalPrice, currentPpcc,
                    currentRentalPole);

                this.calculateNewDeadline();

                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");
            }
        },

        async getFttx(id) {
            await Axios.get(`/admin/select/get-fttx/${id}`).then(resp => {
                this.dialogData = resp.data;
            });
        },
        async getPriceFttx(customerId, posSpeedId, currentRentalPrice, currentPpcc, currentRentalPole) {
            const params = new URLSearchParams({
                customerId: customerId,
                posSpeedId: posSpeedId,
                currentRentalPrice: currentRentalPrice,
                currentPpcc: currentPpcc,
                currentRentalPole: currentRentalPole,
            }).toString();

            await Axios.get(`/admin/select/get-standard-price-fttx?${params}`)
                .then(resp => {
                    this.formSubmitData.rental_price = resp.data.rental_price;
                    this.formSubmitData.ppcc = resp.data.ppcc;
                    this.formSubmitData.rental_pole = resp.data.rental_pole;
                    this.getTotalPrice();
                });
        },
        getTotalPrice() {
            let duration = this.formSubmitData.number_of_month ? Number(this.formSubmitData
                .number_of_month) : 0;
            let rentalPrice = this.formSubmitData.rental_price ? Number(this.formSubmitData.rental_price) :
                0;
            let ppcc = this.formSubmitData.ppcc ? Number(this.formSubmitData.ppcc) : 0;
            let rentalPole = this.formSubmitData.rental_pole ? Number(this.formSubmitData.rental_pole) : 0;
            let otherFee = this.formSubmitData.other_fee ? Number(this.formSubmitData.other_fee) : 0;

            let subtotal = (rentalPrice + ppcc + rentalPole) * duration;
            this.formSubmitData.total = this.numberRound(subtotal + otherFee, 2);
        },

        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
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
                                url: `{{ route('admin-fttx-renewal') }}`,
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
        calculateNewDeadline() {
            const currentDeadlineValue = this.formSubmitData.deadline;
            const numberOfMonthValue = this.formSubmitData.number_of_month || 1;

            if (currentDeadlineValue && numberOfMonthValue && !isNaN(numberOfMonthValue)) {
                const currentDeadline = new Date(currentDeadlineValue);

                currentDeadline.setMonth(currentDeadline.getMonth() + parseInt(numberOfMonthValue));

                const formattedNewDeadline = currentDeadline.toISOString().split('T')[0];

                this.formSubmitData.new_deadline = formattedNewDeadline;
            } else {
                this.formSubmitData.new_deadline = '';
            }
            this.getTotalPrice();
        },
        dialogClose() {
            this.$store.renewalFttxDialog.active = false;
        },
    }));
</script>
<script>
    Alpine.store('renewalFttxDialog', {
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
    window.renewalFttxDialog = (options) => {
        Alpine.store('renewalFttxDialog', {
            active: true,
            options: {
                ...Alpine.store('renewalFttxDialog').options,
                ...options
            }
        });
    };
</script>
