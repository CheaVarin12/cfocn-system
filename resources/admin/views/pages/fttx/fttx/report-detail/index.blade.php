<template x-data="{}" x-if="$store.reportDetailDialog.active">
    <div class="dialog" x-data="xReportByMonth" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.reportDetailDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>
                        Report Details (
                        <span x-text="dialogData?.customer?.name_en ?? dialogData?.customer?.name_kh"></span>,
                        <span x-text="'Work Order ISP: ' + (dialogData?.work_order_isp ?? 'N/A')"></span>,
                        <span x-text="'Work Order CFOCN: ' + (dialogData?.work_order_cfocn ?? 'N/A')"></span>
                        )
                    </h3>
                    <div style="display: flex;">
                        <button style="margin-right: 12px;background:rgba(0, 0, 255, 0.6784313725)" type="button"
                            class="btn-create" @click="storeDialog()">
                            <i style="font-size: 18px;" class="material-symbols-outlined">add</i>
                            <span style="color: white;font-size: 14px;">Create</span>
                        </button>
                        <button style="background: rgba(255, 0, 0, 0.7607843137)" type="button" class="btn-create mr-3"
                            @click="dialogClose()">
                            <i style="font-size: 18px;" class="material-symbols-outlined">close</i>
                            <span style="color: white;font-size: 14px;">Close</span>
                        </button>
                    </div>
                </div>
                @include('admin::pages.fttx.fttx.report-detail.table')
                @include('admin::pages.fttx.fttx.report-detail.store')
                <template x-if="submitLoading">
                    @include('admin::components.spinner')
                </template>
            </div>
        </div>
    </div>
</template>
<script>
    $(document).ready(function() {
        $('#startDate, #endDate').on('change', function() {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            if (startDate && endDate && startDate > endDate) {
                $('#endDate').val(startDate);
            }
            $('#endDate').attr('min', startDate);
        });
    });
</script>
<script>
    Alpine.data('xReportByMonth', () => ({
        submitLoading: false,
        reportData: [],
        formDataSearch: {
            start_date:null,
            end_date:null,
        },
        dialogData: null,
        total: {
            dialogData: null,
            new_installation_fee: 0,
            fiber_jumper_fee: 0,
            digging_fee: 0,
            rental_unit_price: 0,
            ppcc: 0,
            pole_rental_fee: 0,
            other_fee: 0,
            discount: 0,
            total_amount: 0,
        },
        fttx_id: null,
        async init() {
            this.submitLoading = true;
            let id = this.$store.reportDetailDialog.options.data;
            if (id) {
                await this.getFttx(id);
            }
            this.fttx_id = id;
            await this.getDetailData(id);

            this.getTotalAmount();
            if (this.dialogData) {
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
        getTotalAmount() {
            this.total.new_installation_fee = 0;
            this.total.fiber_jumper_fee = 0;
            this.total.digging_fee = 0;
            this.total.rental_unit_price = 0;
            this.total.ppcc = 0;
            this.total.pole_rental_fee = 0;
            this.total.other_fee = 0;
            this.total.discount = 0;
            this.total.total_amount = 0;

            this.reportData.forEach((item) => {
                this.total.new_installation_fee += Number(item.new_installation_fee ?? 0);
                this.total.fiber_jumper_fee += Number(item.fiber_jumper_fee ?? 0);
                this.total.digging_fee += Number(item.digging_fee ?? 0);
                this.total.rental_unit_price += Number(item.rental_unit_price ?? 0);
                this.total.ppcc += Number(item.ppcc ?? 0);
                this.total.pole_rental_fee += Number(item.pole_rental_fee ?? 0);
                this.total.other_fee += Number(item.other_fee ?? 0);
                this.total.discount += Number(item.discount ?? 0);
                this.total.total_amount += Number(item.total_amount ?? 0);
            });

            this.total.new_installation_fee = this.numberRound(this.total.new_installation_fee, 2);
            this.total.fiber_jumper_fee = this.numberRound(this.total.fiber_jumper_fee, 2);
            this.total.digging_fee = this.numberRound(this.total.digging_fee, 2);
            this.total.rental_unit_price = this.numberRound(this.total.rental_unit_price, 2);
            this.total.ppcc = this.numberRound(this.total.ppcc, 2);
            this.total.pole_rental_fee = this.numberRound(this.total.pole_rental_fee, 2);
            this.total.other_fee = this.numberRound(this.total.other_fee, 2);
            this.total.discount = this.numberRound(this.total.discount, 2);
            this.total.total_amount = this.numberRound(this.total.total_amount, 2);
        },

        refresh(fttxID){
            this.formDataSearch.start_date=''
            this.formDataSearch.end_date='';
            this.getDetailData(fttxID);
        },

        async getDetailData(fttxID) {
            this.submitLoading = true;
            this.reportData =[] ;
            let id = fttxID;
            await Axios.get(`/admin/fttx/fttx/get-fttx-detail/?id=${id?id:null}&start_date=${this.formDataSearch.start_date??''}&end_date=${this.formDataSearch.end_date??''}`).then(resp => {
                this.reportData = resp.data.data.data
                this.formDataSearch.start_date=resp.data.data.start_date;
                this.formDataSearch.end_date=resp.data.data.end_date;
                this.getTotalAmount();
                setTimeout(() => {
                    this.submitLoading = false;
                }, "500");

            });
        },

        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
        },

        dialogClose() {
            this.$store.reportDetailDialog.active = false;
        },
        storeDialog(data) {
            storeFttxDetailDialog({
                active: true,
                data: data,
                fttx_id: this.fttx_id,
                dmcBtn: true,
                title: "Create",
                config: {
                    width: "70%",
                }
            });
        },

        onDelete(data, index) {
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: "Are you sure to delete?",
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        setTimeout(() => {
                            this.submitLoading = true;
                            Axios({
                                url: `{{ route('admin-fttx-delete-fttx-detail') }}`,
                                method: 'POST',
                                data: {
                                    id: data.id,
                                }
                            }).then((res) => {
                                let status = res.data.status;
                                if (status == "success") {
                                    Toast({
                                        title: 'Fttx Detail',
                                        message: 'Data deleted successfully',
                                        status: 'success',
                                        size: 'small',
                                    });
                                }
                                this.getDetailData(this.dialogData.id);
                                setTimeout(
                                    () => {
                                        this.submitLoading = false;
                                    }, 100);

                            }).catch((e) => {

                            }).finally(() => {
                                this.getDetailData(this.dialogData.id)
                            });
                        }, 500);
                    }
                }
            });
        }
    }));
</script>
<script>
    Alpine.store('reportDetailDialog', {
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
    window.reportDetailDialog = (options) => {
        Alpine.store('reportDetailDialog', {
            active: true,
            options: {
                ...Alpine.store('reportDetailDialog').options,
                ...options
            }
        });
    };
</script>
