@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'FTTH Service Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/ftth-service/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-ftth-service-list', 1) !!}">
                            Active
                        </div>
                        <div class="menu-item {!! Request::is('admin/ftth-service/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-ftth-service-list', 2) !!}">
                            Disable
                        </div>
                        <div class="menu-item {!! Request::is('admin/ftth-service/list/trash') ? 'active' : '' !!}" s-click-link="{!! route('admin-ftth-service-list', 'trash') !!}">
                            Trash
                        </div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}"
                                autocomplete="off">
                            <i data-feather="filter"></i>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-success">
                            <i data-feather="search"></i>
                            <span>Search</span>
                        </button>
                    </form>
                    @can('ftth-service-create')
                        <button class="btn-create" s-click-link="{!! route('admin-ftth-service-create') !!}">
                            <i data-feather="plus-circle"></i>
                            <span>Create Service</span>
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
            @include('admin::pages.ftth_service.table')
        </div>
    </div>
@stop
