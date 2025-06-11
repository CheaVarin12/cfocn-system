<template x-data="{}" x-if="$store.invoiceDetail.active">
    <div class="dialog" x-data="xInvoiceDetail" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.invoiceDetail?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <div class="h3HeaderLeft">
                        <h3 x-text="$store.invoiceDetail?.options?.title"></h3>
                    </div>
                    <div class="headerRight">
                        <div class="switchModeGroup">
                            <input type="checkbox" id="switch-mode" hidden>
                            <label for="switch-mode" class="switch-mode" @click="disableLogo()"></label>
                        </div>
                        <div class="refreshDev" @click="init()">
                            <i class='bx bx-refresh' :class="refreshLoading ? 'bx-spin' : ''"></i>
                        </div>
                        <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                    </div>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper" action="#" method="POST">
                        <div class="form-body formBodyInvoiceDetail">
                            @include('admin::pages.work-order.receipt.detail.table')
                        </div>
                        <div class="form-footer">
                            <button type="button" class="excel" color="primary" @click="excel()">
                                <i class='bx bxs-file-export bx-tada-hover'></i>
                                <span>Export excel</span>
                            </button>
                            <button type="button" class="pdf" color="primary" @click="exportPdf()">
                                <i class='bx bxs-file-pdf bx-tada-hover'></i>
                                <span>Export Pdf</span>
                            </button>
                            <button class="primary" type="button" @click="printPageArea('printableArea')">
                                <i class='bx bx-printer bx-tada-hover'></i>
                                <span>Print</span>
                            </button>
                            <button type="button" class="close" @click="dialogClose()">
                                <i class='bx bx-x bx-tada-hover'></i>
                                <span>Close</span>
                            </button>
                        </div>
                    </form>

                    <template x-if="submitLoading">
                        @include('admin::components.spinner')
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Alpine.data('xInvoiceDetail', () => ({
        submitLoading: false,
        refreshLoading: false,
        btnDMC: false,
        des_reason: null,
        printLogo: @json($logoControl->status) ?? true,
        propertyExcel: {
            font: {
                Khmer_OS_Siemreap: "Khmer OS Siemreap",
                Khmer_OS: "Khmer OS",
                Khmer_OS_Battambang: "Khmer OS Battambang",
                Times_New_Roman: "Times New Roman",
                Calibri: "Calibri"
            },
            alignment: {
                center: {
                    vertical: "middle",
                    horizontal: "center",
                    wrapText: true,
                },
                left: {
                    vertical: "middle",
                    horizontal: "left",
                    wrapText: true,
                },
                right: {
                    vertical: "middle",
                    horizontal: "right",
                    wrapText: true,
                }
            },
            border: {
                border: {
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
                }
            }
        },
        connectonServer: {
            status: false,
            message: null
        },
        timeCloseAuto: 0,
        data: null,
        async init() {
            this.submitLoading = true;
            this.refreshLoading = true;
            let dataStore = this.$store.invoiceDetail.options.data;

            let delayQuery = null;
            clearTimeout(delayQuery);
            delayQuery = setTimeout(async () => {
                this.data = dataStore;
                this.data.dataCustomer = dataStore?.data_customer ? this.jsonPase(dataStore?.data_customer) : this.data.customer;
                let detailInvoices = dataStore?.invoices?.invoice_detail ?? [];
                this.data.details = dataStore?.receipt_detail?.length > 0 ? dataStore?.receipt_detail : detailInvoices;
                this.submitLoading = false;
                this.refreshLoading = false;
            }, 500);
        },
        jsonPase(data) {
            return JSON.parse(data);
        },
        async excel() {
            this.submitLoading = true;
            setTimeout(async () => {
                try {
                    await this.RunExcelJSExport(this.data);
                    this.submitLoading = false;
                } catch ($e) {
                    this.submitLoading = false;
                }

            }, 500);
        },
        async RunExcelJSExport(data) {
            let workbook = new ExcelJS.Workbook();
            const defaultColumn = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

            // Create workbook and worksheet
            const worksheet = workbook.addWorksheet();

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

            let columnHeaderTableMarge = ['A', 'E', 'F', 'G'];
            let columnSingnature = [
                ['B', 'C'],
                ['D', 'E'],
                ['F', 'H']
            ];

            let dataColumn = defaultColumn;
            //conditionExportExcellayout
            if (!data.purchase_type) {
                if (data.check_rate_first > 0 && data.check_rate_seconde <= 0) {
                    dataColumn = [...dataColumn, "I"];
                } else if ((data.check_rate_first > 0 && data.check_rate_seconde > 0) || (data
                        .check_rate_first <= 0 && data.check_rate_seconde > 0)) {
                    dataColumn = [...dataColumn, "I", "J"];
                }
            } else {
                dataColumn = [...dataColumn, "I"];
            }
            //endConditionExportExcelLayout
            let lastColumn = dataColumn[dataColumn.length - 1];
            columnHeaderTableMarge[columnHeaderTableMarge.length - 1] = lastColumn;
            columnSingnature[columnSingnature.length - 1][1] = lastColumn;

            //dataHeader
            const dataHeaderNumberRow = this.excelHeader(worksheet, data, align_left, lastColumn,
                style_font,
                Khmer_OS_Siemreap, Times_New_Roman, Khmer_OS, Calibri, Khmer_OS_Battambang);
            //endDataHeader


            //dataHeaderTable
            const dataHeaderTableNumberRow = this.excelHeaderTable(worksheet, data, dataHeaderNumberRow,
                style_font, align_left, align_center, align_right, style_border, Calibri, Khmer_OS,
                columnHeaderTableMarge,
                lastColumn);
            //endDataHeaderTable

            //table
            let i = 0;

            i = this.excelTable(worksheet, data, Khmer_OS, Calibri, style_border,
                align_center, align_left, dataHeaderNumberRow, dataHeaderTableNumberRow,
                dataColumn, Khmer_OS_Battambang, style_font);

            //endTable

            //footerTable
            const dataTableFooterLeftNumberRow = this.excelFooterTable(worksheet, data, dataColumn,
                Khmer_OS_Siemreap, style_font, align_left, style_border, align_right, align_center,
                Calibri, i, columnHeaderTableMarge);
            //endFooterTable

            //Singnature
            const singnatureNumberRow = this.excelSingnature(worksheet, i, dataTableFooterLeftNumberRow,
                dataColumn, style_font, Khmer_OS_Battambang, align_center, align_left, Calibri,
                columnSingnature
            );
            //endSignature

            // Generate Excel File with given name
            const titleExportName = "receipt_" + this.dateFormatEn(moment(),
                'DD_MM_YYYY_H:mm:ss');
            workbook.xlsx.writeBuffer().then(function(data) {
                const blob = new Blob([data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                saveAs(blob, titleExportName);
            });

        },
        //table
        excelTable(worksheet, data, Khmer_OS, Calibri, style_border, align_center, align_left,
            dataHeaderNumberRow,
            dataHeaderTableNumberRow,
            dataColumn, Khmer_OS_Battambang, style_font) {
            // Add Header Row
            const header = [
                "Nº",
                "Item",
                "Description",
                "QTY",
                "UOM",
                "Unit price",
                "Amount",
            ];

            const headerRow = worksheet.addRow(header);

            // Cell Style : Fill and Border
            headerRow.eachCell((cell, number) => {
                cell.fill = {
                    type: "pattern",
                    pattern: "solid",
                    fgColor: {
                        argb: "bdbdbd"
                    },
                    stroke: {
                        color: '88AAFF',
                        opacity: 1,
                        weight: 2
                    }
                };
                cell.font = {
                    name: Calibri,
                    size: 13,
                    bold: true
                };

                cell.border = style_border;
                cell.alignment = align_center;
            });
            let i = (dataHeaderNumberRow + dataHeaderTableNumberRow) + 2;
            worksheet.getRow((dataHeaderNumberRow + dataHeaderTableNumberRow) + 1).height = 25;

            data.details.forEach((item, index) => {
                worksheet.addRow([
                    index + 1,
                    item?.service?.name,
                    item?.des,
                    item?.qty,
                    item?.uom,
                    item?.price,
                    item?.amount
                ]);

                //setStyle
                dataColumn.forEach((column) => {
                    style_font.name = Khmer_OS_Battambang;
                    worksheet.getCell(column + i).font = {
                        ...style_font
                    };
                    worksheet.getCell(column + i).alignment = align_center;
                    worksheet.getCell(column + i).border = style_border;
                    if (column == "C") {
                        align_left.vertical = "top";
                        align_left.wrapText = true;
                        worksheet.getCell(column + i).alignment = align_left;
                    }

                });
                //endSteStyle
                i++;
            });

            //setWidthHeight
            dataColumn.forEach((column, colIndex) => {
                let colI = colIndex + 1;
                worksheet.getColumn(colI).width = 13;
                if (colI == 2) {
                    worksheet.getColumn(colI).width = 20;
                }
                if (colI == 3) {
                    worksheet.getColumn(colI).width = 37;
                }
                if (colI == 6 || colI == 7) {
                    worksheet.getColumn(colI).width = 23;
                    this.excelFormatDollar(worksheet.getColumn(colI));
                }
            });
            //endSetWithHeight
            return i;
        },

        excelHeader(worksheet, data, align_left, lastColumn, style_font, Khmer_OS_Siemreap,
            Times_New_Roman,
            Khmer_OS,
            Calibri, Khmer_OS_Battambang) {
            let emptyRow = 4;
            for (let j = 0; j <= emptyRow; j++) {
                worksheet.addRow([]);
            }
            //dataHeader
            let dataHeader = [
                "(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK Co., Ltd.",
                `No.168, St.1946, Phum Tumnub, Sangkat Phnom Penh Thmei,Khan Sen Sok, Phnom Penh, Cambodia.`,
                `Tel: +855-71 3 472 472 / 23 888 022 Fax: +855-23 886 600`,
                `VAT TIN : ${data?.invoices?.order?.project?.vat_tin ?? ''}`
            ];

            // Add Row Title and formatting
            var i = 0;
            dataHeader.forEach((item, index) => {
                i = emptyRow + (index + 1);
                const getCell = worksheet.getCell(`A${i}`);
                getCell.value = item;
                worksheet.mergeCells(`A${i}:${lastColumn+i}`);

                //style
                getCell.alignment = {
                    ...align_left,
                    wrapText: false
                };
                getCell.font = {
                    ...this.excelHeaderSetStyle(i, style_font, Khmer_OS_Siemreap,
                        Times_New_Roman, Khmer_OS, Calibri, Khmer_OS_Battambang)
                };

            });
            //endDataHeader

            let jIndex = dataHeader.length + emptyRow + 1;
            worksheet.addRow([]);
            worksheet.mergeCells(`A${jIndex}:${lastColumn+jIndex}`);
            worksheet.getCell(`A${jIndex}`).border = {
                top: {
                    style: "thin"
                },
            };
            return jIndex;
        },
        excelHeaderSetStyle(i, style_font, Khmer_OS_Siemreap, Times_New_Roman, Khmer_OS, Calibri,
            Khmer_OS_Battambang) {
            if (i == 1) {
                style_font.name = Calibri;
                style_font.size = 13;
                style_font.bold = true;
            } else {
                style_font.name = Calibri;
                style_font.size = 10;
                style_font.bold = false;
            }
            return style_font;
        },
        excelHeaderTable(worksheet, data, dataHeaderNumberRow, style_font, align_left, align_center,
            align_right,
            style_border,
            Calibri, Khmer_OS, columnHeaderTableMarge, lastColumn) {
            
            let dataHeaderTable = [
                [
                    `TO : ${data?.dataCustomer?.name_en??''}`,
                    `Receipt No : ${data?.receipt_number??''}`
                ],
                [
                    `Address : ${data?.dataCustomer?.address_en??''}`,
                    `Receipt Date : ${this.dateFormatEn(data?.issue_date, 'YYYY-MM-DD')}`
                ],
                [
                    `Telephone Nº : ${data?.dataCustomer?.phone??''}`,
                    `Invoice Ref : ${data?.receipt_from == 'credit_note' ? data?.credit_note?.credit_note_number : data?.invoices?.invoice_number}`
                ]
            ];
            let indexText = dataHeaderNumberRow + 1;
            let getCellText = worksheet.getCell(`A${indexText}`);
            getCellText.value = "OFFICIAL RECEIPT";
            //setStyle
            getCellText.alignment = align_center;
            getCellText.font = {
                ...style_font,
                name: Calibri,
                size: 13
            };
            worksheet.mergeCells(`A${indexText}:${lastColumn+indexText}`);

            dataHeaderTable.forEach((item, index) => {
                const i = indexText + (index + 1);
                let columnLeft =
                    `${columnHeaderTableMarge[0]}${i}:${columnHeaderTableMarge[1]}${i}`;
                let columnRight =
                    `${columnHeaderTableMarge[2]}${i}:${columnHeaderTableMarge[3]}${i}`;
                worksheet.mergeCells(columnLeft);
                worksheet.mergeCells(columnRight);
                const left = worksheet.getCell(`A${i}`);
                const right = worksheet.getCell(`${columnHeaderTableMarge[2]}${i}`);

                left.value = item[0];
                right.value = item[1];

                //setStyle
                style_font.name = Calibri;
                style_font.size = 10;
                style_font.bold = false;

                left.font = {
                    ...style_font
                };
                left.alignment = align_left;
                // left.border = style_border;

                right.font = {
                    ...style_font
                };
                right.alignment = align_right;
                // right.border = style_border;
                if (index == 1 || index == 3) {
                    style_font.name = Calibri;
                    left.font = {
                        ...style_font
                    };

                } else if (index == 4) {
                    right.font = {
                        ...style_font
                    };
                }
                //endSteStyle
            });

            return dataHeaderTable.length + 1;
        },

        //FooterLayout
        excelFooterTable(worksheet, data, dataColumn, Khmer_OS_Siemreap, style_font, align_left,
            style_border,
            align_right, align_center, Calibri, i, columnHeaderTableMarge) {
            let dataTableFooterLeft = `Amount in Word:` + '\r' + `${data?.note??''}`;

            let totalPrice = data?.total_price;
            let totalPriceKh = data?.total_price_kh;

            let vat = data?.vat;
            let vatKh = data?.vat_kh;

            let grandTotal = data?.total_grand;
            let grandTotalKh = data?.total_grand_kh;

            let partialPayment = data?.partial_payment;

            let dataTableFooterRight = [{
                    name: ["Sub Total"],
                    total: [Number(totalPrice)]
                },
                {
                    name: ["VAT 10%"],
                    total: [Number(vat)]
                },
                {
                    name: ["Grand Total"],
                    total: [Number(grandTotal)]
                },
                {
                    name: ["Partial Payment"],
                    total: [Number(partialPayment)]
                }
            ];

            //footerTable
            this.footer(worksheet, dataTableFooterLeft, dataTableFooterRight, i,
                style_font,
                Khmer_OS_Siemreap,
                align_left, dataColumn,
                style_border, align_right, align_center, Calibri, columnHeaderTableMarge);
            //endFooterTable
            return dataTableFooterRight.length;
        },
        //footer
        footer(worksheet, dataTableFooterLeft, dataTableFooterRight, i, style_font,
            Khmer_OS_Siemreap, align_left, dataColumn,
            style_border, align_right, align_center, Calibri, columnHeaderTableMarge) {

            let indexData = dataTableFooterRight.length;
            const colLeft = ['A', 'B', 'C', 'D', 'E'];
            const colRight = ['F', 'G'];
            //left
            worksheet.mergeCells(`A${i}:E${(indexData + i)-1}`);
            let getCellText = worksheet.getCell(`A${i}`);
            getCellText.value = dataTableFooterLeft;
            getCellText.alignment = {
                ...align_left,
                vertical: "bottom"
            };
            getCellText.border = style_border;

            //right
            dataTableFooterRight.forEach((item, index) => {
                const indexDataRow = i + index;
                colRight.forEach(col => {
                    let getCellText = worksheet.getCell(`${col}${indexDataRow}`);
                    if (col == "F") {
                        getCellText.value = item.name[0];
                        getCellText.alignment = {
                            ...align_right
                        };
                        worksheet.getCell(`${col}${indexDataRow}`).fill = {
                            type: "pattern",
                            pattern: "solid",
                            fgColor: {
                                argb: "bdbdbd"
                            }
                        };
                    } else {
                        getCellText.value = item.total[0];
                        getCellText.alignment = {
                            ...align_center
                        };
                    }
                    worksheet.getRow(indexDataRow).height = 18;
                    worksheet.getCell(col + indexDataRow).border = style_border;

                });

            });
        },
        excelSingnature(worksheet, i, dataTableFooterLeftNumberRow,
            dataColumn, style_font, Khmer_OS_Battambang, align_center, align_left, Calibri, columnSingnature
        ) {
            //emptySignature
            let indexData = i + dataTableFooterLeftNumberRow;
            //endEmptySignature

            //footerSignature
            let dataFooterSingnature = [
                "Note: This receipt is made up in three copies.",
                "Original receipt is kept by customer.",
                "Two original copied receipts are kept by CFOCN."
            ];

            dataFooterSingnature.forEach(val => {
                worksheet.addRow([val]);
            });

            let dataFooterRightSingnature = [
                "Received by",
                "",
                "Date :"
            ];
            let indexVal = 0;
            dataFooterRightSingnature.forEach((val, index) => {
                indexVal = indexData + dataFooterSingnature.length + index;
                worksheet.addRow(["", "", "", "", "", val]);
                let columnMarge = `F${indexVal}:G${indexVal}`;
                worksheet.mergeCells(columnMarge);
                worksheet.getRow(indexVal).alignment = align_center;
            });
            worksheet.getRow(indexVal - 1).height = 40;
            worksheet.getCell(`F${indexVal-1}:G${indexVal-1}`).border = {
                bottom: {
                    style: "thin"
                }
            };
            worksheet.getRow(indexVal).alignment = align_left;

            //endFooterSignature
            return indexData;
        },
        excelFormatDollar(worksheet) {
            return worksheet.numFmt = '$* #,##0.00;[Red]-#,##0.00';
        },
        excelFormatKh(worksheet) {
            return worksheet.numFmt = 'R* #,##0;[Red]-#,##0';
        },
        exportPdf() {
            let date = moment().format('MM_DD_YYYY_HH_mm_ss');
            let title = `receipt_number_${this.data.receipt_number}_on_date_${date}`;
            var element = document.getElementById('printableArea');
            var opt1 = {
                margin: 0.3,
                filename: `${title}.pdf`,
                jsPDF: {
                    unit: 'in',
                    format: 'A4',
                    orientation: 'portrait'
                }
            };
            let nbPages = 3;
            var opt = {
                margin: 0.3,
                filename: `${title}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    dpi: 192,
                    letterRendering: true
                },
                jsPDF: {
                    unit: "in",
                    format: "a4",
                    orientation: "portrait",
                    putTotalPages: true,
                },
            };
            this.submitLoading = true;
            setTimeout(() => {
                html2pdf().set(opt).from(element).save();
                this.submitLoading = false;
            }, 500);

        },
        printCb(cb) {
            this.printLogo = false;
            cb(true);
        },
        disableLogo() {
            this.printLogo = !this.printLogo;
        },
        printPageArea(areaID) {
            let date = moment().format('MM_DD_YYYY_HH_mm_ss');
            const currTitle = document.title;
            document.title =
                `receipt_number_${this.data.receipt_number}_on_date_${date}`;
            printJS({
                printable: areaID,
                type: 'html',
                css: [`{{ asset('admin-public/css/invoice/receiptII.css') }}`],
                onPrintDialogClose: (res) => {
                    document.title = currTitle;
                },
                onIncompatibleBrowser: (res) => {},
                modalMessage: 'Retrieving Document...',
            });
        },
        async fetchData(url, callback) {
            await fetch(url, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                    },
                    body: null,
                })
                .then(response => response.json())
                .then(response => {
                    callback(response);
                })
                .catch((e) => {})
                .finally(async (res) => {});
        },
        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
        },
        numberFormat(num) {
            return new Intl.NumberFormat().format(num.toFixed(2));
        },
        dialogClose() {
            this.$store.invoiceDetail.active = false;
        },
        dateFormat(date) {
            return date ? moment(date).format('DD MMM YYYY') : ' ';
        },
        dateFormatEn(date, type) {
            return date ? moment(date).format(type) : "";
        },

        //excelProperty
        fontStyle() {
            return {
                name: "Khmer OS System",
                size: 12,
                color: {
                    argb: "000000"
                },
                bold: false
            };
        },
        fontStyleHeader() {
            return {
                name: "Khmer OS Muol Light",
                family: 4,
                size: 18,
                color: {
                    argb: "538DD5"
                },
            };
        },
        styleBorder() {
            return {
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
        },
        alignMent() {
            return {
                vertical: "middle",
                horizontal: "center",
                wrapText: true,
            };
        },
        dialogCloseWithTime() {
            let number = 100;
            let barWidth = 0;
            this.timeCloseAuto = 100;
            setTimeout(() => {
                let intervalID = setInterval(() => {
                    if (barWidth === number) {
                        clearInterval(intervalID);
                    } else {
                        this.timeCloseAuto--;
                        barWidth++;
                    }
                }, number);
                this.$store.DMCSubmitStatusDialog.open({
                    data: {
                        title: "Message",
                        message: "Are you sure want to submit DMC ?",
                        btnClose: "Close",
                        btnSave: "Yes",
                    },
                    afterClosed: (result) => {}
                });
            }, 100);
        },
    }));
</script>

{{-- store --}}
<script>
    Alpine.store('invoiceDetail', {
        active: false,
        dmcBtn: false,
        options: {
            data: null,
            selected: null,
            multiple: false,
            title: 'Choose an option',
            placeholder: 'Type to search...',
            allow_close: true,
            onReady: () => {},
            onSearch: () => {},
            beforeClose: () => {},
            afterClose: () => {}
        }
    });
    window.invoiceDetail = (options) => {
        Alpine.store('invoiceDetail', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('invoiceDetail').options,
                ...options
            }
        });
    };
</script>
