@extends('admin::shared.layout')
@section('layout')
    {{ HTML::style('admin-public/css/custom_style/dropdown-select.css') }}
    {{ HTML::style('admin-public/css/custom_style/report-detail.css') }}
    <div class="content-wrapper" x-data="fttxData" style="height: 94vh;overflow: unset;">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Fttx Managerment'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter">
                        <input type="hidden" name="check" x-model="formData.check" value="{!! request('check') !!}">
                        <div class="form-row iconAc">
                            <input type="text" name="search" x-model="formData.search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}">
                            <button type="button">
                                <i data-feather="info"></i>
                                <div class="popUpBtntext">
                                    <div class="popUpText">
                                        <label>Search Keyword :</label>
                                        <p>Work order isp</p>
                                        <p>Work order cfocn</p>
                                        <p>Subscriber no</p>
                                        <p>Isp name</p>
                                        <p>team install</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto" @click="search()">
                            <i data-feather="search"></i>
                        </button>
                    </form>

                    <button class="btn-create" @click="showDialogHideSearch('show')" style="background-color: #0d6efd;">
                        <i data-feather="filter"></i>
                        <span>Filters</span>
                    </button>
                    <button class="btn-create bg-success" @click="exportExcel()">
                        <i data-feather="arrow-down-circle"></i>
                        <span>Export Excel</span>
                    </button>
                    @can('fttx-create')
                        <button class="btn-create bg-info" @click="importDialog()">
                            <i data-feather="arrow-up-circle"></i>
                            <span>Import</span>
                        </button>
                        <button type="button" class="btn-create" @click="storeDialog()">
                            <i data-feather="plus-circle"></i>
                            <span>Create</span>
                        </button>
                    @endcan
                    <button @click="viewExpire()" class="minWithAuto work-order-expire">
                        <div>{{ $totalWorkOrderExpire < 100 ? $totalWorkOrderExpire : '99+' }}</div>
                        Work Order Expire
                    </button>
                    <button style="justify-content: center;" class="btn-create bg-success"
                        s-click-link="{{ asset('template-upload-fttx/fttx-import-form-template.xlsx') }}"
                        title="Download Template Upload">
                        <i data-feather="arrow-down-circle"></i>
                    </button>
                    <button s-click-link="{!! url()->current() !!}">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                    <form action="{{ route('admin-fttx-save-column') }}" method="POST">
                        @csrf
                        <div class="form-body">
                            <div class="form-row row-search">
                                <button style="height: 35px;" type="button" class="minWithAuto"
                                    @click="showHideDropdown('column')">
                                    <span>Show/Hide Column</span>
                                </button>
                                <div class="dropdown" x-show="showColumnDropdown">
                                    <ul style="height: 75vh;right:0;width:500px;">
                                        <li class="fttx-li" style=" white-space: nowrap;">
                                            <input id="select-all-columns" type="checkbox" onclick="toggleSelectAll(this)" style="width: 25px !important;" >
                                            <span>Select All</span>
                                        </li>
                                        @foreach ($columnFttx as $value)
                                            <li style=" white-space: nowrap;" class="fttx-li">
                                                <input name="column[]" type="checkbox" value="{{ $value->id }}"
                                                    {{ $value->status == 1 ? 'checked' : '' }} style="width: 25px !important;">
                                                <span>{{ $value->name }}</span>
                                            </li>
                                        @endforeach
                                        <li>
                                            <div style="height: 35px;margin-bottom: 12px;">
                                                <button
                                                    style="float: right;background: rgba(0, 0, 255, 0.6784313725);color:white;"
                                                    type="submit">Save</button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.fttx.fttx.table')
            @include('admin::pages.fttx.fttx.store')
            @include('admin::pages.fttx.fttx.import-data.import')
            @include('admin::pages.fttx.fttx.renewal')
            @include('admin::pages.fttx.fttx.report-detail.index')
            @include('admin::pages.fttx.fttx.search-dialog')
            @if (count($data) > 0)
                <div class="table-footer">
                    @include('admin::components.pagination', ['paginate' => $data])
                </div>
            @endif
        </div>
        <template x-if="submitLoading">
            @include('admin::components.spinner')
        </template>
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function() {
            $('#startPaymentDate, #endPaymentDate').on('change', function() {
                let startDate = $('#startPaymentDate').val();
                let endDate = $('#endPaymentDate').val();
                if (startDate && endDate && startDate > endDate) {
                    $('#endPaymentDate').val(startDate);
                }
                $('#endPaymentDate').attr('min', startDate);
            });
            $('#startNewInstallDate, #endNewInstallDate').on('change', function() {
                let startDate = $('#startNewInstallDate').val();
                let endDate = $('#endNewInstallDate').val();
                if (startDate && endDate && startDate > endDate) {
                    $('#endNewInstallDate').val(startDate);
                }
                $('#endNewInstallDate').attr('min', startDate);
            });

            $('#startDismantleDate, #endDismantleDate').on('change', function() {
                let startDate = $('#startDismantleDate').val();
                let endDate = $('#endDismantleDate').val();
                if (startDate && endDate && startDate > endDate) {
                    $('#endDismantleDate').val(startDate);
                }
                $('#endDismantleDate').attr('min', startDate);
            });
            $('#startDeadlineDate, #endDeadlineDate').on('change', function() {
                let startDate = $('#startDeadlineDate').val();
                let endDate = $('#endDeadlineDate').val();
                if (startDate && endDate && startDate > endDate) {
                    $('#endDeadlineDate').val(startDate);
                }
                $('#endDeadlineDate').attr('min', startDate);
            });
        });

        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('input[name="column[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }
    </script>
    <script>
        Alpine.data('fttxData', () => ({
            submitLoading: false,
            showPosSpeedDropdown: false,
            showStatusDropdown: false,
            showColumnDropdown: false,
            posSpeed: @json($posSpeed),
            statusOptions: Object.values(@json(config('dummy.fttx_status'))),
            formData: {
                check: @json(request('check')),
                search: @json(request('search')),
                pos_speed_id: @json(request('pos_speed_id')) ?
                    @json(request('pos_speed_id')).toString().split(',').map(id => parseInt(id)) : [],
                status: @json(request('status')) != 'all' ? @json(request('status')) ?
                    @json(request('status')).toString().split(',').map(id => parseInt(id)) : [] : ['all'],
                start_completed_date: @json(request('start_completed_date')),
                end_completed_date: @json(request('end_completed_date')),
                start_payment_date: @json(request('start_payment_date')),
                end_payment_date: @json(request('end_payment_date')),
                start_dismantle_date: @json(request('start_dismantle_date')),
                end_dismantle_date: @json(request('end_dismantle_date')),
                start_deadline_date: @json(request('start_deadline_date')),
                end_deadline_date: @json(request('end_deadline_date')),
                expire: @json(request('expire')),
                check_renewal_all: null,
            },
            init() {},
            storeDialog(data) {
                storeFttxDialog({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "70%",
                    }
                });
            },
            importDialog() {
                importData({
                    active: true,
                    title: "Import Fttx Data",
                    config: {
                        width: "50%",
                    },
                    afterClose: (res) => {
                        let currentFullUrl = '{!! url()->full() !!}';
                        reloadUrl(currentFullUrl);
                    }
                });
            },
            reportDetailDialog(data) {
                reportDetailDialog({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "97%",
                    }
                });
            },
            renewalDialog(data) {
                renewalFttxDialog({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "50%",
                    }
                });
            },
            exportExcel() {
                const check = 'export';
                const search = this.formData.search ?? '';
                const pos_speed_id = this.formData.pos_speed_id ?? '';
                const status = this.formData.status ?? '';
                const start_completed_date = this.formData.start_completed_date ?? '';
                const end_completed_date = this.formData.end_completed_date ?? '';
                const start_payment_date = this.formData.start_payment_date ?? '';
                const end_payment_date = this.formData.end_payment_date ?? '';
                const start_dismantle_date = this.formData.start_dismantle_date ?? '';
                const end_dismantle_date = this.formData.end_dismantle_date ?? '';
                const start_deadline_date = this.formData.start_deadline_date ?? '';
                const end_deadline_date = this.formData.end_deadline_date ?? '';
                const expire = this.formData.expire ?? '';

                const url = `{{ route('admin-fttx-list') }}?check=${check}&
                                                            search=${search}&
                                                            pos_speed_id=${pos_speed_id}&
                                                            status=${status}&
                                                            start_completed_date=${start_completed_date}&
                                                            end_completed_date=${end_completed_date}&
                                                            start_payment_date=${start_payment_date}&
                                                            end_payment_date=${end_payment_date}&
                                                            start_dismantle_date=${start_dismantle_date}&
                                                            end_dismantle_date=${end_dismantle_date}&
                                                            start_deadline_date=${start_deadline_date}&
                                                            end_deadline_date=${end_deadline_date}&
                                                            expire=${expire}`;
                window.location.href = url;
            },
            viewExpire() {
                this.formData.expire = true;
                this.search();
            },
            renewalAll() {
                this.$store.confirmDialog.open({
                    data: {
                        title: "Message",
                        message: `Are you sure to renewal ({{ $totalWorkOrderExpire }})?`,
                        btnClose: "Close",
                        btnSave: "Renewal All",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.submitLoading = true;
                            this.formData.check_renewal_all = true;
                            this.search();
                        }
                    }
                });
            },
            updateStatus(status, type) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "Message",
                        message: `Are you sure to update status from '${type}' to 'Active' ?`,
                        btnClose: "Close",
                        btnSave: "Update",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            const url = `{{ route('admin-fttx-update-status') }}?status=${status}`;
                            window.location.href = url;
                        }
                    }
                });
            },
            search() {
                const check = '';
                const search = this.formData.search ?? '';
                const pos_speed_id = this.formData.pos_speed_id ?? '';
                const status = this.formData.status ?? '';
                const start_completed_date = this.formData.start_completed_date ?? '';
                const end_completed_date = this.formData.end_completed_date ?? '';
                const start_payment_date = this.formData.start_payment_date ?? '';
                const end_payment_date = this.formData.end_payment_date ?? '';
                const start_dismantle_date = this.formData.start_dismantle_date ?? '';
                const end_dismantle_date = this.formData.end_dismantle_date ?? '';
                const start_deadline_date = this.formData.start_deadline_date ?? '';
                const end_deadline_date = this.formData.end_deadline_date ?? '';
                const expire = this.formData.expire ?? '';
                const check_renewal_all = this.formData.check_renewal_all ?? '';

                const url = `{{ route('admin-fttx-list') }}?check=${check}&
                                                            search=${search}&
                                                            pos_speed_id=${pos_speed_id}&
                                                            status=${status}&
                                                            start_completed_date=${start_completed_date}&
                                                            end_completed_date=${end_completed_date}&
                                                            start_payment_date=${start_payment_date}&
                                                            end_payment_date=${end_payment_date}&
                                                            start_dismantle_date=${start_dismantle_date}&
                                                            end_dismantle_date=${end_dismantle_date}&
                                                            start_deadline_date=${start_deadline_date}&
                                                            end_deadline_date=${end_deadline_date}&
                                                            expire=${expire}&
                                                            check_renewal_all=${check_renewal_all}`;
                window.location.href = url;
            },
            showHideDropdown(type = null) {
                if (type) {
                    if (type === 'pos_speed_id') {
                        this.showPosSpeedDropdown = !this.showPosSpeedDropdown;
                    } else if (type === 'status') {
                        this.showStatusDropdown = !this.showStatusDropdown;
                    } else if (type === 'column') {
                        this.showColumnDropdown = !this.showColumnDropdown;
                    }
                }
            },
            get selectedPosSpeed() {
                const posSpeed = @json($posSpeed);
                if (!this.formData.pos_speed_id.length) {
                    return '';
                }
                return this.formData.pos_speed_id
                    .map(id => {
                        const matched = posSpeed.find(item => item.id == id);
                        return matched ? matched.split_pos : null;
                    })
                    .filter(Boolean)
                    .join(', ');
            },
            get selectedStatus() {
                const status = Object.values(
                    @json(config('dummy.fttx_status')));
                if (!this.formData.status.length) {
                    return '';
                }
                return this.formData.status
                    .map(id => {
                        const matched = status.find(item => item.key == id);
                        return matched ? matched.text : null;
                    })
                    .filter(Boolean)
                    .join(', ');
            },
            toggleSelectAllPosSpeed(event) {
                if (event.target.checked) {
                    this.formData.pos_speed_id = this.posSpeed.map(item => item.id);
                } else {
                    this.formData.pos_speed_id = [];
                }
            },
            toggleSelectAllStatus(event) {
                if (event.target.checked) {
                    this.formData.status = this.statusOptions.map(item => item.key);
                } else {
                    this.formData.status = [];
                }
            },

            showDialogHideSearch(type) {
                let $dialog = $("#myDialog");
                if (type == 'show') {
                    $dialog[0].showModal();
                } else {
                    $dialog[0].close();
                }
            },
        }));
    </script>
@stop
