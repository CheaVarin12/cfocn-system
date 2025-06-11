@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="xCustomerReport">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Report Old Customer'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row"></div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row iconAc">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}" autocomplete="off">
                            <div class="div">
                                <i data-feather="info"></i>
                                <div class="popUpBtntext">
                                    <div class="popUpText">
                                        <label>Search Keyword :</label>
                                        <p>Customer ID</p>
                                        <p>Customer Name</p>
                                        <p>PO No</p>
                                        <p>PAC No</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <input type="text" name="from_date" placeholder="From Date" value="{!! request('from_date') !!}"
                                id="from_date" autocomplete="off">
                            <i data-feather="calendar" id="from_date"></i>
                        </div>
                        <div class="form-row">
                            <input type="text" name="to_date" placeholder="To Date" value="{!! request('to_date') !!}"
                                id="to_date" autocomplete="off">
                            <i data-feather="calendar"></i>
                        </div>
                        {{-- check search ro export --}}
                        <input type="hidden" name="check" x-model="check">
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                        <button type="button" class="btn-create bg-success" @click="importCustomerDialog()">
                            <i data-feather="arrow-up-circle"></i>
                            <span>Import Excel</span>
                        </button>
                    </form>
                    <button s-click-link="{!! url()->current() !!}">
                        <i data-feather="refresh-ccw"></i>
                        <span>Reload</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.report.old_customer.table')
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
        @include('admin::pages.report.old_customer.import')
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function() {
            $("#from_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                gotoCurrent: true,
                yearRange: "-50:+0",
                onSelect: function(selected) {
                    $("#to_date").datepicker("option", "minDate", selected)
                }
            });
            $("#to_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                gotoCurrent: true,
                yearRange: "-50:+0",
                onSelect: function(selected) {
                    $("#from_date").datepicker("option", "maxDate", selected)
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
                search: @json(request('search')),
                check: @json(request('check')),
            },
            connectonServer: {
                status: false,
                message: null
            },
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
                            let url = `{{ route('admin-report-old-customer-list') }}`;
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
                                    let con_message = res.data.connection_status == "unable_to_connect" ? "unable to connect" : "problem lose or time out try again";
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
        }));
    </script>
@stop
