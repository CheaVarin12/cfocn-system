<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>Nº</span>
                </div>
                <div class="row table-row-30">
                    <span>Project</span>
                </div>
                <div class="row table-row-20">
                    <span>Percentage</span>
                </div>
                <div class="row table-row-20">
                    <span>License fee</span>
                </div>
                <div class="row table-row-20">
                    <span>Year</span>
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
                        <div class="row table-row-30">
                            <span>{{ $item->project->name??"--" }}</span>
                        </div>
                        <div class="row table-row-20">
                            <span>{{ $item->percentage??"--" }} %</span>
                        </div>
                        <div class="row table-row-20">
                            <span>{{ $item->license_fee??"--" }}</span>
                        </div>
                        <div class="row table-row-20">
                            <span>{{ $item->year }}</span>
                        </div>
                        <div class="row table-row-5">
                           
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if ($status != 'trash')
                                         
                                                <li>
                                                    <a class="dropdown-item" s-click-link="{!! route('admin-license-fee-edit', $item->id) !!}">
                                                        <i data-feather="edit"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                </li>
                                                @if ($item->status == 2)
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                                '{!! route('admin-license-fee-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                                '@lang('dialog.msg.enable', ['name' => $item->year])',
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
                                                                '{!! route('admin-license-fee-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                                '@lang('dialog.msg.disable', ['name' => $item->year])',
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
        @component('admin::components.empty',
            [
                'name' => 'License Fee is empty',
                'msg' => 'You can create a new License Fee by clicking the button below.',
                'permission' => 'service-create',
                'url' => route('admin-license-fee-create'),
                'button' => 'Create License Fee',
            ])
        @endcomponent
    @endif
</div>
