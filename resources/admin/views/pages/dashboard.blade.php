@extends('admin::shared.layout')
@section('layout')
    <div class="dashboard-admin" x-data="XDataDashboard">
        <div class="dashboard-bg"></div>
        <div class="dashboard-wrapper">
            <div class="dashboard-body">
                <div class="filter">
                    <h3>
                        @lang('dashboard.dashboard')
                    </h3>
                    <form id="FilterForm" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row formRow2">
                            <input type="text" id="fromDate" name="from_date" value="{!! $firstMonthDay ? $firstMonthDay : request('from_date') !!}"
                                placeholder="@lang('dashboard.filter.from_date')" autocomplete="off">
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="form-row formRow2">
                            <input type="text" id="toDate" name="to_date" value="{!! $lastMonthDay ? $lastMonthDay : request('to_date') !!}"
                                placeholder="@lang('dashboard.filter.to_date')" autocomplete="off">
                            <i data-feather="calendar"></i>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-success">
                            <i data-feather="search"></i>
                        </button>
                        <button type="button" s-click-link="{!! url()->current() !!}">
                            <i data-feather="refresh-ccw"></i>
                        </button>
                    </form>
                </div>
                <div class="dashboard-list">
                    <div class="dashboard-row">
                        @foreach ($dashboard as $item)
                            <div class="item {{ $item->custom_class }}">
                                <div class="item-body">
                                    <div class="left">
                                        <span>{{ $item->name }}</span>
                                        @if (isset($item?->type_value) && $item->type_value == 'dollar')
                                            <h3> ${{ number_format($item->value, 2) }}</h3>
                                        @else
                                            <h3> {{ $item->value }}</h3>
                                        @endif
                                    </div>
                                    <div class="right">
                                        <i class='bx {{ $item->icon ?? 'bx-user' }}'></i>
                                    </div>
                                </div>
                                {{-- <div class="item-footer" s-click-link="{{ $item->link }}">
                                    <span>Detail</span>
                                    <i data-feather="arrow-right"></i>
                                </div> --}}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="dashboard-footer"></div>
            </div>
        </div>
    @stop
    @section('script')
        <script>
            $("#fromDate").datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                gotoCurrent: true,
                yearRange: "-50:+0",
                onSelect: function(selected) {
                    $("#toDate").datepicker("option", "minDate", selected)
                }
            });
            $("#toDate").datepicker({
                minDate: `{{ isset($firstMonthDay) && $firstMonthDay ? $firstMonthDay : 0 }}`,
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                gotoCurrent: true,
                yearRange: "-50:+0",
                onSelect: function(selected) {
                    $("#fromDate").datepicker("option", "maxDate", selected)
                }
            });
        </script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('XDataDashboard', () => ({
                    init() {},
                }));
            });
        </script>
    @endsection
