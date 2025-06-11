@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Credit Note'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/work-order/credit-note/list/1') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-credit-note-list', 1) !!}">
                            Active
                        </div>
                        <div class="menu-item {!! Request::is('admin/work-order/credit-note/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-credit-note-list', 2) !!}">
                            Disable
                        </div>
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
                                    <option value="{{ $project->id }}" {{ request('search_project') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="from_date" placeholder="From Date" id="fromDate"
                                autocomplete="off" value="{!! request('from_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="to_date" placeholder="To Date" id="toDate" autocomplete="off"
                                value="{!! request('to_date') !!}" readonly>
                            <i data-feather="calendar"></i>
                        </div>
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto">
                            <i data-feather="search"></i>
                        </button>
                    </form>
                    @can('work-order-credit-note')
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
            @include('admin::pages.work-order.credit_note.table')
        </div>
    </div>
    @include('admin::pages.work-order.credit_note.detail.index')
    @include('admin::pages.work-order.credit_note.store.insert')
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
            invoiceDetailDialog(data) {
                invoiceDetail({
                    active: true,
                    data: data,
                    dmcBtn: false,
                    title: "Credit Note Detail",
                    config: {
                        width: "60%",
                    }
                });
            },
            dmcSubmitDialog(data) {
                invoiceDetail({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    title: "Credit Note DMC Submit",
                    config: {
                        width: "60%",
                    }
                });
            }
        }));
    </script>
@stop
