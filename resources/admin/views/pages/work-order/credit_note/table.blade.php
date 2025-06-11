<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-13 text-start">
                    <span>Credit Note Number</span>
                </div>
                <div class="row table-row-10">
                    <span>Invoice Number</span>
                </div>
                <div class="row table-row-25">
                    <span>Customer Name</span>
                </div>
                <div class="row table-row-15">
                    <span>Project</span>
                </div>
                <div class="row table-row-10">
                    <span>Amount</span>
                </div>
                <div class="row table-row-7">
                    <span>Issue Date</span>
                </div>
                <div class="row table-row-10">
                    <span>DMC Status</span>
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
                        <div class="row table-row-13 text-start">
                            <span>{!! $item->credit_note_number ? $item->credit_note_number : '--' !!} </span>
                        </div>
                        <div class="row table-row-10">
                            <span>{!! $item->invoice_number ? $item->invoice_number : '--' !!}</span>
                        </div>
                        <div class="row table-row-25">
                            <span>
                                {!! $item?->data_customer ? json_decode($item->data_customer)->name_en : $item?->customer?->name_en !!}
                            </span>
                        </div>
                        <div class="row table-row-15">
                            <span> {!! $item->order->project->name ?? '--' !!} </span>
                        </div>
                        <div class="row table-row-10">
                            <span>$ {!! $item->total_price ? number_format($item->total_price, 2) : '--' !!} </span>
                        </div>
                        <div class="row table-row-7">
                            <span> {!! $item->issue_date ? $item->issue_date : '--' !!} </span>
                        </div>
                        <div class="row table-row-10">
                            <span
                                class="badge {{ $item->doc_status == 'is_send' ? 'success' : 'pending' }}">{!! $item->doc_status == 'is_send' ? 'Completed' : 'Pending' !!}
                            </span>
                        </div>
                        <div class="row table-row-5">
                            @canany(['work-order-credit-note'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('work-order-credit-note')
                                            <li>
                                                <a class="dropdown-item" @click="invoiceDetailDialog({{ $item }})">
                                                    <i data-feather="eye" class="text-success"></i>
                                                    <span>View Detail</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @if (checkValidate($item->issue_date))
                                            @if ($status != 3)
                                                @can('work-order-credit-note')
                                                    @if ($item->status == 2)
                                                        <li>
                                                            <a class="dropdown-item enable-btn"
                                                                onclick="$onConfirmMessage(
                                                                '{!! route('admin-work-order-credit-note-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                                '@lang('dialog.msg.enable', ['name' => $item->name])',
                                                                {
                                                                    confirm: '@lang('dialog.button.enable')',
                                                                    cancel: '@lang('dialog.button.cancel')'
                                                                },
                                                            );">
                                                                <i data-feather="rotate-ccw"></i>
                                                                <span>Enable</span>
                                                            </a>
                                                        </li>
                                                    @else
                                                        @if ($item->doc_status != 'is_send')
                                                            <li>
                                                                <a class="dropdown-item" @click="edit({{ $item }})">
                                                                    <i data-feather="edit"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item disable-btn"
                                                                    onclick="$onConfirmMessage(
                                                                    '{!! route('admin-work-order-credit-note-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                                    '@lang('dialog.msg.disable', ['name' => $item->name])',
                                                                    {
                                                                        confirm: '@lang('dialog.button.disable')',
                                                                        cancel: '@lang('dialog.button.cancel')'
                                                                    }
                                                                );">
                                                                    <i data-feather="x-circle"></i>
                                                                    <span>Disable</span>
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                @endcan
                                                @can('work-order-credit-note')
                                                    @if ($item->doc_status != 'is_send')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                @click="dmcSubmitDialog({{ $item }})">
                                                                <i data-feather="file" class="text-success"></i>
                                                                <span>DMC Submit</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endcan
                                            @endif
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
            'name' => 'Credit Note is empty',
        ])
        @endcomponent
    @endif
</div>
