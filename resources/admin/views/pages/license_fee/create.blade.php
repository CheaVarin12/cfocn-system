@extends('admin::shared.layout')
@section('layout')
<style>
    .ui-datepicker-calendar {
        display: none;
    }
    .ui-datepicker .ui-datepicker-buttonpane button {
        background: #0099ff !important;
    }
    #ui-datepicker-div{
        width: 300px !important;
    }
</style>
    <div class="form-admin" >
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-license-fee-save') !!}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-license-fee-list', 1) !!}"></i>
                    Create License Fee
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row">
                    <div class="form-row">
                        <label>Project<span>*</span></label>
                        <select name="project_id">
                            <option value="">Select project...</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Percentage<span>*</span></label>
                        <input type="number"  oninput="validity.valid||(value='');" min="0" step="any" name="percentage" placeholder="Enter Percentage">
                    </div>
                    <div class="form-row">
                        <label>License Fee<span>*</span></label>
                        <input type="number" oninput="validity.valid||(value='');" min="0" step="any" name="license_fee" placeholder="Enter name">
                    </div>
               
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Year<span>*</span></label>
                        <input type="text" name="year" id="year"  placeholder="Select year">
                    </div>
                    <div class="form-row">
                        <label>Status<span>*</span></label>
                        <select name="status">
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-button">
                    <button type="submit" color="primary">
                        <i data-feather="save"></i>
                        <span>Submit</span>
                    </button>
                    <button color="danger" type="button" s-click-link="{!! route('admin-license-fee-list', 1) !!}">
                        <i data-feather="x"></i>
                        <span>Cancel</span>
                    </button>
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
    @include('admin::file-manager.popup')
@stop
@section('script')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script lang="ts">
    $(document).ready(function() {
        $validator("#form", {
            project_id: {
                required: true,
            },
            percentage: {
                required: true,
            },
            license_fee:{
                required: true,
            },
            year:{
                required: true,
            },
            stats:{
                required: true,
            },
        });
        $("#year").datepicker({
                // changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                // yearRange: "-1:+1",
                dateFormat: "yy",
                onClose: function() { 
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(year, 1));
                }
            });
    });
</script>
@stop
