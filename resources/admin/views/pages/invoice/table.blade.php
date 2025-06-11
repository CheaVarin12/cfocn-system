<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>P.O/LO Number</span>
                </div>
                <div class="row table-row-10">
                    <span>Invoice Number</span>
                </div>
                <div class="row table-row-18 text-start">
                    <span>Customer Name</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>Project</span>
                </div>
                <div class="row table-row-10">
                    <span>Paid Amount</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>Amount</span>
                </div>
                <div class="row table-row-8 text-start">
                    <span>Paid Status</span>
                </div>
                <div class="row table-row-8 text-start">
                    <span>Issue Date</span>
                </div>
                <div class="row table-row-8">
                    <span>DMC Status</span>
                </div>
                <div class="row table-row-3">
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
                            <span>{!! $item->po_number ? $item->po_number : ($item->purchase->po_number ? $item->purchase->po_number : '--') !!} </span>
                        </div>
                        <div class="row table-row-10">
                            <span>{!! $item->invoice_number ? $item->invoice_number : '--' !!}</span>
                        </div>
                        <div class="row table-row-18 text-start">
                            <span>
                                {!! $item?->data_customer ? json_decode($item->data_customer)->name_en : $item?->customer?->name_en !!}
                            </span>
                        </div>
                        <div class="row table-row-10 text-start">
                            <span> {!! $item->purchase ? $item->purchase?->project?->name : '--' !!} </span>
                        </div>
                        <div class="row table-row-10">
                            <span>{!! $item->paid_amount ? '$ ' . number_format($item->paid_amount, 2) : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-start">
                            <span>$ {!! $item->total_grand ? number_format($item->total_grand, 2) : '--' !!} </span>
                        </div>
                        <div class="row table-row-8 text-start">
                            <span>{!! $item->paid_status ? $item->paid_status : '--' !!} </span>
                        </div>
                        <div class="row table-row-8 text-start">
                            <span> {!! $item->issue_date ? $item->issue_date : '--' !!} </span>
                        </div>
                        <div class="row table-row-8">
                            @if ($item->doc_status == 'is_void' || $item->doc_status == 'is_send')
                                <span class="badge success">Completed</span>
                            @else
                                <span class="badge pending">Pending</span>
                            @endif
                        </div>
                        <div class="row table-row-3">
                            @canany(['invoice-update', 'invoice-void', 'invoice-detail', 'invoice-create-receipt',
                                'invoice-copy', 'invoice-dmc-submit'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if (request('status') != 3)
                                            @if (request('status') != 5 && request('status') != 6)
                                                @if (checkValidate($item->issue_date))
                                                    @can('invoice-create-receipt')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                @click="createReceiptDialog({{ $item }})">
                                                                <i data-feather="file"></i>
                                                                <span>Create Receipt</span>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('invoice-copy')
                                                        @if ($item->multiple_pac)
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    @click="copyInvoiceMultiPacDialog({{ $item }})">
                                                                    <i data-feather="copy" class="text-primary"></i>
                                                                    <span>Copy</span>
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    @click="copyInvoiceDialog({{ $item }})">
                                                                    <i data-feather="copy" class="text-primary"></i>
                                                                    <span>Copy</span>
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endcan
                                                @endif
                                            @endif

                                            @can('invoice-detail')
                                                <li>
                                                    <a class="dropdown-item" @click="invoiceDetailDialog({{ $item }})">
                                                        <i data-feather="eye" class="text-success"></i>
                                                        <span>View Detail</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @if (request('status') != 6)
                                                @if (checkValidate($item->issue_date))
                                                    @if ($item->doc_status != 'is_send')
                                                        @if ($item->invoice_number)
                                                            @can('invoice-dmc-submit')
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        @click="dmcSubmitDialog({{ $item }})">
                                                                        <i data-feather="file" class="text-success"></i>
                                                                        <span>DMC Submit</span>
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        @endif
                                                        @can('invoice-update')
                                                            @if ($item->multiple_pac)
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        @click="editInvoiceMultiPacDialog({{ $item }})">
                                                                        <i data-feather="edit" class="text-primary"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        @click="editInvoiceDialog({{ $item }})">
                                                                        <i data-feather="edit" class="text-primary"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                                    @endif
                                                    @if (request('status') != 5)
                                                        @can('invoice-void')
                                                            <li>
                                                                <a class="dropdown-item enable-btn text-danger"
                                                                    onclick="$onConfirmMessage(
                                                                '{!! route('admin-invoice-delete', $item->id) !!}',
                                                                '@lang('Are you sure to void ?', ['name' => $item->name])',
                                                                {
                                                                    confirm: '@lang('void')',
                                                                    cancel: '@lang('dialog.button.cancel')'
                                                                },
                                                            );">
                                                                    <i data-feather="trash" class="text-danger"></i>
                                                                    <span>Void</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    @endif
                                                @endif
                                            @endif
                                            @if (request('status') == 5)
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        @click="invoiceDelete({{ $item }})">
                                                        <i data-feather="trash-2"></i>
                                                        <span>Delete</span>
                                                    </a>
                                                </li>
                                            @endif
                                        @else
                                            @if ($item->doc_status != 'is_void')
                                                @if (checkValidate($item->issue_date) && $item->invoice_number)
                                                    @can('invoice-dmc-submit')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                @click="dmcSubmitDialog({{ $item }},'void')">
                                                                <i data-feather="file" class="text-success"></i>
                                                                <span>DMC Submit</span>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @endif
                                            @endif
                                            @can('invoice-detail')
                                                <li>
                                                    <a class="dropdown-item" @click="invoiceDetailDialog({{ $item }})">
                                                        <i data-feather="eye" class="text-success"></i>
                                                        <span>View Detail</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @if (request('status') == 3 && $item->deleted_at)
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        @click="invoiceDelete({{ $item }})">
                                                        <i data-feather="trash-2"></i>
                                                        <span>Delete</span>
                                                    </a>
                                                </li>
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
            'name' => 'Invoice is empty',
        ])
        @endcomponent
    @endif
</div>
