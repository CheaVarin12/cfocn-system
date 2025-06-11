@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-contact-save', ['id' => $data?->id, 'type' => request('type')]) !!}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    Contact Us
                </h3>
            </div>
            @csrf
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>Phone <span>*</span></label>
                        <input type="number" name="phone" value="{!!$data->phone!!}"
                            placeholder="">
                    </div>
                    <div class="form-row">
                        <label>Email <span>*</span></label>
                        <input type="text" name="email" value="{!!$data->email!!}"
                            placeholder="">
                    </div>
                </div>
                <div class="row">
                    <div class="form-row">
                        <label>Address</label>
                        <textarea placeholder="" name="address" row="3">{!!$data->address!!}</textarea>
                        @error('address')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-row">
                        <label>Status<span>*</span></label>
                        <select name="status">
                            <option value="1" {!! (isset($data->status) && $data->status == 1) || old('status') == 1 ? 'selected' : '' !!}> Active</option>
                            <option value="2" {!! (isset($data->status) && $data->status == 2) || old('status') == 2 ? 'selected' : '' !!}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row-1">
                    <div class="form-row">
                        <label>Image</label>
                        <div class="form-select-photo image">
                            <div class="select-photo {!! isset($data) && $data->image != null ? 'active' : '' !!}">
                                <div class="icon">
                                    <i data-feather="image"></i>
                                </div>
                                <div class="title">
                                    <span>Image</span>
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
                </div>
                <div class="form-button">
                    @can('contact-update')
                        <button type="submit" color="primary">
                            <i data-feather="save"></i>
                            <span> Save</span>
                        </button>
                    @endcan
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
    @include('admin::file-manager.popup')
@endsection

@section('script')
    <script lang="ts">
        $(document).ready(function() {
            $validator("#form", {
                address: {
                    required: true,
                },
                phone: {
                    required: true,
                },
                email: {
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
@endsection
