@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="xService">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-project-save', request('id')) !!}" method="POST"
              enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-project-list', 1) !!}"></i>
                    {{
                        request('id')
                        ? __('adminGlobal.form.title.updateProject', ['name' => __('adminGlobal.name')])
                        : __('adminGlobal.form.title.createProject', ['name' => __('adminGlobal.name')])
                    }}
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>Name <span>*</span> </label>
                        <input type="text" name="name" value="{{request('id') ? $data->name : old('name')}}"
                               placeholder="Enter name">
                        @error('name')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>VAT<span>*</span> </label>
                        <input type="text" name="vat_tin" value="{{request('id') ? $data->vat_tin : old('vat_tin')}}"
                               placeholder="Enter vat">
                        @error('vat_tin')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Phone <span>*</span> </label>
                        <input type="text" name="phone" value="{{request('id') ? $data->phone : old('phone')}}"
                               placeholder="Enter phone">
                        @error('phone')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>@lang('user.form.status.label')<span>*</span></label>
                        <select name="status">
                            <option value="1" {!! (request('id') && $data->status == 1) || old('status') == 1 ? 'selected' : '' !!}>Active</option>
                            <option value="2" {!! (request('id') && $data->status == 2) || old('status') == 2 ? 'selected' : '' !!}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-button">
                    <button type="submit" color="primary">
                        <i data-feather="save"></i>
                        <span>@lang('adminGlobal.form.button.submit')</span>
                    </button>
                    <button color="danger" type="button" s-click-link="{!! route('admin-project-list', 1) !!}">
                        <i data-feather="x"></i>
                        <span>@lang('adminGlobal.form.button.cancel')</span>
                    </button>
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
@stop

@section('script')
<script lang="ts">
    $(document).ready(function() {
        $validator("#form", {
            name: {
                required: true,
            },
            vat_tin:{
                required: true,
            },
            phone:{
                required: true,
            },
        });
    });
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