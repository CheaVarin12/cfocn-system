@extends('admin::shared.layout')
@section('layout')
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            right: 0;
            top: 0;
            color: rgba(0, 0, 0, 0);
            opacity: 0;
            display: block;
            background: blur(0);
            width: 100%;
            height: 100%;
            border-width: thin;
            z-index: 0;
        }

        .excel-body table thead tr th.first-part {
            background-color: #ACB9CA;
        }

        .excel-body table thead tr th.second-part {
            background-color: #C6E0B4;
        }

        .excel-body table tbody tr.list td.white-space-nowrap {
            white-space: normal;
        }
    </style>
    <div class="content-wrapper" x-data="posSpeedData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Pos Speed Managerment'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/fttx/pos-speed/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-pos-speed-list', 1) !!}">
                            Active</div>
                        <div class="menu-item {!! Request::is('admin/fttx/pos-speed/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-pos-speed-list', 2) !!}">
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
                    @can('fttx-pos-speed-create')
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
            @include('admin::pages.fttx.pos-speed.table')
            @include('admin::pages.fttx.pos-speed.create')
            @include('admin::pages.fttx.pos-speed.edit-price-by-payment-month')
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
                storePosSpeedDialog({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "80%",
                    }
                });
            },
            updatePriceByMonthDialog(data) {
                updatePriceByMonthDialog({
                    active: true,
                    data: data.price_by_pos_speed[0],
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "60%",
                    }
                });
            },
        }));
    </script>
@stop
