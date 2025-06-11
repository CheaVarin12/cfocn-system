@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Order Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        @if ($status == 'dashboard')
                            <button s-click-link="{!! route('admin-dashboard') !!}" style="background-color: #024de3">
                                <i data-feather="arrow-left"></i>
                                <span>Back</span>
                            </button>
                        @else
                            <div class="menu-item {!! Request::is('admin/work-order/order/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-order-list', 1) !!}">
                                Active</div>
                            <div class="menu-item {!! Request::is('admin/work-order/order/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-order-list', 2) !!}">
                                Disable</div>
                        @endif
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}" 
                            autocomplete="off">
                            <i data-feather="filter"></i>
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
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                    </form>
                    @can('work-order-order')
                        <button class="btn-create" s-click-link="{!! route('admin-order-create') !!}">
                            <i data-feather="plus-circle"></i>
                            <span>Create Order</span>
                        </button>
                    @endcan
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.work-order.order.table')
        </div>
    </div>
    @include('admin::pages.work-order.order.createInvoice.created')
    @include('admin::pages.work-order.order.createInvoice.createSaled')
@stop
@section('script')
    <script>
        Alpine.data('XData', () => ({
            init() {},
            createInvoiceDialog(data) {
                if (data?.type_id != 2) {
                    invoiceCreate({
                        active: true,
                        data: data
                    });
                } else {
                    invoiceCreateSale({
                        active: true,
                        data: data
                    });
                }
            }
        }));
    </script>
@stop
