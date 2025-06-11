<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-15">
                    <span>Customer ID</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>Customer Name</span>
                </div>
                <div class="row table-row-10 text-start">
                    <span>VAT</span>
                </div>
                <div class="row table-row-10">
                    <span>Phone Number</span>
                </div>
                <div class="row table-row-10">
                    <span>Register Date</span>
                </div>
                <div class="row table-row-20 text-start">
                    <span>Address EN</span>
                </div>
                <div class="row table-row-20 text-start">
                    <span>Address KH</span>
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
                        <div class="row table-row-15">
                            <span>{!! $item->customer_code ? $item->customer_code : '--' !!}</span>
                        </div>
                        <div class="row table-row-10 text-start">
                            <span>{!! $item->name_en ? $item->name_en : '--' !!}</span>
                            <span>{!! $item->name_kh ? $item->name_kh : '--' !!}</span>
                        </div>
                        <div class="row table-row-15">
                            <span>{!! $item->vat_tin ? $item->vat_tin : '--' !!}</span>
                        </div>
                        <div class="row table-row-15">
                            <span>{!! $item->phone ? $item->phone : '--' !!}</span>
                        </div>
                        <div class="row table-row-10">
                            <span>{!! $item->register_date ? $item->register_date : '--' !!}</span>
                        </div>
                        <div class="row table-row-20 text-start">
                            <span>{!! $item->address_en ? $item->address_en : '--' !!}</span>
                        </div>
                        <div class="row table-row-20 text-start">
                            <span>{!! $item->address_kh ? $item->address_kh : '--' !!}</span>
                        </div>
                        <div class="row table-row-5">
                            @canany(['customer-update','customer-upload-file'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if ($status != 'trash')
                                             @can('customer-upload-file')
                                                <li>
                                                    <a class="dropdown-item" s-click-link="{!! route('admin-customer-list-document', $item->id) !!}">
                                                        <i data-feather="folder"></i>
                                                        <span>Upload file</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('customer-update')
                                                <li>
                                                    <a class="dropdown-item" s-click-link="{!! route('admin-customer-edit', $item->id) !!}">
                                                        <i data-feather="edit"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                </li>
                                                @if ($item->status == 2)
                                                    <li>
                                                        <a class="dropdown-item enable-btn"
                                                            onclick="$onConfirmMessage(
                                                                '{!! route('admin-customer-status', ['id' => $item->id, 'status' => 1]) !!}',
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
                                                    <li>
                                                        <a class="dropdown-item disable-btn"
                                                            onclick="$onConfirmMessage(
                                                                '{!! route('admin-customer-status', ['id' => $item->id, 'status' => 2]) !!}',
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
                                            @endcan
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                        '{!! route('admin-customer-delete', $item->id) !!}',
                                                        '@lang('dialog.msg.delete', ['name' => $item->name_en])',
                                                        {
                                                            confirm: '@lang('dialog.button.delete')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                    <i data-feather="trash"></i>
                                                    <span>Delete</span>
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                        '{!! route('admin-customer-destroy', $item->id) !!}',
                                                        '@lang('dialog.msg.delete', ['name' => $item->name_en])',
                                                        {
                                                            confirm: '@lang('dialog.button.delete')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                    <i data-feather="trash"></i>
                                                    <span>Delete</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    onclick="$onConfirmMessage(
                                                        '{!! route('admin-customer-restore', $item->id) !!}',
                                                        '@lang('dialog.msg.restore', ['name' => $item->name_en])',
                                                        {
                                                            confirm: '@lang('dialog.button.restore')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                    <i data-feather="rotate-ccw"></i>
                                                    <span>Restore</span>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" @click="detailDialog({{ $item }})">
                                                <i data-feather="eye"></i>
                                                <span>View history</span>
                                            </a>
                                        </li>
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
        @component('admin::components.empty',
            [
                'name' => 'Customer is empty',
                'msg' => 'You can create a new customer by clicking the button below.',
                'permission' => 'customer-create',
                'url' => route('admin-customer-create'),
                'button' => 'Create Customer',
            ])
        @endcomponent
    @endif
</div>


