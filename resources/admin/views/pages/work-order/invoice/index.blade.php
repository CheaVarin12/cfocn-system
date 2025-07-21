@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Invoice Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/work-order/invoice/list/all') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-invoice-list', 'all') !!}">
                            All</div>
                        <div class="menu-item {!! Request::is('admin/work-order/invoice/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-invoice-list', 2) !!}">
                            Un Paid</div>
                        <div class="menu-item {!! Request::is('admin/work-order/invoice/list/3') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-invoice-list', 3) !!}">
                            Void</div>
                        {{-- <div class="menu-item {!! Request::is('admin/work-order/invoice/list/5') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-invoice-list', 5) !!}">
                            Auto</div> --}}
                        <div class="menu-item {!! Request::is('admin/work-order/invoice/list/7') ? 'active' : '' !!}" s-click-link="{!! route('admin-work-order-invoice-list', 7) !!}">
                            Copy</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row w80" style="width: 70px;min-width:70px !important;">
                            <select name="sort" id="type_id" name="type_id">
                                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>
                                    ASC</option>
                                <option value="desc"
                                    {{ request('sort') == 'desc' || request('sort') == '' ? 'selected' : '' }}>DESC</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
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
                    <template x-if="selectedInvoices.length == 0 && status !=7">
                        <a class="ms-1 px-2 pt-1 "
                            style="width: 102px;color:white;background-color:#024de3;border-radius: 5px;height: 35px;cursor: pointer;text-align: center;"
                            onclick="$onConfirmMessage(
                                '{!! route('admin-work-order-invoice-copy-invoice-last-month') !!}',
                                  '@lang('Are you sure you want to copy invoices from last month ?')',
                                    {
                                     confirm: '@lang('Copy')',
                                     cancel: '@lang('dialog.button.cancel')'
                                                                },
                                );">
                            <span style="font-size:13px">Copy Invoice</span>
                        </a>
                    </template>
                    <template x-if="selectedInvoices && selectedInvoices.length > 0 && status !=7">
                        <button class="ms-1 px-2 pt-1"
                            style="width: 106px;color:white;background-color:#024de3;border-radius: 5px;height: 35px;cursor: pointer;text-align: center;"
                            @click="copySelectedInvoices">
                            <span style="font-size:13px;margin-top: -3px;">Copy Selected</span>
                        </button>
                    </template>
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.work-order.invoice.table')
        </div>
    </div>
    @include('admin::pages.work-order.invoice.copy.created')
    @include('admin::pages.work-order.invoice.copy.createSaled')
    @include('admin::pages.work-order.invoice.edit.edit')
    @include('admin::pages.work-order.invoice.edit.editSale')
    @include('admin::pages.work-order.invoice.detail.index')
    @include('admin::pages.work-order.invoice.receipt.create')
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
            selectedInvoices: [],
            status: null,
            init() {
                feather.replace();
                this.status = `{{ request('status') }}`;

            },
            copyInvoiceDialog(data) {
                if (data?.order?.type_id != 2) {
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
            editInvoiceDialog(data) {
                if (data?.order?.type_id != 2) {
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
                    title: "Invoice Detail",
                    config: {
                        width: "55%",
                    }
                });
            },
            dmcSubmitDialog(data, resBtn = null) {
                invoiceDetail({
                    active: true,
                    data: data,
                    dmcBtn: true,
                    typeBtnStatus: resBtn,
                    title: "Invoice DMC Submit",
                    config: {
                        width: "55%",
                    }
                });
            },
            invoiceDelete(item) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "Delete Invoice",
                        message: "Are you sure to delete file can't restore when delete ?",
                        btnClose: "Close",
                        btnSave: "Yes",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.submitLoading = true;
                            setTimeout(() => {
                                Axios({
                                    url: `{{ route('admin-work-order-invoice-destroy') }}`,
                                    method: 'POST',
                                    data: {
                                        id: item?.id,
                                    }
                                }).then((res) => {
                                    this.submitLoading = false;
                                    if (res.data == "success") {
                                        let currentFullUrl =
                                            '{!! url()->full() !!}';
                                        reloadUrl(currentFullUrl);
                                    }
                                }).catch((e) => {
                                    this.submitLoading = false;

                                }).finally(() => {
                                    this.submitLoading = false;
                                });
                            }, 500);
                        }
                    }
                });
            },
            createReceiptDialog(item) {
                createReceipt({
                    active: true,
                    data: {
                        ...item
                    },
                    title: "Create Receipt",
                    config: {
                        width: "55%",
                    },
                    afterClose: (res) => {
                        let currentFullUrl = '{!! url()->full() !!}';
                        reloadUrl(currentFullUrl);
                    }
                });
            },
            invoiceVoidDialog(data) {
                console.log(data);
            },
            toggleSelection(id) {
                const index = this.selectedInvoices.indexOf(id);
                if (index > -1) {
                    this.selectedInvoices.splice(index, 1);
                } else {
                    this.selectedInvoices.push(id);
                }
            },
            selectAll($event) {
                if ($event.target.checked) {
                    this.selectedInvoices = @json($data->pluck('id')); // Laravel collection of IDs
                } else {
                    this.selectedInvoices = [];
                }
            },
            copySelectedInvoices() {
                this.$store.confirmDialog.open({
                    data: {
                        title: "Copy Invoices",
                        message: "Are you sure you want to copy the selected invoices?",
                        btnClose: "Cancel",
                        btnSave: "Yes",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            Axios.post(`{!! route('admin-work-order-invoice-copy-invoice-last-month-selected') !!}`, {
                                    ids: this.selectedInvoices
                                })
                                .then(res => {
                                    if (res.data?.status === 'success') {
                                        Toast({
                                            title: 'Copied!',
                                            message: 'Invoices copied successfully.',
                                            status: 'success',
                                            size: 'small',
                                            duration: 4000,
                                        });
                                        reloadUrl(`{!! url()->full() !!}`);
                                    } else {
                                        alert('Something went wrong!');
                                    }
                                })
                                .catch(err => {
                                    console.error('Copy failed:', err);
                                });
                        }
                    }
                });
            }
        }));
    </script>
@stop
