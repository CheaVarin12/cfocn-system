<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>PO\LO Number</span>
                </div>
                <div class="row table-row-15 text-start">
                    <span>Customer</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>Project</span>
                </div>
                <div class="row table-row-15 text-start">
                    <span>Sevice type</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>Customer Phone</span>
                </div>
                <div class="row table-row-10">
                    <span>Total Price</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>Po Service Type</span>
                </div>
                <div class="row table-row-10">
                    <span>End Date</span>
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
                        <div class="row table-row-10 text-start">
                            <span>{!! $item->po_number ? $item->po_number : '--' !!}</span>
                        </div>
                        <div class="row table-row-15 text-start">
                            <span>
                                {!! $item->customer ? $item->customer->name_en : '--' !!}
                            </span>
                        </div>
                        <div class="row table-row-10 text-start">
                            <span> {!! $item->project ? $item->project->name : '--' !!}</span>
                        </div>
                        <div class="row table-row-15 text-start">
                            <span> {!! $item->serviceType ? $item->serviceType->name : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-start">
                            <span>{!! $item->customer ? $item->customer->phone : '--' !!} </span>
                        </div>
                        <div class="row table-row-10">
                            <span>$ {!! $item->total_price ? number_format($item->total_price, 2) : '--' !!} </span>
                        </div>
                        <div class="row table-row-10">
                            @foreach (config('dummy.po_service') as $type)
                              @if($type['key'] == $item->type)
                                 <span>{{ $type['text'] }}</span>
                              @endif
                            @endforeach
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->end_date ? $item->end_date : '--' !!}</span>
                        </div>
                        <div class="row table-row-5">
                            @canany(['purchase-order-update', 'purchase-order-upload-file',
                                'purchase-order-create-pac'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('purchase-order-update')
                                            <li>
                                                <a class="dropdown-item" @click="createPoDialog({{ $item }})">
                                                    <i data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            @if ($item->status == 1)
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                                '{!! route('admin-po-service-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                                '@lang('Are you sure to inactive ?', ['name' => $item->name])',
                                                                {
                                                                    confirm: 'Inactive',
                                                                    cancel: '@lang('dialog.button.cancel')'
                                                                },
                                                            );">
                                                        <i data-feather="x-circle" class="text-warning"></i>
                                                        <span>Inactive</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                                '{!! route('admin-po-service-status', ['id' => $item->id, 'status' => 3]) !!}',
                                                                '@lang('Are you sure to terminate ?', ['name' => $item->name])',
                                                                {
                                                                    confirm: 'Terminate',
                                                                    cancel: '@lang('dialog.button.cancel')'
                                                                },
                                                            );">
                                                        <i data-feather="power" class="text-danger"></i>
                                                        <span>Terminate</span>
                                                    </a>
                                                </li>
                                            @elseif ($item->status == 2)
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                            '{!! route('admin-po-service-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                            '@lang('Are you sure to active ?', ['name' => $item->name])',
                                                            {
                                                                confirm: 'Active',
                                                                cancel: '@lang('dialog.button.cancel')'
                                                            },
                                                        );">
                                                        <i data-feather="rotate-ccw" class="text-success"></i>
                                                        <span>Active</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                            '{!! route('admin-po-service-status', ['id' => $item->id, 'status' => 3]) !!}',
                                                            '@lang('Are you sure to terminate ?', ['name' => $item->name])',
                                                            {
                                                                confirm: 'Terminate',
                                                                cancel: '@lang('dialog.button.cancel')'
                                                            },
                                                        );">
                                                        <i data-feather="power" class="text-danger"></i>
                                                        <span>Terminate</span>
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                        '{!! route('admin-po-service-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                        '@lang('Are you sure to active ?', ['name' => $item->name])',
                                                        {
                                                            confirm: 'Active',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        },
                                                    );">
                                                        <i data-feather="rotate-ccw" class="text-success"></i>
                                                        <span>Active</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                            '{!! route('admin-po-service-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                            '@lang('Are you sure to inactive ?', ['name' => $item->name])',
                                                            {
                                                                confirm: 'Inactive',
                                                                cancel: '@lang('dialog.button.cancel')'
                                                            },
                                                        );">
                                                        <i data-feather="x-circle" class="text-warning"></i>
                                                        <span>Inactive</span>
                                                    </a>
                                                </li>
                                            @endif
                                        @endcan
                                        {{-- <li>
                                            <a class="dropdown-item" @click="detailDialog({{ $item }})">
                                                <i data-feather="eye" class="text-success"></i>
                                                <span>View Detail</span>
                                            </a>
                                        </li> --}}
                                        @can('purchase-order-upload-file')
                                            <li>
                                                <a class="dropdown-item" @click="fileDialog({{ $item }})">
                                                    <i data-feather="upload"></i>
                                                    <span>Upload File</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('purchase-order-create-pac')
                                            <li>
                                                <a class="dropdown-item" @click="createPacDialog({{ $item }})">
                                                    <i data-feather="file" class="text-primary"></i>
                                                    <span>Create pac</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('purchase-order-delete')
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                '{!! route('admin-delete-po', $item->id) !!}',
                                                'Are you sure to delete ?',
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
            'name' => 'Po is empty',
        ])
        @endcomponent
    @endif
</div>
