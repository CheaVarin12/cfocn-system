@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="Report">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Report Revenue'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/report/revenue/list') ? 'active' : '' !!}">Data</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        {{-- <div class="form-row">
                            <select style="width: 200px" name="project_id" x-model="form.project_id">
                                <option value="">Select Project ...</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ request('search_project') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                        {{-- <div class="form-row">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}" x-model="form.search">
                            <i data-feather="filter"></i>
                        </div> --}}
                        {{-- check search ro export --}}
                        {{-- <input type="hidden" name="check" x-model="check"> --}}
                        <div class="form-row w80">
                            <input type="text" name="from_date" placeholder="From Date" value="{!! isset($from_date) && $from_date ? $from_date : request('from_date') !!}"
                                id="from_date" autocomplete="off" x-model="form.form_date" x-ref="form_date">
                            <i data-feather="calendar" id="from_date"></i>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="to_date" placeholder="To Date" value="{!! $to_date ? $to_date : request('to_date') !!}"
                                id="to_date" autocomplete="off" x-model="form.to_date" x-ref="to_date">
                            <i data-feather="calendar"></i>
                        </div>
                        <button mat-flat-button type="button" class="btn-create bg-success" @click="filterData('search')">
                            <i data-feather="search"></i>
                            <span>Search</span></span>
                        </button>
                        {{-- @can('report-sale-journal-excel-export') --}}
                        <button type="button" class="btn-create bg-success" @click="filterDataExportExcel('excel')">
                            <i data-feather="arrow-down-circle"></i>
                            <span>Excel</span>
                        </button>
                        {{-- @endcan --}}
                    </form>
                    <button s-click-link="{!! url()->current() !!}">
                        <i data-feather="refresh-ccw"></i>
                        <span>Reload</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            <template x-if="!loading">
                @include('admin::pages.report.revenue.table')
            </template>
        </div>

        <template x-if="loading">
            @include('admin::components.spinner')
        </template>
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
        Alpine.data('Report', () => ({
            search: '',
            excel: '',
            action: '',
            check: '',
            checkData: true,
            checkProject: '',
            tab: 1,
            form: {
                project_id: '',
                search: '',
                type: '',
                form_date: '',
                to_date: ''
            },
            totalAmount: 0,
            totalLicenseFee: 0,
            data: [],
            async init() {
                this.form.form_date = @json($from_date);
                this.form.to_date = @json($to_date);
                await this.filterData();
            },
            onChangeTab(tab) {
                this.tab = tab;
            },
            async filterData(type = '') {
                this.form.type = type;
                this.form.form_date = this.$refs.form_date.value;
                this.form.to_date = this.$refs.to_date.value;
                this.loading = true;
                setTimeout(async () => {
                    let url = "/admin/report/revenue/fetchData?" + new URLSearchParams(this
                        .form);
                    await this.fetchData(url, this.form, (res) => {
                        res.data.forEach(valItem => {
                            const {
                                dataDB,
                                dataDefault
                            } = valItem;
                            let totalAmount = 0;
                            let totalLicenseFee = 0;
                            dataDefault.map(val => {
                                let findData = dataDB.find(i => i
                                    .project_child_id == val
                                    .project_child_id);
                                if (findData) {
                                    val.amount = findData.amount;
                                    val.licenseFeePercentage = findData
                                        .licenseFeePercentage;
                                    val.licenseFeeAmount = findData
                                        .licenseFeeAmount;
                                    val.licenseFeePercentage = findData
                                        .licenseFeePercentage;
                                    val.project_name = findData
                                        .ProjectItem.name;
                                    valItem.project_name = findData
                                        .ProjectItem.name;
                                    totalAmount += findData.amount;
                                    totalLicenseFee += findData
                                        .licenseFeeAmount;
                                }
                            });
                            valItem.totalAmount = totalAmount;
                            valItem.totalLicenseFee = totalLicenseFee;
                            this.totalAmount += totalAmount;
                            this.totalLicenseFee += totalLicenseFee;
                        });
                        this.data = res?.data ?? [];
                        this.loading = false;
                    });
                }, 500);
            },
            async fetchData(url, form, callback) {
                await fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        },
                    })
                    .then(response => response.json())
                    .then(response => {
                        callback(response);
                    })
                    .catch((e) => {})
                    .finally(async (res) => {});
            },
            numberRound(num, decimalPlaces = null) {
                if (!decimalPlaces) {
                    return Math.round(num);
                }
                var p = Math.pow(10, decimalPlaces);
                return Math.round(num * p) / p;
            },
            numberFormat(num) {
                return new Intl.NumberFormat().format(num.toFixed(2));
            },
            filterDataExportExcel(type = '') {
                this.form.form_date = this.$refs.form_date.value;
                this.form.to_date = this.$refs.to_date.value;

                let url =
                    `admin/report/revenue/excel?form_date=${this.form.form_date}&to_date=${this.form.to_date}`;
                window.location.href = `{{ url('${url}') }}`;
            }
        }));
    </script>
@stop
