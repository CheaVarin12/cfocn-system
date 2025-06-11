@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="customer">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Customer Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/customer/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-customer-list', 1) !!}">
                            Active</div>
                        <div class="menu-item {!! Request::is('admin/customer/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-customer-list', 2) !!}">
                            Disable</div>
                        <div class="menu-item {!! Request::is('admin/customer/list/trash') ? 'active' : '' !!}" s-click-link="{!! route('admin-customer-list', 'trash') !!}">
                            Trash</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-success">
                            <i data-feather="search"></i>
                            <span>Search</span>
                        </button>
                    </form>
                    @can('customer-create')
                        <button class="btn-create" s-click-link="{!! route('admin-customer-create') !!}">
                            <i data-feather="plus-circle"></i>
                            <span>Create Customer</span>
                        </button>
                    @endcan
                    @can('customer-excel-export')
                        <button class="btn-create bg-success" s-click-link="{!! route('admin-customer-export-customer-excel') !!}" :disabled="checkData">
                            <i data-feather="arrow-down-circle"></i>
                            <span>Export Excel</span>
                        </button>
                    @endcan
                    <button class="btn-create bg-info" @click="importCustomerDialog()">
                        <i data-feather="arrow-up-circle"></i>
                        <span>Import Excel</span>
                    </button>
                    <button s-click-link="{!! url()->current() !!}">
                        <i data-feather="refresh-ccw"></i>
                        <span>Reload</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.customer.table')
        </div>
        @include('admin::pages.customer.history')
        @include('admin::pages.customer.import')
    </div>
@stop

@section('script')
    <script>
        Alpine.data('customer', () => ({
            checkData: true,
            init() {
                if (@json($data->count() > 0)) {
                    this.checkData = false;
                }
            },
            detailDialog(data) {
                this.$store.detail.open({
                    data: {
                        ...data
                    }
                });
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
        }));
    </script>
@stop
