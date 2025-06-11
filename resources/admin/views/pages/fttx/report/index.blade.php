@extends('admin::shared.layout')
@section('layout')
    {{ HTML::style('admin-public/css/custom_style/dropdown-select.css') }}
    <style>
        .select2-container--default .select2-selection--single {
            height: 26px !important;
            border: none !important;
            width: 200px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            padding-left: 15px;
            width: 41px;
            height: 26px;
            border: solid 1px #afafaf;
            border-radius: 7px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            display: none;
        }
    </style>
    <div class="content-wrapper" x-data="xFttxReport">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Annual Report'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row"></div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row" style="min-width: 200px;">
                            <select id="customer_id" name="customer_id" x-model="formData.customer_id"
                                x-init="fetchSelectCustomer()">
                                <option value="">Select customer...</option>
                            </select>
                        </div>
                        <div class="form-row" style="width: 200px;display: inline;">
                            <input type="text" x-model="selectedPosSpeed" placeholder="Pos Speed" readonly
                                @click="showHideDropdown('pos_speed_id')">
                            <div class="dropdown" x-show="showPosSpeedDropdown">
                                <ul class="report-ul">
                                    <li class="report-li">
                                        <input type="checkbox" class="report-input" @click="toggleSelectAllPosSpeed($event)"
                                            :checked="formData.pos_speed_id.length === posSpeed.length">
                                        <span>Select All</span>
                                    </li>
                                    @foreach ($posSpeeds as $value)
                                        <li class="report-li">
                                            <input type="checkbox" class="report-input" x-model="formData.pos_speed_id"
                                                value="{{ $value->id }}">
                                            <span>{{ $value->split_pos }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <input type="hidden" name="pos_speed_id" x-model="formData.pos_speed_id">
                        </div>
                        <div class="form-row" style="width: 200px;display: inline;">
                            <input type="text" x-model="selectedStatus" placeholder="Status" readonly
                                @click="showHideDropdown('fttx_status')">
                            <div class="dropdown" x-show="showStatusDropdown">
                                <ul class="report-ul">
                                    <li class="report-li">
                                        <input class="report-input" type="checkbox" @click="toggleSelectAllStatus($event)"
                                            :checked="formData.fttx_status.length === statusOptions.length">
                                        <span>Select All</span>
                                    </li>
                                    @foreach (config('dummy.fttx_status') as $status)
                                        <li class="report-li">
                                            <input type="checkbox" class="report-input" x-model="formData.fttx_status"
                                                value="{{ $status['key'] }}">
                                            <span>{{ $status['text'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="fttx_status" x-model="formData.fttx_status">
                        <div class="form-row w80">
                            <input type="text" name="from_date" placeholder="From Date" value="{!! isset($from_date) && $from_date ? $from_date : request('from_date') !!}"
                                id="from_date" autocomplete="off">
                            <i data-feather="calendar" id="from_date"></i>
                        </div>
                        <div class="form-row w80">
                            <input type="text" name="to_date" placeholder="To Date" value="{!! $to_date ? $to_date : request('to_date') !!}"
                                id="to_date" autocomplete="off">
                            <i data-feather="calendar"></i>
                        </div>

                        <input type="hidden" name="check" x-model="check">
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto"
                            @click=" check = ''">
                            <i data-feather="search"></i>
                        </button>
                        <button type="submit" class="btn-create bg-success" @click=" check = 'export'">
                            <i data-feather="arrow-down-circle"></i>
                            <span>Export Excel</span>
                        </button>
                    </form>
                    <button s-click-link="{!! url('admin/fttx/report/list?fttx_status=2') !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.fttx.report.table')
            @include('admin::pages.fttx.report.view-details.index')
        </div>
        <template x-if="submitLoading">
            @include('admin::components.spinner')
        </template>
    </div>
@stop
@section('script')
    <script>
        $(document).ready(function() {
            $("#from_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                gotoCurrent: true,
                yearRange: "-50:+0",
                onSelect: function(selected) {
                    $("#to_date").datepicker("option", "minDate", selected)
                }
            });
            $("#to_date").datepicker({
                minDate: `{{ isset($from_date) && $from_date ? $from_date : 0 }}`,
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                gotoCurrent: true,
                yearRange: "-50:+0",
                onSelect: function(selected) {
                    $("#from_date").datepicker("option", "maxDate", selected)
                }
            });
        });
    </script>
    <script>
        Alpine.data('xFttxReport', () => ({
            search: '',
            excel: '',
            check: '',
            typeSearch: 'date',
            checkProject: '',
            submitLoading: false,
            customerData: @json($customer) ?? null,
            showPosSpeedDropdown: false,
            showStatusDropdown: false,
            posSpeed: @json($posSpeeds),
            statusOptions: Object.values(@json(config('dummy.fttx_status'))),
            formData: {
                customer_id: @json(request('customer_id')),
                check: @json(request('check')),
                pos_speed_id: @json(request('pos_speed_id')) ?
                    @json(request('pos_speed_id')).toString().split(',').map(id => parseInt(id)) : [],
                fttx_status: @json(request('fttx_status')) ?
                    @json(request('fttx_status')).toString().split(',').map(id => parseInt(id)) : [],
                from_date: @json(request('from_date')),
                to_date: @json(request('to_date')),
            },
            init() {
                if (this.customerData) {
                    this.appendSelect2HtmlCurrentSelect('customer_id', this.customerData?.id, this.customerData
                        ?.name_en);
                }
            },
            fetchSelectCustomer() {
                $('#customer_id').select2({
                    placeholder: `Select Isp...`,
                    allowClear: true, // Enables the clear button
                    ajax: {
                        url: '{{ route('admin-select-customer') }}',
                        dataType: 'json',
                        type: "GET",
                        quietMillis: 50,
                        data: (param) => {
                            return {
                                search: param.term
                            };
                        },
                        processResults: (data) => {
                            return {
                                results: $.map(data.data, (item) => {
                                    return {
                                        text: item?.name_en ? item?.name_en : item?.name_kh,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                }).on('select2:open', (e) => {
                    document.querySelector('.select2-search__field').focus();
                }).on('select2:close', async (eventClose) => {
                    const _id = eventClose.target.value;
                    this.formData.customer_id = _id;
                });

                // Handle clear event
                $('#customer_id').on('select2:clear', () => {
                    this.formData.customer_id = ''; // Reset value
                });
            },
            appendSelect2HtmlCurrentSelect(select2ID, id, name) {
                var option = "<option selected></option>";
                var optionHTML = $(option).val(id ? id : null).text(name ? name : name);
                $(`#${select2ID}`).append(optionHTML).trigger('change');
            },
            showHideDropdown(type = null) {
                if (type) {
                    if (type === 'pos_speed_id') {
                        this.showPosSpeedDropdown = !this.showPosSpeedDropdown;
                    } else if (type === 'fttx_status') {
                        this.showStatusDropdown = !this.showStatusDropdown;
                    }
                }
            },
            get selectedPosSpeed() {
                const posSpeeds = @json($posSpeeds);
                if (!this.formData.pos_speed_id.length) {
                    return '';
                }
                return this.formData.pos_speed_id
                    .map(id => {
                        const matched = posSpeeds.find(item => item.id == id);
                        return matched ? matched.split_pos : null;
                    })
                    .filter(Boolean)
                    .join(', ');
            },
            get selectedStatus() {
                const status = Object.values(
                    @json(config('dummy.fttx_status')));
                if (!this.formData.fttx_status.length) {
                    return '';
                }
                return this.formData.fttx_status
                    .map(id => {
                        const matched = status.find(item => item.key == id);
                        return matched ? matched.text : null;
                    })
                    .filter(Boolean)
                    .join(', ');
            },
            toggleSelectAllPosSpeed(event) {
                if (event.target.checked) {
                    this.formData.pos_speed_id = this.posSpeed.map(item => item.id);
                } else {
                    this.formData.pos_speed_id = [];
                }
            },
            toggleSelectAllStatus(event) {
                if (event.target.checked) {
                    this.formData.fttx_status = this.statusOptions.map(item => item.key);
                } else {
                    this.formData.fttx_status = [];
                }
            },
            reportDetailDialog(data) {
                let dataInfo = data;
                dataInfo.fttx_status = this.formData.fttx_status;
                dataInfo.from_date = this.formData.from_date;
                dataInfo.to_date = this.formData.to_date;
                reportDetailDialog({
                    active: true,
                    data: dataInfo,
                    dmcBtn: true,
                    title: "Create",
                    config: {
                        width: "97%",
                    }
                });
            },
        }));
    </script>
@stop
