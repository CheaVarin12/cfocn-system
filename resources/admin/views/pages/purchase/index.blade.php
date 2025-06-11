@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'P A C Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        @if ($status == 'dashboard')
                            <button s-click-link="{!! route('admin-dashboard') !!}" style="background-color: #024de3">
                                <i data-feather="arrow-left"></i>
                                <span>Back</span>
                            </button>
                        @else
                            <div class="menu-item {!! Request::is('admin/purchase/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-purchase-list', 1) !!}">
                                Approved</div>
                            <div class="menu-item {!! Request::is('admin/purchase/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-purchase-list', 2) !!}">
                                Inactive</div>
                            <div class="menu-item {!! Request::is('admin/purchase/list/3') ? 'active' : '' !!}" s-click-link="{!! route('admin-purchase-list', 3) !!}">
                                Terminate</div>
                            <div class="menu-item {!! Request::is('admin/purchase/list/all') ? 'active' : '' !!}" s-click-link="{!! route('admin-purchase-list', 'all') !!}">
                                All</div>
                            <div class="menu-item {!! Request::is('admin/purchase/list/old') ? 'active' : '' !!}" s-click-link="{!! route('admin-purchase-list', 'old') !!}">Old</div>
                        @endif
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}">
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
                    @can('purchase-create')
                        <button class="btn-create" s-click-link="{!! route('admin-purchase-create') !!}">
                            <i data-feather="plus-circle"></i>
                            <span>Create</span>
                        </button>
                    @endcan
                    <button class="btn-create bg-success" @click="importPACDialog()">
                        <i data-feather="arrow-up-circle"></i>
                        <span>Import PAC</span>
                    </button>
                    <button class="btn-create bg-info" @click="importPACDetailDialog()">
                        <i data-feather="arrow-up-circle"></i>
                        <span>Import PAC Detail</span>
                    </button>
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.purchase.table')
        </div>
    </div>
    @include('admin::pages.purchase.createInvoice.created')
    @include('admin::pages.purchase.createInvoice.createSaled')
    @include('admin::pages.purchase.import')
    @include('admin::pages.purchase.import_detail')
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
            },
            importPACDialog() {
                importPAC({
                    active: true,
                    title: "Import PAC",
                    config: {
                        width: "50%",
                    },
                    afterClose: (res) => {
                        let currentFullUrl = '{!! url()->full() !!}';
                        reloadUrl(currentFullUrl);  
                    }
                });
            },
            importPACDetailDialog() {
                importPACDetail({
                    active: true,
                    title: "Import PAC Detail",
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
