@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="xCustomerReport">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Customer DMC Report'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row"></div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row w100">
                            <input type="text" name="from_date" placeholder="Start Date" id="fromDate"
                                autocomplete="off" value="{!! request('from_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="form-row w100">
                            <input type="text" name="to_date" placeholder="End Date" id="toDate" autocomplete="off"
                                value="{!! request('to_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="form-row">
                            <select style="width: 200px" name="search_project">
                                <option value="">Select Project ...</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ request('search_project') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="check" x-model="check">
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                        <button type="button" class="btn-create bg-info" @click="importCustomerDialog()">
                            <i data-feather="arrow-up-circle"></i>
                            <span>Import Excel</span>
                        </button>
                        @can('report-customer-view')
                            <button type="submit" class="btn-create bg-success" @click=" check = 'export'">
                                <i data-feather="arrow-down-circle"></i>
                                <span>Export Excel</span>
                            </button>
                        @endcan
                    </form>
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.report.customer_dmc.table')
            @if (count($data) > 0)
                <div class="footerAction">
                    <div class="action">
                        <div class="left">
                        </div>
                        <div class="right">
                            <button type="button" @click="dmcSubmit()">
                                <i class='bx bx-send bx-tada-hover'></i>
                                <span>DMC Submit</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <template x-if="submitLoading">
            @include('admin::components.spinner')
        </template>
        @include('admin::pages.report.customer_dmc.import')
        @include('admin::pages.report.customer_dmc.edit')
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function() {
            $('#fromDate').datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: '-10:+10',
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#toDate').datepicker('option', 'minDate', select)
                }
            });
            $('#toDate').datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: '-10:+10',
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#fromDate').datepicker('option', 'maxDate', select)
                }
            });
        });
    </script>
    <script>
        Alpine.data('xCustomerReport', () => ({
            search: '',
            excel: '',
            action: '',
            check: '',
            checkData: true,
            typeSearch: 'date',
            checkProject: '',
            submitLoading: false,
            formData: {
                check: @json(request('check')),
                search_project: @json(request('search_project')),
            },
            connectonServer: {
                status: false,
                message: null
            },
            dataItem: null,
            init() {
                let conn_status = @json($server_connection_status);
                this.connectonServer.status = conn_status == 'login_success' ? true : false;
                this.connectonServer.message = conn_status;
            },
            importCustomerDialog() {
                importCustomer({
                    active: true,
                    title: "Import Customer",
                    config: {
                        width: "50%",
                    },
                    afterClose: (res) => {
                        let currentFullUrl = '{!! url()->full() !!}';
                        reloadUrl(currentFullUrl);
                    }
                });
            },
            async editInformationDialog(data) {
                await this.getDataById(data.id);
                editDialog({
                    active: true,
                    title: `Edit Customer Information (${this.dataItem?.dmc_customer?.customer_code})`,
                    data: {
                        ...this.dataItem,
                    },
                    config: {
                        width: "70%",
                    },
                    afterClose: (res) => {
                        let idElement = `#row-id${res.data.data.id}`;
                        $(`${idElement} td:nth-child(2)`).text(this.formatDate(res.data.data
                            .register_date));
                        $(`${idElement} td:nth-child(3)`).text(res.data.data.customer_code);
                        $(`${idElement} td:nth-child(4)`).text(res.data.data.customer_name);
                        $(`${idElement} td:nth-child(5)`).text(this.formatDate(res.data.data
                        .po_date));
                        $(`${idElement} td:nth-child(6)`).text(res.data.data.po_number);
                        $(`${idElement} td:nth-child(7)`).text(this.formatDate(res.data.data
                        .pac_date));
                        $(`${idElement} td:nth-child(8)`).text(res.data.data.pac_number);
                        $(`${idElement} td:nth-child(9)`).text(res.data.data.customer_address);
                        $(`${idElement} td:nth-child(10)`).text(res.data.data.service_type);
                        $(`${idElement} td:nth-child(11)`).text(res.data.data.type);
                        $(`${idElement} td:nth-child(12)`).text(res.data.data.qty_cores);
                        $(`${idElement} td:nth-child(13)`).text(res.data.data.length);
                        $(`${idElement} td:nth-child(14)`).text(res.data.data.status == 1 ?
                            'Active' : 'Deactive');
                        $(`${idElement} td:nth-child(13)`).text(this.formatDate(res.data.data
                            .inactive_date));
                    }
                });
            },
            dmcSubmit() {
                this.$store.confirmDialog.open({
                    data: {
                        title: "Message",
                        message: "Are you sure want to submit DMC ?",
                        btnClose: "Close",
                        btnSave: "Yes",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.submitLoading = true;
                            let formData = this.formData;
                            formData.check = "submitDMC";
                            let url = `{{ route('admin-report-customer-dmc-list') }}`;
                            Axios.get(url, {
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                },
                                params: {
                                    ...formData
                                }
                            }).then(res => {
                                this.submitLoading = false;
                                let pageNumber = @json(request('page'));
                                if (res.data.message == "unsuccess") {
                                    let con_message = res.data.connection_status ==
                                        "unable_to_connect" ? "unable to connect" :
                                        "problem lose or time out try again";
                                    this.$store
                                        .DMCSubmitStatusDialog
                                        .open({
                                            data: {
                                                title: "Lose Connection",
                                                message: `Server ${con_message} !`,
                                                btnClose: "Close",
                                                btnSave: "Yes",
                                            }
                                        });
                                } else if (res.data.message == "success") {
                                    window.location.href = `{!! url()->full() !!}`;
                                }
                            }).catch(error => {
                                this.submitLoading = false;
                            });
                        }
                    }
                });
            },
            async fetchData(url, callback, body = null) {
                await fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        },
                        body: body,
                    })
                    .then(response => response.json())
                    .then(response => {
                        callback(response);
                    })
                    .catch((e) => {})
                    .finally(async (res) => {});
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                const year = date.getFullYear();
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov",
                    "Dec"
                ];
                const month = monthNames[date.getMonth()];
                const day = String(date.getDate()).padStart(2, '0');
                return `${day}/${month}/${year}`;
            },


            async getDataById(id) {
                let url = `{{ route('admin-report-customer-dmc-get-by-id') }}`;
                await Axios.get(url, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    params: {
                        id: id,
                    }
                }).then(res => {
                    this.dataItem = res.data;
                })
            },
        }));
    </script>
@stop
