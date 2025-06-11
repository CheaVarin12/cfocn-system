<div class="table">
    @if ($data->count() > 0)
        <div class="table-wrapper">
            <div class="table-header">
                <div class="row table-row-5">
                    <span>@lang('table.field.no')</span>
                </div>
                <div class="row table-row-10">
                    <span>@lang('table.field.image')</span>
                </div>
                <div class="row table-row-20 text-left">
                    <span>@lang('table.field.name_km')</span>
                </div>
                <div class="row table-row-20">
                    <span>@lang('table.field.ordering')</span>
                </div>

                <div class="row table-row-25">
                    <span>@lang('table.field.created_at')</span>
                </div>
                <div class="row table-row-15">
                    <span>@lang('table.field.create_by')</span>
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
                        <div class="row table-row-10">
                            <div class="thumbnail" data-fancybox data-src="{{ asset('file_manager' . $item->image) }}">
                                <img src="{!! $item->image_url !!}"
                                    onerror="(this).src='{{ asset('images/logo/default.png') }}'" alt="">
                            </div>
                        </div>
                        <div class="row table-row-20 text left bold">
                            <span>{!! isset(json_decode($item->name)->km) ? json_decode($item->name)->km : '--' !!}</span>
                        </div>
                        <div class="row table-row-20 text">
                            <span>{{ isset($item->ordering) ? $item->ordering : '---' }}</span>
                        </div>

                        <div class="row table-row-25">
                            @foreach (DateFormat::create($item->created_at) as $date)
                                <span>{!! $date !!}</span>
                            @endforeach
                        </div>
                        <div class="row table-row-15">
                            <span>{{ isset($item->user) ? $item->user->name : '---' }}</span>
                        </div>
                        <div class="row table-row-5">
                            <div class="dropdown">
                                <i data-feather="more-vertical" class="action-btn" id="dropdownMenuButton"
                                    data-mdb-toggle="dropdown" aria-expanded="false">
                                </i>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($status != 'trash')
                                        @can('slide-update')
                                            <li>
                                                <a class="dropdown-item" s-click-link="{!! route('admin-slide-edit', $item->id) !!}">
                                                    <i data-feather="edit"></i>
                                                    <span>@lang('table.option.edit')</span>
                                                </a>
                                            </li>
                                            @if ($item->status == 2)
                                                <li>
                                                    <a class="dropdown-item enable-btn"
                                                        onclick="$onConfirmMessage(
                                                            '{!! route('admin-slide-status', ['id' => $item->id, 'status' => 1]) !!}',
                                                            '@lang('dialog.msg.enable', ['name' => isset(json_decode($item->name)->km) ? json_decode($item->name)->km : json_decode($item->name)->en])',
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
                                                            '{!! route('admin-slide-status', ['id' => $item->id, 'status' => 2]) !!}',
                                                            '@lang('dialog.msg.disable', ['name' => isset(json_decode($item->name)->km) ? json_decode($item->name)->km : json_decode($item->name)->en])',
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
                                        @endcan
                                        @can('slide-delete')
                                            <li>
                                                <a class="dropdown-item text-danger trash-btn"
                                                    data-url="{!! route('admin-slide-destroy', $item->id) !!}" data-name="{!! isset(json_decode($item->name)->km) ? json_decode($item->name)->km : json_decode($item->name)->en !!}">
                                                    <i data-feather="trash-2"></i>
                                                    <span>@lang('table.option.delete')</span>
                                                </a>
                                            </li>
                                        @endcan
                                    @else
                                        @can('slide-delete')
                                            <li>
                                                <a class="dropdown-item disable-btn"
                                                    onclick="$onConfirmMessage(
                                                        '{!! route('admin-slide-restore', ['id' => $item->id, 'status' => 'restore']) !!}',
                                                        '@lang('dialog.msg.restore', ['name' => isset(json_decode($item->name)->km) ? json_decode($item->name)->km : json_decode($item->name)->en])',
                                                        {
                                                            confirm: '@lang('dialog.button.restore')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                    <i data-feather="rotate-ccw"></i>
                                                    <span>@lang('table.option.restore')</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger delete-btn"
                                                    onclick="$onConfirmMessage(
                                                        '{!! route('admin-slide-destroy', $item->id) !!}' ,
                                                        '@lang('dialog.msg.delete', ['name' => isset(json_decode($item->name)->km) ? json_decode($item->name)->km : json_decode($item->name)->en])',
                                                        {
                                                            confirm: '@lang('dialog.button.delete')',
                                                            cancel: '@lang('dialog.button.cancel')'
                                                        }
                                                    );">
                                                    <i data-feather="trash"></i>
                                                    <span>@lang('table.option.delete')</span>
                                                </a>
                                            </li>
                                        @endcan
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
                'name' => __('adminGlobal.empty.titleSlide'),
                'msg' => __('adminGlobal.empty.descriptionSlide'),
                'permission' => 'slide-create',
                'url' => route('admin-slide-create'),
                'button' => __('adminGlobal.button.createSlide'),
            ])
        @endcomponent
    @endif
</div>
