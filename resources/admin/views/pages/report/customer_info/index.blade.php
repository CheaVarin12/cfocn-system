@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="xCustomerReport">
        <div class="header">
            @include('admin::shared.header', ['header_name' => 'Report Customer Info'])
            <div class="header-tab">
                <div class="header-tab-wrapper">
                    <div class="menu-row">
                        <div class="menu-item {!! Request::is('admin/report/customer-info/list') ? 'active' : '' !!}">Data</div>
                    </div>
                </div>
                <div class="header-action-button">
                    <form class="filter" action="{!! url()->current() !!}" method="GET">
                        <div class="form-row">
                            <input type="text" name="search" placeholder="@lang('adminGlobal.filter.search')"
                                value="{!! request('search') !!}">
                            <i data-feather="filter"></i>
                        </div>
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
                        {{-- check search ro export --}}
                        {{-- <input type="hidden" name="check" x-model="check"> --}}
                        <button mat-flat-button type="submit" class="btn-create bg-success minWithAuto"
                            @click=" check = 'search'">
                            <i data-feather="search"></i>
                        </button>
                        @can('report-customer-view')
                            <button type="button" class="btn-create bg-success" @click="excel()">
                                <i data-feather="arrow-down-circle"></i>
                                <span>Excel</span>
                            </button>
                        @endcan
                    </form>
                    <button s-click-link="{!! url()->current() !!}">
                        <i data-feather="refresh-ccw"></i>
                        <span>Reload</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body footerTableScroll">
            @include('admin::pages.report.customer_info.table')
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
        Alpine.data('xCustomerReport', () => ({
            search: '',
            excel: '',
            action: '',
            check: '',
            checkData: true,
            typeSearch: 'date',
            checkProject: '',
            submitLoading: false,
            formData: {
                search_project: @json(request('search_project')),
                search: @json(request('search')),
                from_date: @json(request('from_date')),
                to_date: @json(request('to_date')),
                check: @json(request('check')),
            },
            connectonServer: {
                status: false,
                message: null
            },
            init() {},
            checkProjectdata() {
                if (this.checkProject == '') {
                    this.checkData = true;
                } else {
                    this.checkData = false;
                }
            },
            async fetchData(url, callback, body = null) {
                await fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        },
                        body: body,
                    })
                    .then(response => response.json())
                    .then(response => {
                        callback(response);
                    })
                    .catch((e) => {})
                    .finally(async (res) => {});
            },
            async excel() {
                let url = '{!! url()->full() !!}';
                let data = @json($data);
                this.submitLoading = true;
                setTimeout(async () => {
                    await this.RunExcelJSExport(data);
                    this.submitLoading = false;
                }, 500);
            },
            async RunExcelJSExport(data) {
                let workbook = new ExcelJS.Workbook();
                const defaultColumn = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                let lastColumn = defaultColumn[defaultColumn.length - 1];

                //fontName
                const Khmer_OS_Siemreap = "Khmer OS Siemreap";
                const Times_New_Roman = "Times New Roman";
                const Khmer_OS = "Khmer OS";
                const Calibri = "Calibri";
                const Khmer_OS_Battambang = "Khmer OS Battambang";

                const style_font = {
                    name: "Khmer OS System",
                    size: 12,
                    color: {
                        argb: "000000"
                    },
                    bold: false
                };

                const style_font_header = {
                    name: "Khmer OS Muol Light",
                    family: 4,
                    size: 18,
                    color: {
                        argb: "538DD5"
                    },
                };
                const style_border = {
                    top: {
                        style: "thin"
                    },
                    left: {
                        style: "thin"
                    },
                    bottom: {
                        style: "thin"
                    },
                    right: {
                        style: "thin"
                    },
                };
                const align_center = {
                    vertical: "middle",
                    horizontal: "center",
                    wrapText: true,
                };
                const align_left = {
                    vertical: "middle",
                    horizontal: "left",
                    wrapText: true,
                };
                const align_right = {
                    vertical: "middle",
                    horizontal: "right",
                    wrapText: true,
                };
                const wrapText = {
                    wrapText: true
                };
                const colorRed = {
                    color: {
                        argb: "FF0000"
                    }
                };

                // Create workbook and worksheet
                const worksheet = workbook.addWorksheet("Customer Infomation");

                // Add Row Title and formatting
                const titleTopRow = worksheet.addRow(["របាយការណ៍ការអតិថិជន"]);
                titleTopRow.font = style_font_header;
                titleTopRow.alignment = align_center;
                worksheet.mergeCells("A1:" + lastColumn + 1);

                // worksheet.addRow([]);
                // Add Header Row
                const header = [
                    "Register Date",
                    "ID",
                    "Name",
                    "Phone Number",
                    "VAT",
                    "Email",
                    "Status",
                    "Address"
                ];

                const headerRow = worksheet.addRow(header);

                // Cell Style : Fill and Border
                headerRow.eachCell((cell, number) => {
                    cell.fill = {
                        type: "pattern",
                        pattern: "solid",
                        fgColor: {
                            argb: "d4d4d4E5"
                        },
                    };
                    cell.font = {
                        name: "Khmer Moul",
                        size: 12
                    };

                    cell.border = style_border;
                    cell.alignment = align_center;
                });
                var i = 3;
                data.forEach((item, index) => {
                    worksheet.addRow([
                        this.dateFormatEn(item?.register_date, 'YYYY-MM-DD'),
                        item?.customer_code,
                        item?.name_en + '\r\n' + item?.name_kh,
                        item?.phone,
                        item?.vat_tin,
                        item?.email,
                        (item?.status == 1 ? 'Active' : 'Deactive'),
                        item?.address_en + '\r\n' + item?.address_kh,
                    ]);
                    //setStyle
                    defaultColumn.forEach((column) => {
                        worksheet.getCell(column + i).font = style_font;
                        worksheet.getCell(column + i).alignment = align_center;
                        worksheet.getCell(column + i).border = style_border;
                        if (column == "C" || column == "F" || column == "H") {
                            worksheet.getCell(column + i)
                                .alignment = align_left;
                        }
                    });
                    //endSteStyle
                    i++;
                });
                //setWidthHeight
                worksheet.getRow(1).height = 50;
                worksheet.getRow(2).height = 22;
                defaultColumn.forEach((column, colIndex = 1) => {
                    let colI = colIndex + 1;
                    worksheet.getColumn(colI).width = 25;
                    if (colI == 1) {
                        worksheet.getColumn(colI).width = 18;
                    }
                    if (colI == 3) {
                        worksheet.getColumn(colI).width = 50;
                    }
                    if (colI == 6) {
                        worksheet.getColumn(colI).width = 40;
                    }
                    if (colI == 8) {
                        worksheet.getColumn(colI).width = 70;
                    }
                });
                //endSetWithHeight
                // Generate Excel File with given name
                const titleExportName = "customer_info_" + this.dateFormatEn(moment(),
                    'DD_MM_YYYY_H:mm:ss');
                workbook.xlsx.writeBuffer().then(function(data) {
                    const blob = new Blob([data], {
                        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    });
                    saveAs(blob, titleExportName);
                });
            },
            dateFormatEn(date, type) {
                return date ? moment(date).format(type) : "";
            },
        }));
    </script>
@stop
