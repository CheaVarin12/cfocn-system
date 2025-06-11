@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Credit Note'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/credit-note/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-credit-note-list', 1) !!}">
                            Active</div>
                        <div class="menu-item {!! Request::is('admin/credit-note/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-credit-note-list', 2) !!}">
                            Disable</div>
                        {{-- <div class="menu-item {!! Request::is('admin/credit-note/list/all') ? 'active' : '' !!}" s-click-link="{!! route('admin-credit-note-list', 'all') !!}">
                            All</div> --}}
                        {{-- <div class="menu-item {!! Request::is('admin/credit-note/list/3') ? 'active' : '' !!}" s-click-link="{!! route('admin-credit-note-list', 3) !!}">
                            DMC</div> --}}
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row iconAc">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}">
                            <div class="div">
                                <i data-feather="info"></i>
                                <div class="popUpBtntext">
                                    <div class="popUpText">
                                        <label>Search Keyword :</label>
                                        <p>Credit note number</p>
                                        <p>Invoice number</p>
                                        <p>Customer (name, code)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <select name="search_project">
                                <option value="">Select Project ...</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ request('search_project') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="from_date" placeholder="Start Date" id="fromDate"
                                autocomplete="off" value="{!! request('from_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="to_date" placeholder="End Date" id="toDate" autocomplete="off"
                                value="{!! request('to_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                    </form>
                    @can('credit-note-create')
                        <button type="button" class="btn-create" @click="create()">
                            <i data-feather="plus-circle"></i>
                            <span>Create</span>
                        </button>
                    @endcan
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.credit_note.table')
        </div>
    </div>
    @include('admin::pages.credit_note.detail.index')
    @include('admin::pages.credit_note.store.insert')
@stop
@section('script')
    <script>
        $(document).ready(function() {
            $('#fromDate').datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: '-10:+10',
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#toDate').datepicker('option', 'minDate', select)
                }
            });
            $('#toDate').datepicker({
                changeYear: true,
                gotoCurrent: true,
                yearRange: '-10:+10',
                dateFormat: "yy-mm-dd",
                onSelect: (select) => {
                    $('#fromDate').datepicker('option', 'maxDate', select)
                }
            });
        });
    </script>
    <script>
        Alpine.data('XData', () => ({
            init() {},
            copyInvoiceDialog(data) {
                if (data?.purchase?.type_id != 2) {
                    copyInvoiceCreate({
                        active: true,
                        data: data
                    });
                } else {
                    copyInvoiceCreateSale({
                        active: true,
                        data: data
                    });
                }
            },
            create() {
                insertCreditNote({
                    active: true,
                    data: null,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "70%",
                    }
                });
            },
            edit(data) {
                insertCreditNote({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Update",
                    config: {
                        width: "70%",
                    }
                });
            },
            editInvoiceDialog(data) {
                if (data?.purchase?.type_id != 2) {
                    editInvoice({
                        active: true,
                        data: data
                    });
                } else {
                    EditInvoiceSale({
                        active: true,
                        data: data
                    });
                }
            },
            invoiceDetailDialog(data) {
                invoiceDetail({
                    active: true,
                    data: data,
                    dmcBtn: false,
                    title: "Credit note detail",
                    config: {
                        width: "50%",
                    }
                });
            },
            dmcSubmitDialog(data) {
                invoiceDetail({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Credit note DMC Submit",
                    config: {
                        width: "50%",
                    }
                });
            }
        }));
    </script>
@stop
