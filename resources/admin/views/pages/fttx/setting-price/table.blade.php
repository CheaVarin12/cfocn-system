<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5 text-left">
                    <span>Nº</span>
                </div>
                <div class="row table-row-25 text-left">
                    <span>Price</span>
                </div>
                <div class="row table-row-25 text-left">
                    <span>Type</span>
                </div> 
                <div class="row table-row-40 text-left">
                    <span>Description</span>
                </div>
                <div class="row table-row-5 text-left">
                    <span></span>
                </div>
            </div>
            <div class="table-body">
                @foreach ($data as $index => $item)
                    <div class="column heightAuto">
                        <div class="row table-row-5 text left">
                            <span>{!! $data->currentPage() * $data->perPage() - $data->perPage() + ($index + 1) !!}</span>
                        </div>
                        <div class="row table-row-25 text left">
                            @foreach ($item->price as $value)
                                <span>{!! $value ? $value . ' $ ,' : '' !!}</span>
                            @endforeach
                        </div>
                        <div class="row table-row-25 text left">
                            @foreach (config('dummy.setting_price_type') as $type)
                                @if ($type['key'] == $item->type)
                                    <span>{!! $type['text'] ? $type['text'] : '--' !!}</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="row table-row-40 text left">
                            <span>{!! $item->description ? $item->description : '--' !!}</span>
                        </div>
                        <div class="row table-row-5 text left">
                            @canany(['fttx-setting-price-update'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if ($status != 'trash')
                                            @can('fttx-setting-price-update')
                                                <li>
                                                    <a class="dropdown-item" s-click-link="#"
                                                        @click="storeDialog({{ $item }})">
                                                        <i data-feather="edit"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                </li>
                                                @if ($item->status == 2)
                                                    <li>
                                                        <a class="dropdown-item enable-btn"
                                                            onclick="$onConfirmMessage(
                                                                '{!! route('admin-setting-price-status', ['id' => $item->id, 'status' => 1]) !!}',
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
                                                                '{!! route('admin-setting-price-status', ['id' => $item->id, 'status' => 2]) !!}',
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
        @component('admin::components.empty', [
            'name' => 'Data is empty',
        ])
        @endcomponent
    @endif
</div>
