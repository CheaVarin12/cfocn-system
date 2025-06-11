@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Type Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/type/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-type-list', 1) !!}">
                            Active</div>
                        <div class="menu-item {!! Request::is('admin/type/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-type-list', 2) !!}">
                            Inactive</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search"
                                value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-success">
                            <i data-feather="search"></i>
                            <span>Search</span>
                        </button>
                    </form>
                    @can('service-type-create')
                        <button class="btn-create" s-click-link="{!! route('admin-type-create') !!}">
                            <i data-feather="plus-circle"></i>
                            <span>Create Type</span>
                        </button>
                    @endcan
                    <button s-click-link="{!! url()->current() !!}">
                        <i data-feather="refresh-ccw"></i>
                        <span>Reload</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.type.table')
        </div>
    </div>
@stop
