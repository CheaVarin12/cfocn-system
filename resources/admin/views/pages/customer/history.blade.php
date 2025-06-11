@component('admin::components.dialog', ['dialog' => 'detail'])
    <div x-data="detail" class="dialog-form reportSaleJournalDetail" style="width: 800px;position: relative;">
        <div class="titleReportDetail">
            <h3>Customer detail (ID:<span x-text="$store.detail.data?.customer_code"></span>)</h3>
            <button type="button" @click="$store.detail.close(false)" class="btnClose"><i class='bx bx-x'></i></button>
        </div>
        <div class="tableLayoutCon">
            <div class="tableLy">
                <div class="tableCustomScroll">
                    <div class="table excel">
                        <div class="excel-body">
                            <table class="tableWidth">
                                <thead class="column">
                                    <tr>
                                        <th>Nº</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Vat</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Register Date</th>
                                        <th>Address EN</th>
                                        <th>Address KH</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <template x-for="(item,index) in data?.data">
                                        <tr>
                                            <td>
                                                <span x-text="index + 1"></span>
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.customer_code??''"></span>
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.name_en??''"></span>
                                                (<span x-text="item.data_customer?.name_kh??''"></span>)
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.vat_tin??''"></span>
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.phone??''"></span>
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.email??''"></span>
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.register_date??''"></span>
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.address_en??''"></span>
                                            </td>
                                            <td>
                                                <span x-text="item.data_customer?.address_kh??''"></span>
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
        <template x-if="data?.data?.length > 0">
            <div class="dialogDetailPagination">
                <button type="button" :disabled="data?.prev_page_url ? false : true"
                    @click="fetchDataPagi(data?.prev_page_url)"><i class='bx bx-chevron-left'></i></button>
                <button type="button" :disabled="data?.next_page_url ? false : true"
                    @click="fetchDataPagi(data?.next_page_url)"><i class='bx bx-chevron-right'></i></button>
            </div>
        </template>
        <template x-if="!loading && data?.data?.length <=0 ">
            @component('admin::components.empty', [
                'name' => 'Data is empty',
            ])
            @endcomponent
        </template>
        <template x-if="loading">
            <div class="loadingFullSizeLayout" style="position: absolute;">
                <div class="loading loadingSubmit">
                    <span id="spinner"></span>
                    <label>Loading...</label>
                </div>
            </div>
        </template>
    </div>
    <script>
        Alpine.data("detail", () => ({
            data: null,
            disabled: false,
            loading: false,
            timeCloseAuto: 0,
            init() {
                let data = this.$store.detail.data;
                let url = `/admin/customer/history/${data.id}`;
                this.fetchDataPagi(url);
            },
            fetchDataPagi(url) {
                this.loading = true;
                let delayQuery = null;
                clearTimeout(delayQuery);
                delayQuery = setTimeout(async () => {
                    try {
                        await this.fetchData(url, (res) => {
                            this.data = res;
                            this.loading = false;
                        });
                    } catch (e) {
                        this.loading = false;
                    };
                }, 500);
            },
            onConfirm() {
                this.disabled = true;
                this.$store.detail.close(true);
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
        }))
    </script>
@endcomponent
