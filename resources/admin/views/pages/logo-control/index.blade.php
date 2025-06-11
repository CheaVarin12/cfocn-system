@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{{ route('admin-logo-control-save') }}" method="POST" >
            <div class="form-header">
                <h3>
                    Lock Logo
                </h3>
            </div>
            @csrf
            <div class="form-body">
                <div class="row-1">
                    <div class="form-row">
                        <label>Status <span>*</span></label>
                        <select name="status">
                            <option value="1" {{ ($logoControl && $logoControl->status == true) || old('status') === '1' ? 'selected' : '' }}>Show</option>
                            <option value="0" {{ ($logoControl && $logoControl->status == false) || old('status') === '0' ? 'selected' : '' }}>Hide</option>
                        </select>
                    </div>
                </div>                
                <div class="form-button">
                    @can('logo-control')
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
                rate: {
                    required: true,
                },
            });
        });

    </script>
@endsection
