<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-25 text-center">
                    <span>Customer</span>
                </div>
                <div class="row table-row-15 text-center">
                    <span>Customer Phone</span>
                </div>
                <div class="row table-row-15 text-center">
                    <span>Project</span>
                </div>
                <div class="row table-row-15">
                    <span>Total Price</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Total Quantity</span>
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
                    <div class="column heightAutos">
                        <div class="row table-row-5">
                            <span>{!! $data->currentPage() * $data->perPage() - $data->perPage() + ($index + 1) !!}</span>
                        </div>
                        <div class="row table-row-25 text-center">
                            <span>
                                {!! $item->customer ? $item->customer->name_en : '--' !!}
                            </span>
                        </div>
                        <div class="row table-row-15 text-center">
                            <span>{!! $item->customer ? $item->customer->phone : '--' !!} </span>
                        </div>
                        <div class="row table-row-15 text-center">
                            <span> {!! $item->project ? $item->project->name : '--' !!}</span>
                        </div>
                        <div class="row table-row-15">
                            <span>$ {!! $item->total_price ? number_format($item->total_price, 2) : '--' !!} </span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->total_qty ? $item->total_qty : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->end_date ? $item->end_date : '--' !!}</span>
                        </div>
                        <div class="row table-row-5">
                            @canany(['work-order-order'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('work-order-order')
                                            <li>
                                                <a class="dropdown-item" s-click-link="{!! route('admin-order-edit', $item->id) !!}">
                                                    <i data-feather="edit" class="text-info"></i>
                                                    <span>Edit</span>
                                                </a>
                                            </li>
                                            @if ($item->status == 2)
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                                '{!! route('admin-order-status', ['id' => $item->id, 'status' => 1]) !!}',
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
                                                                '{!! route('admin-order-status', ['id' => $item->id, 'status' => 2]) !!}',
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
                                        @if ($item->status != 2)
                                            @can('work-order-order')
                                                <li>
                                                    <a class="dropdown-item" @click="createInvoiceDialog({{$item}})">
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
            'name' => 'Order is empty',
            'msg' => 'You can create a new order by clicking the button below.',
            'permission' => 'order-create',
            'url' => route('admin-order-create'),
            'button' => 'Create Order',
        ])
        @endcomponent
    @endif
</div>
