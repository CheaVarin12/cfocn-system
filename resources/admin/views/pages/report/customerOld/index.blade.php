@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="xCustomerReport">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Report Customer'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/report/customer/list') ? 'active' : '' !!}">Data</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
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
                        <div class="form-row w80">
                            <input type="text" name="from_date" placeholder="From Date" value="{!! isset($from_date) && $from_date ? $from_date : request('from_date') !!}"
                                id="from_date" autocomplete="off">
                            <i data-feather="calendar" id="from_date"></i>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="to_date" placeholder="To Date" value="{!! $to_date ? $to_date : request('to_date') !!}"
                                id="to_date" autocomplete="off">
                            <i data-feather="calendar"></i>
                        </div>
                        {{-- check search ro export --}}
                        <input type="hidden" name="check" x-model="check">
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto"
                            @click=" check = 'search'">
                            <i data-feather="search"></i>
                        </button>
                        @can('report-customer-view')
                            <button type="submit" class="btn-create bg-success" @click=" check = 'export'">
                                <i data-feather="arrow-down-circle"></i>
                                <span>Excel</span>
                            </button>
                        @endcan
                    </form>
                    <button s-click-link="{!! url()->current() !!}">
                        <i data-feather="refresh-ccw"></i>
                        <span>Reload</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.report.customer.table')
            @if (count($data) > 0)
                <div class="footerAction">
                    <div class="action">
                        <div class="left">
                            {{-- <div class="imgIcon" x-show="connectonServer.message">
                            <div class="icon" :class="connectonServer.status ? 'con' : 'lose'">
                                <div class="round"></div>
                                <label x-text="connectonServer.status ? 'connection':'lose connection'"></label>
                            </div>
                        </div> --}}
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
        @include('admin::pages.report.customer.detail')
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
                minDate: `{{ isset($from_date) && $from_date ? $from_date : 0 }}`,
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
                search_project: @json(request('search_project')),
                search: @json(request('search')),
                from_date: @json(request('from_date')),
                to_date: @json(request('to_date')),
                check: @json(request('check')),
            },
            connectonServer: {
                status: false,
                message: null
            },
            init() {
                let conn_status = @json($server_connection_status);
                this.connectonServer.status = conn_status ==
                    'login_success' ? true : false;
                this.connectonServer.message = conn_status;
            },
            checkProjectdata() {
                if (this.checkProject == '') {
                    this.checkData = true;
                } else {
                    this.checkData = false;
                }
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
                            let url = '/admin/report/customer/list'
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
                                if (res.data.message ==
                                    "unsuccess") {
                                    let con_message = res.data
                                        .connection_status ==
                                        "unable_to_connect" ?
                                        "unable to connect" :
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
                                } else if (res.data.message ==
                                    "success") {
                                    window.location.href = `{!! url()->full() !!}`;
                                }
                            }).catch(error => {
                                this.submitLoading = false;
                            })
                            // let nameFile = 'invoice_number_' + moment().format('DD_MM_YYYY_H_mm_ss');
                            // var blockIdHtml = document.getElementById('printableArea');
                            // let dataTimeOut = null;
                            // clearTimeout(dataTimeOut);
                            // dataTimeOut = setTimeout(() => {
                            //     $htmlGenerateToPdf(blockIdHtml, nameFile, (file) => {
                            //         var formData = new FormData();
                            //         formData.append("file", file);
                            //         formData.append("invoice_id", this.data.id);
                            //         formData.append("typeBtnStatus", typeBtnStatus);
                            //         Axios.post('/admin/invoice/dmc-submit',
                            //             formData, {
                            //                 headers: {
                            //                     'Content-Type': 'multipart/form-data',
                            //                 }
                            //             }).then(res => {
                            //             this.submitLoading = false;
                            //             let pageNumber =
                            //                 @json(request('page'));
                            //             if (res.data.message ==
                            //                 "unsuccess") {
                            //                 let con_message = res.data
                            //                     .connection_status ==
                            //                     "unable_to_connect" ?
                            //                     "unable to connect" :
                            //                     "problem (lose or time out) try again";
                            //                 this.$store
                            //                     .DMCSubmitStatusDialog
                            //                     .open({
                            //                         data: {
                            //                             title: "Lose Connection",
                            //                             message: `Server ${con_message} !`,
                            //                             btnClose: "Close",
                            //                             btnSave: "Yes",
                            //                         }
                            //                     });
                            //             } else if (res.data.message ==
                            //                 "success") {
                            //                 this.dialogClose();
                            //                 window.location.href =
                            //                     `{!! url()->current() !!}` +
                            //                     `?page=${pageNumber}`;
                            //             }
                            //         }).catch(error => {
                            //             this.submitLoading = false;
                            //         })
                            //     });
                            // }, 500);
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
            detailDialog(data) {
                this.$store
                    .reportCustomerDetail
                    .open({
                        data: {
                            ...data
                        }
                    });
            }
        }));
    </script>
@stop
