@component('admin::components.dialog', ['dialog' => 'DMCSubmitStatusDialog'])
    <div x-data="DMCSubmitStatusDialog" class="dialog-form dmc-submit-status-dialog" style="width: 300px">
        <div class="dialog-form-body">
            <div class="form-row">
                <i class='bx bx-error bx-tada'></i>
                <h3 x-text="data?.title"></h3>
                <p x-html="data?.message"></p>
            </div>
        </div>
        <div class="dialog-form-footer dmc-status-footer">
            <button type="button" class="close-dmc-alert" @click="$store.DMCSubmitStatusDialog.close(false)"
                x-text="data?.btnClose || 'Close'"></button>
                {{-- x-text="'( '+parseInt(timeCloseAuto)+' ) '+ data?.btnClose || 'Close'" --}}
                {{-- <button type="button" @click="onConfirm" x-bind:disabled="disabled || loading">
                    <span x-text="data?.btnSave || 'Save'"></span>
                    <div class="loader" style="display: none" x-show="loading"></div>
                </button> --}}
        </div>
    </div>
    <script>
        Alpine.data("DMCSubmitStatusDialog", () => ({
            data: null,
            disabled: false,
            loading: false,
            timeCloseAuto: 0,
            init() {
                this.data = this.$store.DMCSubmitStatusDialog.data;
                // this.dialogCloseWithTime();
                feather.replace();
            },
            onConfirm() {
                this.disabled = true;
                this.$store.DMCSubmitStatusDialog.close(true);
            },
            dialogCloseWithTime(type = null) {
                var setTime = 5;
                var timeMin = 15;
                var number = timeMin * setTime;
                this.timeCloseAuto = setTime;
                var time = 0;
                var barWidth = 0;
                var intervalID = null;
                var timeoutID = null;
                var sumTime = 0;
                let ff = false;
                if (type == 'close') {
                    this.$store.DMCSubmitStatusDialog.close(false);
                    clearTimeout(timeoutID);
                    clearInterval(intervalID);
                    number = 0;
                } else {
                    timeoutID = setTimeout(() => {
                        intervalID = setInterval(() => {
                            if (barWidth === number) {
                                clearInterval(intervalID);
                            } else {
                                barWidth++;
                                time = parseInt(barWidth / timeMin);
                                sumTime +=time;
                                this.timeCloseAuto = setTime - time;
                            }
                        }, number);

                    }, 200);
                }
            },
        }))
    </script>
@endcomponent
