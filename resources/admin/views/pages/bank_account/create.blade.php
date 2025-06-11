@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="xBankAccount">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-bank-account-save') !!}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-bank-account-list',1) !!}"></i>
                    Create Bank Account
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>Bank Name<span>*</span></label>
                        <input type="text" name="bank_name" placeholder="Enter bank name">
                        @error('bank_name')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Account Name</label>
                        <input type="text" name="account_name" placeholder="Enter bank name">
                        @error('account_name')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Account Number<span>*</span></label>
                        <input type="text" name="account_number" placeholder="Enter account name">
                        @error('account_number')
                            <label class="error">{{ $message }}</label>
                        @enderror
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
                    <button color="danger" type="button" s-click-link="{!! route('admin-service-list', 1) !!}">
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
    <script lang="ts">
        $(document).ready(function() {
            $validator("#form", {
                bank_name: {
                    required: true,
                },
                account_number: {
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
            Alpine.data("xBankAccount", () => ({}));
        });
    </script>
@stop
