@extends('admin::shared.layout')
@section('layout')
    <style>
        .button-create {
            height: 35px;
            border-radius: 4px;
            background-color: blue;
            color: white;
        }

        /* Base styles */
        body {
            font-family: Arial, sans-serif;
        }

        .dropdown-create {
            position: relative;
            display: inline-block;
        }

        .dropdown-toggle-create {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .dropdown-toggle-create:focus {
            outline: none;
        }

        .dropdown-menu-create {
            list-style: none;
            margin: 0;
            padding: 0;
            position: absolute;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            display: none;
            /* Hidden by default */
            width: 100%;
            z-index: 1000;
        }

        .dropdown-menu-create li {
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu-create li:hover {
            background-color: #f1f1f1;
        }

        .select-option .select-option-body .data-list .data-list-item .title span {
            font-size: 11px !important;
        }
    </style>
    <div class="content-wrapper" x-data="XData">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Invoice Management'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/invoice/list/all') ? 'active' : '' !!}" s-click-link="{!! route('admin-invoice-list', 'all') !!}">
                            All</div>
                        <div class="menu-item {!! Request::is('admin/invoice/list/2') ? 'active' : '' !!}" s-click-link="{!! route('admin-invoice-list', 2) !!}">
                            Un Paid</div>
                        <div class="menu-item {!! Request::is('admin/invoice/list/3') ? 'active' : '' !!}" s-click-link="{!! route('admin-invoice-list', 3) !!}">
                            Void</div>
                        <div class="menu-item {!! Request::is('admin/invoice/list/4') ? 'active' : '' !!}" s-click-link="{!! route('admin-invoice-list', 4) !!}">
                            Draft</div>
                        {{-- <div class="menu-item {!! Request::is('admin/invoice/list/5') ? 'active' : '' !!}" s-click-link="{!! route('admin-invoice-list', 5) !!}">
                            Auto</div> --}}
                        <div class="menu-item {!! Request::is('admin/invoice/list/7') ? 'active' : '' !!}" s-click-link="{!! route('admin-invoice-list', 7) !!}">
                            Copy</div>
                    </div>
                </div>
                <div class="header-action-button" x-data="feather.replace()">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row w80" style="width: 70px;min-width:70px !important;">
                            <select name="sort" id="type_id" name="type_id">
                                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>
                                    ASC</option>
                                <option value="desc"
                                    {{ request('sort') == 'desc' || request('sort') == '' ? 'selected' : '' }}>DESC</option>
                            </select>
                        </div>
                        <div class="form-row iconAc" style="min-width: 100px;">
                            <input type="text" name="search" placeholder="Search" value="{!! request('search') !!}">
                            <div class="div">
                                <i data-feather="info"></i>
                                <div class="popUpBtntext">
                                    <div class="popUpText">
                                        <label>Search Keyword :</label>
                                        <p>Invoice Number</p>
                                        <p>Customer Name</p>
                                        <p>P.O/LO Number</p>
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
                    @can('invoice-create')
                        <div class="dropdown-create">
                            <button class="btn-create" @click="dropdownCreate()">
                                <i data-feather="plus-circle"></i>
                                <span>Create</span>
                            </button>
                            <ul class="dropdown-menu-create">
                                <li @click="createInvoiceDialog('leasing')">Leasing</li>
                                <li @click="createInvoiceDialog('sale')">Sale</li>
                            </ul>
                        </div>
                    @endcan
                    @can('invoice-copy')
                        <template x-if="selectedInvoices.length == 0 && status !=7">
                            <a class="ms-1 px-2 pt-1 "
                                style="width: 102px;color:white;background-color:#024de3;border-radius: 5px;height: 35px;cursor: pointer;text-align: center;"
                                onclick="$onConfirmMessage(
                                '{!! route('admin-invoice-copy-invoice-last-month') !!}',
                                  '@lang('Are you sure you want to copy invoices from last month ?')',
                                    {
                                     confirm: '@lang('Copy')',
                                     cancel: '@lang('dialog.button.cancel')'
                                                                },
                                );">
                                <span style="font-size:13px">Copy Invoice</span>
                            </a>
                        </template>
                    @endcan
                    @can('invoice-copy')
                        <template x-if="selectedInvoices && selectedInvoices.length > 0 && status !=7">
                            <button class="ms-1 px-2 pt-1"
                                style="width: 106px;color:white;background-color:#024de3;border-radius: 5px;height: 35px;cursor: pointer;text-align: center;"
                                @click="copySelectedInvoices">
                                <span style="font-size:13px;margin-top: -3px;">Copy Selected</span>
                            </button>
                        </template>
                    @endcan
                    @can('invoice-dmc-submit')
                        <button class="ms-1 px-2 pt-1"
                            style="width: 106px;color:white;background-color:#00b74a;border-radius: 5px;height: 35px;cursor: pointer;text-align: center;"
                            @click="DMCSubmitMultipleInvoices">
                            <span style="font-size:13px;margin-top: -3px;">Send to Dmc</span>
                        </button>
                    @endcan
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('admin::pages.invoice.table')
        </div>
        <template x-if="submitLoading">
            @include('admin::components.spinner')
        </template>
    </div>
    @include('admin::pages.invoice.copy.invoice-multiple-pac-leasing')
    @include('admin::pages.invoice.copy.invoice-multiple-pac-sale')
    @include('admin::pages.invoice.copy.created')
    @include('admin::pages.invoice.copy.createSaled')
    @include('admin::pages.invoice.edit.edit')
    @include('admin::pages.invoice.edit.editSale')
    @include('admin::pages.invoice.detail.index')
    @include('admin::pages.invoice.receipt.create')
    @include('admin::pages.invoice.create.leasing-invoice')
    @include('admin::pages.invoice.create.sale-invoice')
    @include('admin::pages.invoice.edit.edit-invoice-multiple-pac-leasing')
    @include('admin::pages.invoice.edit.edit-invoice-multiple-pac-sale')
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
            submitLoading: false,
            init() {
                feather.replace();
                this.status = `{{ request('status') }}`;

            },
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
                                    url: `{{ route('admin-invoice-destroy') }}`,
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
            invoiceVoidDialog(data) {},
            dropdownCreate() {
                const dropdownMenu = document.querySelector('.dropdown-menu-create');
                dropdownMenu.style.display =
                    dropdownMenu.style.display === 'block' ? 'none' : 'block';
            },
            createInvoiceDialog(type) {
                if (type === 'leasing') {
                    createInvoiceLeasing({
                        active: true,
                    });
                } else {
                    createInvoiceSale({
                        active: true,
                    });
                }
            },
            editInvoiceMultiPacDialog(data) {
                if (data?.purchase?.type_id != 2) {
                    editInvoiceMultiplePacLeasing({
                        active: true,
                        data: data
                    });
                } else {
                    editInvoiceMultiplePacSale({
                        active: true,
                        data: data
                    });
                }
            },
            copyInvoiceMultiPacDialog(data) {
                if (data?.purchase?.type_id != 2) {
                    copyInvoiceMultiplePacLeasing({
                        active: true,
                        data: data
                    });
                } else {
                    copyInvoiceMultiplePacSale({
                        active: true,
                        data: data
                    });
                }
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
                        this.submitLoading = true;
                        if (result) {
                            Axios.post(`{!! route('admin-invoice-copy-invoice-last-month-selected') !!}`, {
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
                                    this.submitLoading = false;
                                })
                                .catch(err => {
                                    this.submitLoading = false;
                                    console.error('Copy failed:', err);
                                });
                        } else {
                            this.submitLoading = false;
                        }
                    }
                });
            },
            DMCSubmitMultipleInvoices() {
                this.$store.confirmDialog.open({
                    data: {
                        title: "Send Invoices to Dmc",
                        message: "Are you sure you want to send the selected invoices?",
                        btnClose: "Cancel",
                        btnSave: "Yes",
                    },
                    afterClosed: (result) => {
                        this.submitLoading = true;
                        if (result) {
                            Axios.post(`{!! route('admin-invoice-dmc-submit-multiple') !!}`, {
                                    ids: this.selectedInvoices
                                })
                                .then(res => {
                                    if (res.data?.status === 'success') {
                                        Toast({
                                            title: 'Send!',
                                            message: 'Send successfully.',
                                            status: 'success',
                                            size: 'small',
                                            duration: 4000,
                                        });
                                        this.submitLoading = false;
                                        reloadUrl(`{!! url()->full() !!}`);
                                    }
                                    if (res.data.message == "unsuccess") {
                                        let con_message = res.data
                                            .connection_status ==
                                            "unable_to_connect" ?
                                            "unable to connect" :
                                            "problem (lose or time out) try again";
                                        this.$store
                                            .DMCSubmitStatusDialog
                                            .open({
                                                data: {
                                                    title: "Lose Connection",
                                                    message: `Server ${con_message} !`,
                                                    btnClose: "Close",
                                                    btnSave: "Yes",
                                                }
                                            });
                                        this.submitLoading = false;
                                    }
                                })
                                .catch(err => {
                                    this.submitLoading = false;
                                    Toast({
                                        title: 'error!',
                                        message: err.response.data.errors.ids,
                                        status: 'danger',
                                        size: 'small',
                                        duration: 4000,
                                    });
                                });
                        } else {
                            this.submitLoading = false;
                        }
                    }
                });
            }
        }));
    </script>
@stop
