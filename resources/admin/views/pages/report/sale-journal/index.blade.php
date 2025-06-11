@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="Report">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Report Sale Journal'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/report/sale-journal/list') ? 'active' : '' !!}">Data</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row iconAc">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}">
                            <button type="button">
                                <i data-feather="info"></i>
                                <div class="popUpBtntext">
                                    <div class="popUpText">
                                        <label>Search Keyword :</label>
                                        <p>Invoice number</p>
                                        <p>Customer (name, code)</p>
                                    </div>
                                </div>
                            </button>
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
                                id="from_date" autocomplete="off" x-init="fromDate()" x-ref="from_date">
                            <i data-feather="calendar" id="from_date"></i>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="to_date" placeholder="To Date" value="{!! $to_date ? $to_date : request('to_date') !!}"
                                id="to_date" autocomplete="off" x-init="toDate()" x-ref="to_date">
                            <i data-feather="calendar"></i>
                        </div>
                        {{-- check search ro export --}}
                        <input type="hidden" name="check" x-model="check">
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto"
                            @click=" check = 'search'">
                            <i data-feather="search"></i>
                        </button>
                        @can('report-sale-journal-view')
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
        <div class="content-body">
            @include('admin::pages.report.sale-journal.table')
        </div>
        @include('admin::pages.report.sale-journal.detail')
    </div>
@stop
@section('script')
    <script>
        Alpine.data('Report', () => ({
            search: '',
            excel: '',
            action: '',
            check: '',
            checkData: true,
            checkProject: '',
            from_date_label: {
                day: null,
                month: null,
                year: null
            },
            to_date_label: {
                day: null,
                month: null,
                year: null
            },
            from_date: null,
            to_date: null,
            init() {
                this.getDateTime();
            },
            fromDate() {
                $("#from_date").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeYear: true,
                    changeMonth: true,
                    gotoCurrent: true,
                    yearRange: "-50:+0",
                    onSelect: (date) => {
                        $("#to_date").datepicker("option", "minDate", date);
                        this.getDateTime();
                    }
                });
            },
            toDate() {
                $("#to_date").datepicker({
                    minDate: `{{ isset($from_date) && $from_date ? $from_date : 0 }}`,
                    dateFormat: 'yy-mm-dd',
                    changeYear: true,
                    changeMonth: true,
                    gotoCurrent: true,
                    yearRange: "-50:+0",
                    onSelect: (date) => {
                        $("#from_date").datepicker("option", "maxDate", date);
                        this.getDateTime();
                    }
                });
            },
            getDateTime() {

                this.from_date = this.$refs.from_date.value
                this.to_date = this.$refs.to_date.value;

                this.from_date_label = this.getObjectFormatDate(this.from_date);
                this.to_date_label = this.getObjectFormatDate(this.to_date);

            },
            getObjectFormatDate(date) {
                let item = {
                    day: date ? this.formatDate(null, date, 'DD') : null,
                    month: date ? this.formatDate(null, date, 'MMM') : null,
                    year: date ? this.formatDate(null, date, 'YYYY') : null,
                };
                return item;
            },
            formatDate(localType = null, date, typeFormat = null) {
                moment.locale(localType ? localType : 'km');
                return date ? moment(date).format(typeFormat) : '';
            },
            detailDialog(data) {
                this.$store
                    .reportSaleJournalDetail
                    .open({
                        data: {
                            ...data
                        }
                    });
            }
        }));
    </script>
@stop
