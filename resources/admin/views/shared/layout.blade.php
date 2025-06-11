@extends('admin::index')
@section('index')
    <div class="container">
        <div class="container-wrapper" x-data="{ sidebarOpen: false }">
            <div class="sidebar" x-show="!sidebarOpen">
                @include('admin::shared.sidebar')
            </div>
            <div class="content" x-data={}>
                @yield('layout')
                @include('admin::components.confirm-dialog')
                @include('admin::components.dmc-submit-dialog')
                @include('admin::components.select-option')
            </div>
        </div>
    </div>
@stop
