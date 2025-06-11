@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Po Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/po/po-service/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-po-service-list', 1) !!}">
                            Active</div>
                        <div class="menu-item {!! Request::is('admin/po/po-service/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-po-service-list', 2) !!}">
                            Inactive</div>
                        <div class="menu-item {!! Request::is('admin/po/po-service/list/3') ? 'active' : '' !!}" s-click-link="{!! route('admin-po-service-list', 3) !!}">
                            Terminate</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
                        </div>
                        <div class="form-row">
                            <select name="po_service_type">
                                <option value="">Select po service ...</option>
                                @foreach (config('dummy.po_service') as $type)
                                    <option value="{{ $type['key'] }}" {{ request('po_service_type') == $type['key'] ? 'selected' : '' }}>{{ $type['text'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <select name="project_id">
                                <option value="">Select Project ...</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                    </form>
                    @can('purchase-order-create')
                        <button class="btn-create" @click="createPoDialog()">
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
        <div class="content-body">
            @include('admin::pages.po.table')
        </div>
        @include('admin::pages.po.upload-file.index')
        @include('admin::pages.po.detail')
        @include('admin::pages.po.create_pac')
        @include('admin::pages.po.store')
    </div>
@stop
@section('script')
    <script>
        Alpine.data('XData', () => ({
            dialog: {
                rootData: null,
                initData(root) {
                    this.rootData = root;
                },
                component: {
                    uploadFileDialog: false,
                    confirmDialog: false
                },
                data: {
                    uploadFileDialog: {},
                    confirmDialog: {}
                },
                open(dialogRef, id) {
                    this.component[dialogRef] = true;
                    this.data[dialogRef] = id;
                },
                close(dialogRef, data) {
                    this.component[dialogRef] = false;
                    if (data) {
                        const currentFolder = this.rootData?.dataFolders[this.rootData?.dataFolders.length - 1];
                        this.rootData.getData({
                            folder_id: currentFolder?.id ?? ''
                        });
                    }
                },
            },
            init() {
                this.reloadIcon();
            },
            reloadIcon() {
                feather.replace();
                setTimeout(() => {
                    feather.replace();
                }, 10);
            },
            detailDialog(data) {
                detail({
                    active: true,
                    data: data,
                    title: "Lease Order Detail",
                    config: {
                        width: "55%",
                    }
                });
            },
            createPacDialog(data) {
                pacCreate({
                    active: true,
                    data: data
                });
            },
            createPoDialog(data) {
                poCreate({
                    active: true,
                    data: data,
                    config: {
                        width: "90%",
                    }
                });
            },
            fileDialog(data) {
                uploadFileList({
                    active: true,
                    data: data,
                    title: "File",
                    config: {
                        width: "55%",
                    }
                });
            },
        }));
    </script>
@stop
