<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-15 text-center">
                    <span>PO\LO Number</span>
                </div>
                <div class="row table-row-15 text-center">
                    <span>Customer</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Project</span>
                </div>
                <div class="row table-row-15 text-center">
                    <span>Sevice type</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Customer Phone</span>
                </div>
                <div class="row table-row-15">
                    <span>Total Price</span>
                </div>
                <div class="row table-row-10 text-center">
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
                        <div class="row table-row-15 text-center">
                            <span>{!! $item->po_number ? $item->po_number : '--' !!}</span>
                        </div>
                        <div class="row table-row-15 text-center">
                            <span>
                                {!! $item->customer ? $item->customer->name_en : '--' !!}
                            </span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span> {!! $item->project ? $item->project->name : '--' !!}</span>
                        </div>
                        <div class="row table-row-15 text-center">
                            <span> {!! $item->type ? $item->type->name : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->customer ? $item->customer->phone : '--' !!} </span>
                        </div>
                        <div class="row table-row-15">
                            <span>$ {!! $item->total_price ? number_format($item->total_price, 2) : '0.00' !!} </span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->end_date ? $item->end_date : '--' !!}</span>
                        </div>

                        <div class="row table-row-5">
                            @canany(['purchase-update', 'purchase-create-invoice', 'purchase-upload-file'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('purchase-upload-file')
                                            <li>
                                                <a class="dropdown-item" s-click-link="{!! route('admin-purchase-list-document', $item->id) !!}">
                                                    <i data-feather="folder"></i>
                                                    <span>Upload file</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('purchase-update')
                                            <li>
                                                <a class="dropdown-item" s-click-link="{!! route('admin-purchase-edit', $item->id) !!}">
                                                    <i data-feather="edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>

                                            @if ($item->status == 1)
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                            '{!! route('admin-purchase-status', ['id' => $item->id, 'status' => 2]) !!}',
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
                                                            '{!! route('admin-purchase-status', ['id' => $item->id, 'status' => 3]) !!}',
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
                                                        '{!! route('admin-purchase-order-status', ['id' => $item->id, 'status' => 1]) !!}',
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
                                                        '{!! route('admin-purchase-status', ['id' => $item->id, 'status' => 3]) !!}',
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
                                                    '{!! route('admin-purchase-order-status', ['id' => $item->id, 'status' => 1]) !!}',
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
                                                        '{!! route('admin-purchase-status', ['id' => $item->id, 'status' => 2]) !!}',
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
                                        @if ($item->status == 1 )
                                            @can('purchase-create-invoice')
                                                <li>
                                                    <a class="dropdown-item" @click="createInvoiceDialog({{ $item }})">
                                                        <i data-feather="file" class="text-primary"></i>
                                                        <span>Create invoice</span>
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
            'name' => 'PAC is empty',
            'msg' => 'You can create a new pac by clicking the button below.',
            'permission' => 'purchase-create',
            'url' => route('admin-purchase-create'),
            'button' => 'Create PAC',
        ])
        @endcomponent
    @endif
</div>
