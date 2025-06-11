<template x-data="{}" x-if="$store.errorDialog.active">
    <div class="dialog" x-data="xErrorDialog" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm" style="justify-content: center;">
            <div class="diglogForm" :style="{ width: $store.errorDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3 style="color: red;">
                        Error
                    </h3>
                    <div style="display: flex;">
                        <button style="background: rgba(255, 0, 0, 0.7607843137)" type="button" class="btn-create mr-3"
                            @click="dialogClose()">
                            <i style="font-size: 18px;" class="material-symbols-outlined">close</i>
                            <span style="color: white;font-size: 14px;">Close</span>
                        </button>
                    </div>
                </div>
                <div class="content-body footerTableScroll">
                    <div class="tableLayoutCon tableLayoutWithFooter " style="height: 80vh;">
                        <div class="tableLy" style="border-radius: 0px;">
                            <div class="tableCustomScroll">
                                <div class="table excel">
                                    <div class="excel-body">
                                        <table class="tableWidth">
                                            <tbody class="column" style="margin-bottom: 12px;">
                                                <template x-for="(item,index) in errorData">
                                                    <tr style="background: white;">
                                                        <td style="color: red;text-align: left;font-weight: unset;"
                                                            class="row">
                                                            <span x-text="'Row'+'('+item.row_index+') =>'"></span>
                                                            <span
                                                                x-text="item.work_order_cfocn_value ?? item.isp_ex_work_order_isp_value"></span>:
                                                            <span x-text="item.duplicate_row"></span>
                                                            <span x-text="item.fttx_data_validate_unique"></span>
                                                            <span x-text="item.work_order_cfocn"></span>
                                                            <span x-text="item.work_order_isp"></span>
                                                            <span x-text="item.subscriber_no"></span>
                                                            <span
                                                                x-text="item.check_completed_time_and_start_payment_date"></span>
                                                            <span x-text="item.completed_time"></span>
                                                            <span x-text="item.customer_id"></span>
                                                            <span x-text="item.deadline"></span>
                                                            <span x-text="item.dismantle_date"></span>
                                                            <span x-text="item.first_payment_period"></span>
                                                            <span x-text="item.lay_fiber"></span>
                                                            <span x-text="item.pos_speed_id"></span>
                                                            <span x-text="item.reactive_date"></span>
                                                            <span x-text="item.relocation_date"></span>
                                                            <span x-text="item.status"></span>
                                                            <span x-text="item.team_install"></span>
                                                            <span
                                                                x-text="item.relocation_or_change_splitter_date"></span>
                                                            <span x-text="item.change_splitter_date"></span>
                                                            <span x-text="item.reactive_payment_period"></span>
                                                            <span x-text="item.valid_reactive_payment_period"></span>
                                                            <span
                                                                x-text="item.check_validate_first_payment_period"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data('xErrorDialog', () => ({
        errorData: [],
        dialogData: null,
        async init() {
            let error = this.$store.errorDialog.options.data;
            this.errorData = error;

        },
        dialogClose() {
            this.$store.importData.active = false;
            this.$store.errorDialog.active = false;
        },
    }));
</script>
<script>
    Alpine.store('errorDialog', {
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
    window.errorDialog = (options) => {
        Alpine.store('errorDialog', {
            active: true,
            options: {
                ...Alpine.store('errorDialog').options,
                ...options
            }
        });
    };
</script>
