<div class="content-wrapper" x-data="dataX">
    <div class="header">
        @include('admin::shared.header', ['header_name' => 'DMC File Management'])
    </div>
    <div class="content-body fileManageControlLayout">
        @include('admin::pages.dmc_file_manager.filemanagerPage')
    </div>
    @include('admin::pages.dmc_file_manager.detail')
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dataX', () => ({
            empty: true,
            loading: false,
            docLoading: false,
            loadingYear: false,
            refreshLoading: false,
            data: [],
            invoiceSearch: '',
            dataLoading: false,
            folderAll: {
                id: 'all',
                name: "All file"
            },
            docType: {
                id: 'invoice',
                name: 'Invoice'
            },
            dataYear: {
                array: [],
                data: null,
                search: '',
                loading: false
            },
            year: {
                id: '',
                name: ''
            },
            dataMonth: [],
            month: {
                id: '',
                name: ''
            },
            monthData: @json(config('dmc-file-manager')['month']),
            dmcTypeData: @json(config('dmc-file-manager')['dmcType']),
            urlLogoFile: `{{ asset('/images/file') }}`,
            async init() {
                await this.fetchDataLoad();
            },
            async fetchDataLoad() {
                this.docLoading = true;
                this.resetFetchDataLoad();
                const params = `doc_type=${this.docType.id}`;
                let urlYear = `/admin/dmc-file-manager/year?${params}`;
                let urlData = `/admin/dmc-file-manager/fetchData?${params}`;
                let delayQuery = null;
                clearTimeout(delayQuery);
                delayQuery = setTimeout(async () => {
                    try {
                        await this.fetchData(urlYear, (res) => {
                            this.dataYear.data = res.data;
                            this.dataYear.array.push(...res.data.data);

                        });
                        await this.fetchData(urlData, (res) => {
                            this.data = res.data;
                            this.empty = this.data?.data?.length !== 0 ? false : true;
                        });
                        this.docLoading = false;
                    } catch (e) {
                        this.docLoading = false;
                    };
                }, 500);
            },
            resetFetchDataLoad() {
                this.data = [];
                this.dataMonth = [];
                this.dataYear.array = [];
                this.year = {
                    id: '',
                    name: '',
                };
                this.dataYear.search = '';
            },
            onInputYear() {
                this.loadingYear = true;
                let inputSearch = this.dataYear.search ?? '';
                const params = `doc_type=${this.docType.id}&search=${inputSearch}`;
                let urlYear = `/admin/dmc-file-manager/year?${params}`;
                let delayQuery = null;
                clearTimeout(delayQuery);
                delayQuery = setTimeout(async () => {
                    try {
                        this.dataYear.array = [];
                        await this.fetchData(urlYear, (res) => {
                            this.dataYear.data = res.data;
                            this.dataYear.array.push(...res.data.data);
                        });
                        this.loadingYear = false;
                    } catch (e) {
                        this.loadingYear = false;
                        this.empty = true;
                    };
                }, 500);

            },
            loadingMoreYear() {
                this.dataYear.loading = true;
                let inputSearch = this.dataYear.search ?? '';
                const params =
                    `page=${this.dataYear.data.current_page + 1}&doc_type=${this.docType.id}&search=${inputSearch}`;
                let urlYear = `/admin/dmc-file-manager/year?${params}`;
                let delayQuery = null;

                clearTimeout(delayQuery);
                delayQuery = setTimeout(async () => {
                    try {
                        await this.fetchData(urlYear, (res) => {
                            this.dataYear.data = res.data;
                            this.dataYear.array.push(...res.data.data);
                        });
                        this.dataYear.loading = false;
                    } catch (e) {
                        this.dataYear.loading = false;
                        this.empty = true;
                    };
                }, 500);

            },
            invoiceFetchSearch() {
                this.dataLoading = true;
                let inputSearch = this.invoiceSearch ?? '';
                this.empty = false;
                const params =
                    `year=${this.year.name}&month=${this.month.id}&doc_type=${this.docType.id}&search=${inputSearch}`;
                let url = `/admin/dmc-file-manager/fetchData?${params}`;
                let delayQuery = null;
                clearTimeout(delayQuery);
                delayQuery = setTimeout(async () => {
                    try {
                        await this.fetchData(url, (res) => {
                            this.data = res.data;
                            this.dataLoading = false;
                            this.empty = this.data.data.length !== 0 ? false :
                                true;
                        });

                    } catch (e) {
                        this.dataLoading = false;
                        this.empty = true;
                    };
                }, 500);

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
            async clickDocTypeFile(item) {
                this.docType = item;
                await this.fetchDataLoad();
            },
            async clickYear(item) {
                this.year.id = item != null ? item.id : null;
                this.year.name = item != null ? item.year : '';
                this.loading = true;
                this.empty = true;
                this.data = [];
                this.dataMonth = [];
                const params = `year=${this.year.name}&doc_type=${this.docType.id}`;
                let url = `/admin/dmc-file-manager/year-of-month?${params}`;
                let delayQuery = null;
                clearTimeout(delayQuery);
                delayQuery = setTimeout(async () => {
                    try {
                        await this.fetchData(url, (res) => {
                            this.dataMonth = res.data;
                        });
                        await this.clickMonth(this.folderAll);
                    } catch (e) {
                        this.loading = false;
                        this.empty = true;
                    };
                }, 500);
            },
            async clickMonth(item) {
                this.month.id = item != null ? item.id : '';
                this.month.name = item != null ? item.name : '';
                this.loading = true;
                this.empty = true;
                const params =
                    `year=${this.year.name}&month=${this.month.id}&doc_type=${this.docType.id}`;
                let urlData = `/admin/dmc-file-manager/fetchData?${params}`;
                let delayQuery = null;
                clearTimeout(delayQuery);
                delayQuery = setTimeout(async () => {
                    try {
                        await this.fetchData(urlData, (res) => {
                            this.data = res.data;
                        });
                        this.loading = false;
                        this.empty = this.data.data.length !== 0 ? false : true;
                    } catch (e) {
                        this.loading = false;
                        this.empty = true;
                    };
                }, 500);
            },
            async reloadData() {
                this.invoiceSearch = '';
                const params =
                    `year=${this.year.name}&month=${this.month.id}&doc_type=${this.docType.id}`;
                let url = `/admin/dmc-file-manager/fetchData?${params}`;
                this.refreshLoading = true;
                this.loading = true;
                let delayQuery = null;
                this.empty = true;

                clearTimeout(delayQuery);
                delayQuery = setTimeout(() => {
                    Axios.get(url).then((res) => {
                        if (res.data) {
                            this.data = res.data.data;
                            this.loading = false;
                            this.refreshLoading = false;
                            this.empty = this.data.data.length !== 0 ? false : true;
                        } else {
                            this.loading = false;
                            this.refreshLoading = false;
                            this.empty = true;
                        }
                    });
                }, 500);
            },
            paginate(page) {
                const params =
                    `page=${page}&year=${this.year.name}&month=${this.month.id}&doc_type=${this.docType.id}`;
                let url = `/admin/dmc-file-manager/fetchData?${params}`;
                if (url !== null) {
                    this.loading = true;
                    let delayQuery = null;
                    clearTimeout(delayQuery);
                    delayQuery = setTimeout(() => {
                        Axios.get(url).then((res) => {
                            if (res.data) {
                                this.data = res.data.data;
                                this.loading = false;
                            } else {
                                this.dataService = [];
                                this.loading = false;
                            }
                        });
                    }, 500);
                }
            },
            viewPdf(data) {
                this.$store.dmcFileManageDetail
                    .open({
                        data: {
                            ...data
                        }
                    });
            },
            downloadFile(item) {
                let parame = `invoice_id=${item.invoice_id}&file_path=${item?.file_path}`
                let url = `/admin/dmc-file-manager/download?${parame}`;
                reloadUrl(url);
            }
        }));
    });
</script>
