@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="xService">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-ftth-service-store', $service?->id) !!}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-ftth-service-list', 1) !!}"></i>
                    Create FTTH Service
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>Name<span>*</span></label>
                        <input type="text" name="name" value="{{ $service ? $service->name : '' }}" placeholder="Enter name" autocomplete="off">
                        @error('name')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Status<span>*</span></label>
                        <select name="status">
                            <option value="1" {{ $service && $service->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="2" {{ $service && $service->status == 2 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-row">
                        <label>Description<span>*</span></label>
                        <textarea placeholder="Enter description" name="description" rows="5">{!! $service ? $service->description : '' !!}</textarea>
                        @error('description')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-button">
                    <button type="submit" color="primary">
                        <i data-feather="save"></i>
                        <span>{{ $service && $service->id ? 'Update' : 'Save' }}</span>
                    </button>
                    <button color="danger" type="button" s-click-link="{!! route('admin-ftth-service-list', 1) !!}">
                        <i data-feather="x"></i>
                        <span>Cancel</span>
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
            description:{
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
