@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="AR">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Report Income'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/report/income/list') ? 'active' : '' !!}">Data</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        {{-- <div class="form-row">
                            <select style="width: 200px" name="search_project">
                                <option value="">Select Project ...</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('search_project') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="form-row">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
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
                        <button mat-flat-button type="submit" class="btn-create bg-success" @click=" check = 'search'">
                            <i data-feather="search"></i>
                            <span>Search</span></span>
                        </button>
                        @can('report-income-view')
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
            @include('admin::pages.report.income.table')
        </div>
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
        Alpine.data('AR', () => ({
            search: '',
            excel: '',
            action: '',
            check: '',
            checkData: true,
            typeSearch: 'date',
            checkProject: '',
            init() {},
            checkProjectdata() {
                if (this.checkProject == '') {
                    this.checkData = true;
                } else {
                    this.checkData = false;
                }
            }
        }));
    </script>
@stop
