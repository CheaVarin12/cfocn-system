<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-30 text-center">
                    <span>Name</span>
                </div>
                <div class="row table-row-60 text-center">
                    <span>Description</span>
                </div>
                <div class="row table-row-5">
                    <span></span>
                </div>
            </div>
            <div class="table-body">
                @foreach ($data as $index => $item)
                    <div class="column">
                        <div class="row table-row-5">
                            <span>{!! $data->currentPage() * $data->perPage() - $data->perPage() + ($index + 1) !!}</span>
                        </div>
                        <div class="row table-row-30 text-center">
                            <span>{!! $item->name ? $item->name : '--' !!}</span>
                        </div>
                        <div class="row table-row-60 text-center">
                            <span>{!! $item->description ? $item->description : '--' !!}</span>
                        </div>
                        <div class="row table-row-5">
                            <div class="dropdown">
                                <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                    data-mdb-toggle="dropdown" aria-expanded="false">
                                </i>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($item->deleted_at == null)
                                        @can('ftth-service-update')
                                            <li>
                                                <a class="dropdown-item" s-click-link="{!! route('admin-ftth-service-create', $item->id) !!}">
                                                    <i data-feather="edit" class="text-primary"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>

                                            @if ($item->status == 2)
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                        '{!! route('admin-ftth-service-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                        '@lang('dialog.msg.enable', ['name' => $item->name])',
                                                        {
                                                            confirm: '@lang('dialog.button.enable')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        },
                                                    );">
                                                        <i data-feather="rotate-ccw" class="text-success"></i>
                                                        <span>Enable</span>
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item disable-btn"
                                                        onclick="$onConfirmMessage(
                                                        '{!! route('admin-ftth-service-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                        '@lang('dialog.msg.disable', ['name' => $item->name])',
                                                        {
                                                            confirm: '@lang('dialog.button.disable')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                        <i data-feather="x-circle" class="text-warning"></i>
                                                        <span>Disable</span>
                                                    </a>
                                                </li>
                                            @endif
                                        @endcan
                                        @can('ftth-service-delete')
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                    '{!! route('admin-ftth-service-delete', $item->id) !!}',
                                                    '@lang('dialog.msg.delete', ['name' => $item->name])',
                                                    {
                                                        confirm: '@lang('dialog.button.delete')',
                                                        cancel: '@lang('dialog.button.cancel')'
                                                    }
                                                );">
                                                    <i data-feather="trash" class="text-danger"></i>
                                                    <span>Delete</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @else
                                        @can('ftth-service-delete')
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                    '{!! route('admin-ftth-service-destroy', $item->id) !!}',
                                                    '@lang('dialog.msg.delete', ['name' => $item->name])',
                                                    {
                                                        confirm: '@lang('dialog.button.delete')',
                                                        cancel: '@lang('dialog.button.cancel')'
                                                    }
                                                );">
                                                    <i data-feather="trash" class="text-danger"></i>
                                                    <span>Delete</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                    '{!! route('admin-ftth-service-restore', $item->id) !!}',
                                                    '@lang('dialog.msg.restore', ['name' => $item->name])',
                                                    {
                                                        confirm: '@lang('dialog.button.restore')',
                                                        cancel: '@lang('dialog.button.cancel')'
                                                    }
                                                );">
                                                    <i data-feather="rotate-ccw" class="text-success"></i>
                                                    <span>Restore</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>
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
            'name' => 'FTTH Service is empty',
            'msg' => 'You can create a new FTTH Service by clicking the button below.',
            'permission' => 'ftth-service-create',
            'url' => route('admin-ftth-service-create'),
            'button' => 'Create FTTH Service',
        ])
        @endcomponent
    @endif
</div>
