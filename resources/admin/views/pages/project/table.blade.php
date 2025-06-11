<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>@lang('table.field.no')</span>
                </div>
                <div class="row table-row-15 text-left">
                    <span>Name</span>
                </div>
                <div class="row table-row-40">
                    <span>VAT TIN</span>
                </div>
                <div class="row table-row-15">
                    <span>Phone</span>
                </div>
                <div class="row table-row-25">
                    <span>Status</span>
                </div>

                <div class="row table-row-5">
                    <span></span>
                </div>
            </div>
            <div class="table-body">
                @foreach ($data as $index => $item)
                    <div class="column heightAuto">
                        <div class="row table-row-5">
                            <span>{!! $data->currentPage() * $data->perPage() - $data->perPage() + ($index + 1) !!}</span>
                        </div>
                        <div class="row table-row-15 text left bold">
                            <span>{!! $item->name ?? '--' !!}</span>
                        </div>
                        <div class="row table-row-40 text">
                            <span>{!! $item->vat_tin ?? '--' !!}</span>
                        </div>

                        <div class="row table-row-15">
                            <span>{!! $item->phone ?? '--' !!}</span>
                        </div>
                        <div class="row table-row-25">
                            <span>{!! $item->status == 1 ? 'Enabled' : 'Disabled' !!}</span>
                        </div>
                        <div class="row table-row-5">
                            @canany(['project-update'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if ($status != 'trash')
                                            @can('project-update')
                                                <li>
                                                    <a class="dropdown-item" s-click-link="{!! route('admin-project-edit', $item->id) !!}">
                                                        <i data-feather="edit"></i>
                                                        <span>@lang('table.option.edit')</span>
                                                    </a>
                                                </li>
                                                @if ($item->status == 2)
                                                    <li>
                                                        <a class="dropdown-item enable-btn"
                                                            onclick="$onConfirmMessage(
                                                            '{!! route('admin-project-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                            '@lang('dialog.msg.enable', ['name' => isset($item->name) ?? $item->name])',
                                                            {
                                                                confirm: '@lang('dialog.button.enable')',
                                                                cancel: '@lang('dialog.button.cancel')'
                                                            },
                                                        );">
                                                            <i data-feather="rotate-ccw"></i>
                                                            <span>@lang('table.option.enable')</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item disable-btn"
                                                            onclick="$onConfirmMessage(
                                                            '{!! route('admin-project-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                            '@lang('dialog.msg.disable', ['name' => isset($item->name) ? $item->name : $item->name])',
                                                            {
                                                                confirm: '@lang('dialog.button.disable')',
                                                                cancel: '@lang('dialog.button.cancel')'
                                                            }
                                                        );">
                                                            <i data-feather="x-circle"></i>
                                                            <span>@lang('table.option.disable')</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endcan
                                        @else
                                            @can('project-delete')
                                                <li>
                                                    <a class="dropdown-item disable-btn"
                                                        onclick="$onConfirmMessage(
                                                        '{!! route('admin-slide-restore', ['id' => $item->id, 'status' => 'restore']) !!}',
                                                        '@lang('dialog.msg.restore', ['name' => isset(json_decode($item->name)->km) ? json_decode($item->name)->km : json_decode($item->name)->en])',
                                                        {
                                                            confirm: '@lang('dialog.button.restore')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                        <i data-feather="rotate-ccw"></i>
                                                        <span>@lang('table.option.restore')</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger delete-btn"
                                                        onclick="$onConfirmMessage(
                                                        '{!! route('admin-slide-destroy', $item->id) !!}' ,
                                                        '@lang('dialog.msg.delete', ['name' => isset(json_decode($item->name)->km) ? json_decode($item->name)->km : json_decode($item->name)->en])',
                                                        {
                                                            confirm: '@lang('dialog.button.delete')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                        <i data-feather="trash"></i>
                                                        <span>@lang('table.option.delete')</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        @endif
                                    </ul>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="table-footer">
                @include('admin::components.pagination', ['paginate' => $data])
            </div>
        </div>
    @else
        @component('admin::components.empty', [
            'name' => __('adminGlobal.empty.titleProject'),
            'msg' => __('adminGlobal.empty.descriptionProject'),
            'permission' => 'project-create',
            'url' => route('admin-project-create'),
            'button' => __('adminGlobal.button.createProject'),
        ])
        @endcomponent
    @endif
</div>
