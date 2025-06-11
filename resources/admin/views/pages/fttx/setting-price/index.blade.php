@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="posSpeedData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Setting Price Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/fttx/setting-price/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-setting-price-list', 1) !!}">
                            Active</div>
                        <div class="menu-item {!! Request::is('admin/fttx/setting-price/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-setting-price-list', 2) !!}">
                            Inactive</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                    </form>
                    @can('fttx-setting-price-create')
                        <button type="button" class="btn-create" @click="storeDialog()">
                            <i data-feather="plus-circle"></i>
                            <span>Create</span>
                        </button>
                    @endcan
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.fttx.setting-price.table')
            @include('admin::pages.fttx.setting-price.create')
        </div>
        <template x-if="submitLoading">
            @include('admin::components.spinner')
        </template>
    </div>
@stop
@section('script')
    <script>
        Alpine.data('posSpeedData', () => ({
            search: '',
            submitLoading: false,
            formData: {
            },
            init() {},

            storeDialog(data) {
                storeSettingPriceDialog({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "80%",
                    }
                });
            },
        }));
    </script>
@stop
