@extends('admin::shared.layout')
@section('layout')
    <style>
        .ui-datepicker-calendar {
            display: none;
        }

        .ui-datepicker .ui-datepicker-buttonpane button {
            background: #0099ff !important;
            margin-top: 2px;
            color: white !important;
        }

        #ui-datepicker-div {
            width: 300px !important;
            height: 113px !important;
        }
    </style>
    <div class="content-wrapper" x-data="xCustomerReport">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Invoice Detail'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row"></div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row w100">
                            <input type="text" name="year" id="year" value="{{ request('year') }}" placeholder="Select year" readonly>
                        </div>

                        <input type="hidden" name="check" x-model="check">
                        <button mat-flat-button type="submit" class="btn-create bg-primary minWithAuto"  @click=" check = ''">
                            <i data-feather="search"></i>
                        </button>
                        <button type="submit" class="btn-create bg-success" @click=" check = 'export'">
                            <i data-feather="arrow-down-circle"></i>
                            <span>Export Excel</span>
                        </button>
                    </form>
                    <button s-click-link="{!! url()->current() !!}" class="minWithAuto">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.report.summary-annual.invoice-detail.table')
            <div class="table-footer">
                @include('admin::components.pagination', ['paginate' => $data])
            </div>
        </div>
        
        <template x-if="submitLoading">
            @include('admin::components.spinner')
        </template>
    </div>
@stop
@section('script')
    <script>
      $(document).ready(function() {
    $("#year").datepicker({
        changeYear: true,
        showButtonPanel: true,
        dateFormat: "yy",
        yearRange: "-50:+0", 
        onClose: function() {
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, 1));
        },
        beforeShow: function(input, inst) {
            setTimeout(function() {
                $(".ui-datepicker-current").hide(); // Hide the "Today" button
            }, 1);
        },
        onChangeMonthYear: function(year, month, inst) {
            setTimeout(function() {
                $(".ui-datepicker-current").hide(); // Hide the "Today" button
            }, 1);
        }
    });
});

    </script>
    <script>
        Alpine.data('xCustomerReport', () => ({
            search: '',
            excel: '',
            check: '',
            typeSearch: 'date',
            checkProject: '',
            submitLoading: false,
            formData: {
                check: @json(request('check')),
                service_type: @json(request('service_type')),
            },
            init() {},

        }));
    </script>
@stop
