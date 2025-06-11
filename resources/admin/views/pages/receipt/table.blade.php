<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Receipt Number</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Invoice Number</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Customer</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Project</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Amount</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Paid</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Portal</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Issue Date</span>
                </div>
                <div class="row table-row-10 text-center">
                    <span>Paid Date</span>
                </div>
                <div class="row table-row-5">
                    <span></span>
                </div>
            </div>
            <div class="table-body">
                @foreach ($data as $index => $item)
                    <div class="column columnCus heightAuto">
                        <div class="row table-row-5">
                            <span>{!! $data->currentPage() * $data->perPage() - $data->perPage() + ($index + 1) !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->receipt_number ? $item->receipt_number : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            @if ($item->receipt_from == 'credit_note')
                                <span>{!! $item->creditNote ? $item->creditNote->credit_note_number : '--' !!}</span>
                            @else
                                <span>{!! $item->invoices ? $item->invoices->invoice_number : '--' !!}</span>
                            @endif
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item?->data_customer ? json_decode($item->data_customer)->name_en : $item?->customer?->name_en !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->invoices ? $item->invoices->purchase->project->name : '--' !!} </span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>$ {!! $item->total_grand ? number_format($item->total_grand, 2) : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>$ {!! $item->paid_amount ? number_format($item->paid_amount, 2) : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            {{-- <span>{!! $item->debt_amount ?'$ '.number_format($item->debt_amount + $item->debt_amount * (10 / 100), 2) : '--' !!} </span> --}}
                            <span>{!! $item->partial_payment ? '$ '. number_format($item->partial_payment, 2) : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->issue_date ? $item->issue_date : '--' !!} </span>
                        </div>
                        <div class="row table-row-10 text-center">
                            <span>{!! $item->paid_date ? $item->paid_date : '--' !!} </span>
                        </div>
                        <div class="row table-row-5">
                            @canany(['receipt-update', 'receipt-detail', 'receipt-delete'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('receipt-detail')
                                            <li>
                                                <a class="dropdown-item" @click="invoiceDetailDialog({{ $item }})">
                                                    <i data-feather="eye" class="text-success"></i>
                                                    <span>View Detail</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @if (checkValidate($item->issue_date))
                                            @if ($item->payment_status == 'portal')
                                                @can('receipt-update')
                                                    <li>
                                                        <a class="dropdown-item" @click="editStatusDialog({{ $item }})">
                                                            <i class="text-primary" data-feather="dollar-sign"></i>
                                                            <span>Payment Status</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" @click="editDialog({{ $item }})">
                                                            <i data-feather="edit-3"></i>
                                                            <span>Edit</span>
                                                        </a>
                                                    </li>
                                                @endcan
                                            @endif
                                        @endif
                                        @can('receipt-delete')
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                        '{!! route('admin-receipt-delete', $item->id) !!}',
                                                        '@lang('dialog.msg.delete', ['name' => 'receipt ' . $item->receipt_number])',
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
            'name' => 'Recipt is empty',
        ])
        @endcomponent
    @endif
</div>
