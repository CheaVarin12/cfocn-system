@extends('admin::shared.layout')
@section('layout')
    <div class="form-admin" x-data="xService">
        <div class="form-bg"></div>
        <form id="form" class="form-wrapper" action="{!! route('admin-customer-save', $data->id) !!}" method="POST" enctype="multipart/form-data">
            <div class="form-header">
                <h3>
                    <i data-feather="arrow-left" s-click-link="{!! route('admin-customer-list', 1) !!}"></i>
                    Update Customer
                </h3>
            </div>
            {{ csrf_field() }}
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>Customer ID<span>*</span></label>
                        <input type="text" name="customer_code" placeholder="" value="{!! $data->customer_code !!}">
                        @error('customer_code')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Register Date<span>*</span> </label>
                        <input type="text" name="register_date" value="{!! request('id') && isset($data->register_date) ? $data->register_date : old('register_date') !!}" id="register_date"
                            autocomplete="off">
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Name EN<span>*</span></label>
                        <input type="text" name="name_en" placeholder="" value="{!! $data->name_en !!}">
                        @error('name_en')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Name KH</label>
                        <input type="text" name="name_kh" placeholder="" value="{!! $data->name_kh !!}">
                        @error('name_kh')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Phone <span>*</span></label>
                        <input type="text" name="phone" placeholder="" value="{!! $data->phone !!}">
                        @error('phone')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Email </label>
                        <input type="email" name="email" placeholder="" value="{!! $data->email !!}">
                        @error('email')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>VAT</label>
                        <input type="text" name="vat_tin" placeholder="" value="{!! $data->vat_tin !!}">
                        @error('vat_tin')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Status<span>*</span></label>
                        <select name="status">
                            <option value="1" {!! (request('id') && $data->status == 1) || old('status') == 1 ? 'selected' : '' !!}>Active</option>
                            <option value="2" {!! (request('id') && $data->status == 2) || old('status') == 2 ? 'selected' : '' !!}>In Active</option>
                        </select>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Type<span>*</span></label>
                        <select name="type">
                            <option value="">Select type...</option>
                            <option value="1" {!! (request('id') && $data->type == 1) || old('type') == 1 ? 'selected' : '' !!}>លក់អោយបុគ្គលជាប់អាករ</option>
                            <option value="2" {!! (request('id') && $data->type == 2) || old('type') == 2 ? 'selected' : '' !!}>លក់មិនជាប់អាករ</option>
                            <option value="3" {!! (request('id') && $data->type == 3) || old('type') == 3 ? 'selected' : '' !!}>លក់អោយអ្នកប្រើប្រាស់</option>
                        </select>
                        @error('type')
                        <label class="error">{{ $message }}</label>
                    @enderror
                    </div>
                    <div class="form-row">
                        <label>In Active Date</label>
                        <input type="text" name="in_active_date"
                            value="{{ $data->in_active_date ?? old('in_active_date') }}" id="in_active_date"
                            autocomplete="off" placeholder="Select in active date">
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Address EN</label>
                        <textarea placeholder="" name="address_en" row="3">{!! $data->address_en !!}</textarea>
                        @error('address_en')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="form-row">
                        <label>Address KH</label>
                        <textarea placeholder="" name="address_kh" row="3">{!! $data->address_kh !!}</textarea>
                        @error('address_kh')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Attention</label>
                        <input type="text" name="attention" value="{!! $data->attention !!}" placeholder="Enter Attention">
                    </div>
                </div>
                <input type="hidden" name="type_status" :value="customer_type_status" />
                <div class="form-button">
                    <button type="submit" color="green" @click="customer_type_status='update_save_new'">
                        <i data-feather="save"></i>
                        <span>(Update & Save) New</span>
                    </button>
                    <button type="submit" color="primary">
                        <i data-feather="save"></i>
                        <span>Update</span>
                    </button>
                    <button color="danger" type="button" s-click-link="{!! route('admin-customer-list', 1) !!}">
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
                customer_code: {
                    required: true,
                },
                phone: {
                    required: true,
                },
                register_date: {
                    required: true,
                }
            });
            $("#register_date").datepicker({
                yearRange: "-100:+100",
                changeYear: true,
                dateFormat: "yy-mm-dd",
            });
            $("#in_active_date").datepicker({
                setDate: new Date(),
                changeYear: true,
                gotoCurrent: true,
                yearRange: "-100:+100",
                dateFormat: "yy-mm-dd",
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
            Alpine.data("xService", () => ({
                customer_type_status: "update",
                init() {
                    console.log(this.customer_type_status, 'customertypeStatus');
                }
            }));
        });
    </script>
@stop
