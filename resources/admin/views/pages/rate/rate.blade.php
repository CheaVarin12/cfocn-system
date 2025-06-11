@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{{ route('admin-rate-save') }}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    Set Exchange Rate
                </h3>
            </div>
            @csrf
            <div class="form-body">
                <div class="row-1">
                    <div class="form-row">
                        <label>Exchange Rate <span>*</span></label>
                        <input type="number" name="rate" value="{{$rate?$rate->rate:old('rate')}}"
                            placeholder="Enter rate" min="0" oninput="validity.valid||(value='');">
                    </div>
                </div>
                <div class="form-button">
                    @can('exchange-rate')
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
