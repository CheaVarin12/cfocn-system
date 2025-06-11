@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Receipt Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/work-order/receipt/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-receipt-list', 1) !!}">
                            Pending</div>
                        <div class="menu-item {!! Request::is('admin/work-order/receipt/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-receipt-list', 2) !!}">
                            Paid</div>
                        <div class="menu-item {!! Request::is('admin/work-order/receipt/list/all') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-receipt-list', 'all') !!}">
                            All</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row iconAc">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}">
                            <div class="div">
                                <i data-feather="info"></i>
                                <div class="popUpBtntext">
                                    <div class="popUpText">
                                        <label>Search Keyword :</label>
                                        <p>Receipt number</p>
                                        <p>Invoice number</p>
                                        <p>Customer (name, code)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <select name="search_project">
                                <option value="">Select Project ...</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ request('search_project') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="from_date" placeholder="Start Date" id="fromDate"
                                autocomplete="off" value="{!! request('from_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="to_date" placeholder="End Date" id="toDate" autocomplete="off"
                                value="{!! request('to_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        @can('work-order-receipt')
                            <button type="button" class="btn-create" @click="createDialog()">
                                <i data-feather="plus-circle"></i>
                                <span>Create Receipt</span>
                            </button>
                        @endcan
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                    </form>
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.work-order.receipt.table')
        </div>
    </div>
    @include('admin::pages.work-order.receipt.detail.index')
    @include('admin::pages.work-order.receipt.create')
    @include('admin::pages.work-order.receipt.edit')
    @include('admin::pages.work-order.receipt.editStatus')
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
        Alpine.data('XData', () => ({
            init() {},
            invoiceDetailDialog(data) {
                invoiceDetail({
                    active: true,
                    data: data,
                    dmcBtn: false,
                    title: "Receipt Detail",
                    config: {
                        width: "60%",
                    }
                });
            },
            editStatusDialog(item) {
                editStatus({
                    active: true,
                    data: {
                        ...item
                    },
                    title: "Receipt Payment Status",
                    config: {
                        width: "60%",
                    },
                    afterClose: (res) => {
                        let currentFullUrl = '{!! url()->full() !!}';
                        reloadUrl(currentFullUrl);
                    }
                });
            },
            createDialog() {
                create({
                    active: true,
                    data: null,
                    title: "Create Receipt",
                    config: {
                        width: "65%",
                    },
                    afterClose: (res) => {
                        let currentFullUrl = '{!! url()->full() !!}';
                        reloadUrl(currentFullUrl);
                    }
                });
            },
            editDialog(item) {
                edit({
                    active: true,
                    data: {
                        ...item
                    },
                    title: "Receipt Edit",
                    config: {
                        width: "65%",
                    },
                    afterClose: (res) => {
                        let currentFullUrl = '{!! url()->full() !!}';
                        reloadUrl(currentFullUrl);
                    }
                });
            }
        }));
    </script>
@stop
