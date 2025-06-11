@component('admin::components.dialog', ['dialog' => 'invoiceConfirmDialog'])
    <div x-data="invoiceConfirmDialog" class="dialog-form" style="width: 300px">
        <div class="dialog-form-header">
            <h3 x-text="data?.title"></h3>
        </div>
        <div class="dialog-form-body">
            <div class="form-row">
                <p x-html="data?.message"></p>
            </div>
        </div>
        <div class="dialog-form-footer">
            <button type="button" class="close" @click="$store.invoiceConfirmDialog.close(false)"
                x-text="data?.btnClose || 'Close'" x-bind:disabled="disabled || loading"></button>
            <button type="button" @click="onConfirm" x-bind:disabled="disabled || loading">
                <div class="loader" style="display: none" x-show="loading"></div>
                <span x-text="data?.btnSave || 'Save'"></span>
            </button>
        </div>
    </div>
    <script>
        Alpine.data("invoiceConfirmDialog", () => ({
            data: null,
            disabled: false,
            loading: false,
            dataInvoice: null,
            connectonServer: {
                status: false,
                message: null
            },
            async init() {
                this.data = this.$store.invoiceConfirmDialog.data;
            },
            jsonPase(data) {
                return JSON.parse(data);
            },
            async fetchData(url, callback) {
                await fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        },
                        body: null,
                    })
                    .then(response => response.json())
                    .then(response => {
                        callback(response);
                    })
                    .catch((e) => {})
                    .finally(async (res) => {});
            },
            async onConfirm() {
                this.disabled = true;
                this.loading = true;
                await setTimeout(async () => {
                    // this.invoiceDelete((resInvoice) => {
                    //     if (resInvoice) {
                    //         this.$store.invoiceConfirmDialog.close(true);
                    //     }
                    //     this.loading = false;
                    //     reloadUrl("{{ url()->full() }}");
                    // });
                    this.loading = false;
                    this.$store.invoiceConfirmDialog.close(true);
                }, 500);
            },
            invoiceDelete(callback) {
                const url = `/admin/work-order/invoice/delete/${this.data?.item?.id}`;
                Axios({
                    url: url,
                    method: 'GET',
                    data: {
                        ...this.data?.item
                    }
                }).then((res) => {
                    if (res.data.message == "success") {
                        callback(true);
                    }
                }).catch((e) => {
                    callback(false);
                }).finally(() => {
                    callback(false);
                });
            },
        }));
    </script>
@endcomponent
