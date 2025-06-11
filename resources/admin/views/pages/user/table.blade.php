<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>@lang('table.field.no')</span>
                </div>
                {{-- <div class="row table-row-10">
                    <span>@lang('table.field.profile')</span>
                </div> --}}
                <div class="row table-row-35 text-left">
                    <span>@lang('table.field.name')</span>
                </div>
                <div class="row table-row-20 text-start">
                    <span>@lang('table.field.email')</span>
                </div>
                <div class="row table-row-15">
                    <span>@lang('table.field.phone')</span>
                </div>
                <div class="row table-row-10">
                    <span>@lang('table.field.role')</span>
                </div>
                <div class="row table-row-10">
                    <span>@lang('table.field.created_at')</span>
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
                        {{-- {{ Avatar::create('Joko Widodo')->toBase64() }} --}}
                        {{-- <div class="row table-row-10">
                            <div class="thumbnail" data-fancybox data-src="{{ $item->image_url }}">
                                <img class="objectFitContent" src="{!! $item->image_url ? $item->image_url : Avatar::create($item->username)->toBase64() !!}"
                                    onerror="(this).src='{{ asset('images/logo/default.png') }}'" alt="">
                            </div>
                        </div> --}}
                        <div class="row table-row-35 text left bold">
                            <div class="userImgText">
                            <div class="thumbnail" data-fancybox data-src="{{ $item->image_url }}">
                                <img class="objectFitContent" src="{!! $item->image_url ? $item->image_url : Avatar::create($item->username)->toBase64() !!}"
                                    onerror="(this).src='{{ asset('images/logo/default.png') }}'" alt="">
                            </div>
                            <span>{!! isset($item->username) ? $item->username : '--' !!}</span>
                            </div>
                        </div>
                        <div class="row table-row-20 text text-start">
                            <span>{!! isset($item->email) ? $item->email : '--' !!}</span>
                        </div>
                        <div class="row table-row-15 text">
                            <span>{!! isset($item->phone) ? $item->phone : '--' !!}</span>
                        </div>
                        <div class="row table-row-10">
                            <span>{!! isset($item->role) ? $item->role : '--' !!}</span>
                        </div>
                        <div class="row table-row-10">
                            @foreach (DateFormat::create($item->created_at) as $date)
                                <span>{!! $date !!}</span>
                            @endforeach
                        </div>
                        <div class="row table-row-5">
                            @canany(['user-update', 'user-delete', 'user-change-passowrd', 'user-permission'])
                                <div class="dropdown">
                                    <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                    </i>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('user-update')
                                            <li>
                                                <a class="dropdown-item" s-click-link="{!! route('admin-user-create', $item->id) !!}">
                                                    <i data-feather="edit"></i>
                                                    <span>@lang('table.option.edit')</span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('user-change-passowrd')
                                            <li>
                                                <a class="dropdown-item" s-click-link="{!! route('admin-user-change-password', $item->id) !!}">
                                                    <i data-feather="key"></i>
                                                    <span>@lang('table.option.change_password')</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @if ($item->id != Auth::user()->id)
                                            @can('user-permission')
                                                <li>
                                                    <a class="dropdown-item" s-click-link="{!! route('admin-user-permission', $item->id) !!}">
                                                        <i data-feather="sliders"></i>
                                                        <span>@lang('table.option.permission')</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('user-update')
                                                @if ($item->status == 2)
                                                    <li>
                                                        <a class="dropdown-item enable-btn"
                                                            onclick="$onConfirmMessage(
                                                            '{!! route('admin-user-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                            '@lang('dialog.msg.enable', ['name' => $item->name])',
                                                            {
                                                                confirm: '@lang('dialog.button.enable')',
                                                                cancel: '@lang('dialog.button.cancel')'
                                                            },
                                                        );">
                                                            <i data-feather="rotate-ccw"></i>
                                                            <span>@lang('table.option.enable')</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item disable-btn"
                                                            onclick="$onConfirmMessage(
                                                            '{!! route('admin-user-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                            '@lang('dialog.msg.disable', ['name' => $item->name])',
                                                            {
                                                                confirm: '@lang('dialog.button.disable')',
                                                                cancel: '@lang('dialog.button.cancel')'
                                                            }
                                                        );">
                                                            <i data-feather="x-circle"></i>
                                                            <span>@lang('table.option.disable')</span>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
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
            'name' => __('user.empty.title'),
            'msg' => __('user.empty.description'),
            'permission' => 'user-create',
            'url' => route('admin-user-create'),
            'button' => __('user.button.create'),
        ])
        @endcomponent
    @endif
</div>
