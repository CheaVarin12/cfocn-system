<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-25">
                    <span>Bank Name</span>
                </div>
                <div class="row table-row-20">
                    <span>Account Name</span>
                </div>
                <div class="row table-row-25">
                    <span>Account Number</span>
                </div>
                <div class="row table-row-20">
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
                        <div class="row table-row-25">
                            <span>{!! $item->bank_name ? $item->bank_name : '--' !!}</span>
                        </div>
                        <div class="row table-row-20">
                            <span>{!! $item->account_name ? $item->account_name : '--' !!}</span>
                        </div>
                        <div class="row table-row-25">
                            <span>{!! $item->account_number ? $item->account_number : '--' !!}</span>
                        </div>
                        <div class="row table-row-20">
                            <span>{!! $item->status == 1 ? 'Enabled' : 'Disabled' !!}</span>
                        </div>
                        <div class="row table-row-5">
                            @canany(['bank-account'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if ($status != 'trash')
                                            @can('bank-account')
                                                <li>
                                                    <a class="dropdown-item" s-click-link="{!! route('admin-bank-account-edit', $item->id) !!}">
                                                        <i data-feather="edit"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                </li>
                                                @if ($item->status == 2)
                                                    <li>
                                                        <a class="dropdown-item enable-btn"
                                                            onclick="$onConfirmMessage(
                                                                '{!! route('admin-bank-account-status', ['id' => $item->id, 'status' => 1]) !!}',
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
                                                                '{!! route('admin-bank-account-status', ['id' => $item->id, 'status' => 2]) !!}',
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
        @component('admin::components.empty',
            [
                'name' => 'Bank account is empty',
                'msg' => 'You can create a new bank account by clicking the button below.',
                'permission' => 'bank-account',
                'url' => route('admin-bank-account-create'),
                'button' => 'Create Bank Account',
            ])
        @endcomponent
    @endif
</div>
