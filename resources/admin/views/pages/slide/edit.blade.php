@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="xService">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-slide-save', $data->id) !!}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-slide-list', 1) !!}"></i>
                    {!! request('id')
                        ? __('adminGlobal.form.title.updateSlide', ['name' => __('adminGlobal.name')])
                        : __('adminGlobal.form.title.createSlide', ['name' => __('adminGlobal.name')]) !!}
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body" x-data="{ tabSta: 'km' }">
                <div class="ItemTabListingLayout">
                    <div class="ItemTabListing">
                        <div class="ITab" :class="tabSta == 'km' ? 'active' : ''" @click="tabSta = 'km'">@lang('page_contact.form.khmer.label')
                        </div>
                        <div class="ITab" :class=" tabSta == 'en' ? 'active' : ''" @click="tabSta = 'en'">
                            @lang('page_contact.form.english.label')
                        </div>
                        <div class="ITab" :class="tabSta == 'zh' ? 'active' : ''" @click="tabSta = 'zh'">@lang('page_contact.form.chinese.label')
                        </div>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row" x-show="tabSta == 'en'">
                        <label>@lang('form.form.name.en')</label>
                        <input type="text" name="name[en]" value="{!! isset(json_decode($data->name)->en) ? json_decode($data->name)->en : old('name[en]') !!}"
                            placeholder="@lang('form.form.name.placeholder.en')">
                        @error('name.en')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row" x-show="tabSta == 'km'">
                        <label>@lang('form.form.name.km')<span>*</span></label>
                        <input type="text" name="name[km]" value="{!! isset(json_decode($data->name)->km) ? json_decode($data->name)->km : old('name[km]') !!}"
                            placeholder="@lang('form.form.name.placeholder.km')">
                        @error('name.km')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row" x-show="tabSta == 'zh'">
                        <label>@lang('form.form.name.zh')</label>
                        <input type="text" name="name[zh]" value="{!! isset(json_decode($data->name)->zh) ? json_decode($data->name)->zh : old('name[zh]') !!}"
                            placeholder="@lang('form.form.name.placeholder.zh')">
                        @error('name.zh')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Ordering<span>*</span> </label>
                        <input type="number" name="ordering"
                            value="{{ $data->ordering ? $data->ordering : old('ordering') }}"
                            placeholder="Enter name ordering ..." step="1" min="1">
                        @error('ordering')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('adminGlobal.form.image.label')</label>
                        <div class="form-select-photo image">
                            <div class="select-photo {!! isset($data) && $data->image != null ? 'active' : '' !!}">
                                <div class="icon">
                                    <i data-feather="image"></i>
                                </div>
                                <div class="title">
                                    <span>@lang('adminGlobal.form.image.placeholder')</span>
                                </div>
                            </div>
                            <div class="image-view {!! isset($data) && $data->image != null ? 'active' : '' !!}">
                                <img src="{!! isset($data->image) && $data->image != null ? $data->image_url : null !!}"
                                    onerror="(this).src='{{ asset('images/logo/default.png') }}'" alt="">
                            </div>
                            <input type="text" name="image" s-click-fn="selectImage(event)" autocomplete="off"
                                role="presentation" value="{{ isset($data->image) ? $data->image : null }}">
                            <input type="hidden" name="tmp_file"
                                value="{{ isset($data->image) && $data->image != null ? $data->image : null }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <label>@lang('adminGlobal.form.status.label')<span>*</span></label>
                        <select name="status">
                            <option value="1" {!! (request('id') && $data->status == 1) || old('status') == 1 ? 'selected' : '' !!}>@lang('adminGlobal.form.status.active')</option>
                            <option value="2" {!! (request('id') && $data->status == 2) || old('status') == 2 ? 'selected' : '' !!}>@lang('adminGlobal.form.status.disable')</option>
                        </select>
                    </div>
                </div>
                <div class="form-button">
                    <button type="submit" color="primary">
                        <i data-feather="save"></i>
                        <span>@lang('adminGlobal.form.button.submit')</span>
                    </button>
                    <button color="danger" type="button" s-click-link="{!! route('admin-slide-list', 1) !!}">
                        <i data-feather="x"></i>
                        <span>@lang('adminGlobal.form.button.cancel')</span>
                    </button>
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
    @include('admin::file-manager.popup')
@stop

@section('script')
    <script lang="ts">
        $(document).ready(function() {
            $validator("#form", {
                name: {
                    required: true,
                },
                status: {
                    required: true,
                },
            });
        });

        function selectImage(e) {
            fileManager({
                multiple: false,
                afterClose: (data, basePath) => {
                    if (data?.length > 0) {
                        const parent = e.target.closest('.form-select-photo');
                        e.target.value = data[0].path;
                        parent
                            .querySelector('.select-photo')
                            .classList.add('active');
                        parent
                            .querySelector('.image-view')
                            .classList
                            .add('active');
                        parent
                            .querySelector('.image-view')
                            .childNodes[0]
                            .nextElementSibling
                            .setAttribute('src', basePath + data[0].path);
                    }
                }
            })
        }
    </script>
    <script>
        const header = {
            headers: {
                "Content-Type": "application/x-www-form-urlencoded;charset=utf-8",
                Accept: "application/json",
            },
            responseType: "json",
        };
        document.addEventListener('alpine:init', () => {
            Alpine.data("xService", () => ({}));
        });
    </script>
@stop
