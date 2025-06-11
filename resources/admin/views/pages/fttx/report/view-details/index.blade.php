<template x-data="{}" x-if="$store.reportDetailDialog.active">
    <div class="dialog" x-data="xReportDetail" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.reportDetailDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Report Details ( <span x-text="dialogData?.name_en ?? dialogData?.name_kh"></span> ) ( <span
                            x-text="dialogData?.from_date + - + dialogData?.to_date"></span> )</h3>
                    <div style="display: flex;">
                        <button style="background: rgba(255, 0, 0, 0.7607843137)" type="button" class="btn-create mr-3"
                            @click="dialogClose()">
                            <i style="font-size: 18px;" class="material-symbols-outlined">close</i>
                            <span style="color: white;font-size: 14px;">Close</span>
                        </button>
                    </div>
                </div>
                @include('admin::pages.fttx.report.view-details.table')
                <template x-if="submitLoading">
                    @include('admin::components.spinner')
                </template>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data('xReportDetail', () => ({
        submitLoading: false,
        reportData: [],
        formSubmitData: {
            disable: false
        },

        async init() {
            this.submitLoading = true;
            let data = this.$store.reportDetailDialog.options.data;
            this.dialogData = data;
            await this.getDetailData();
        },
        async getDetailData() {
            const params = new URLSearchParams({
                customer_id: this.dialogData.id || '',
                fttx_status: this.dialogData.fttx_status || '',
                from_date: this.dialogData.from_date || '',
                to_date: this.dialogData.to_date || '',
                pos_speed_id: this.dialogData.pos_speed_id || ''
            }).toString();

            await Axios.get(`/admin/fttx/report/get-detail?${params}`)
                .then(resp => {
                    this.reportData = resp.data.data;
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
