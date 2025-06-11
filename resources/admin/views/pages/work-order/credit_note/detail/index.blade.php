<template x-data="{}" x-if="$store.invoiceDetail.active">
    <div class="dialog" x-data="xInvoiceDetail" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.invoiceDetail?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <div class="h3HeaderLeft">
                        <h3 x-text="$store.invoiceDetail?.options?.title"></h3>
                        <template x-if="$store.invoiceDetail?.options?.dmcBtn">
                            <div class="imgIcon" x-show="connectonServer.message">
                                <div class="icon" :class="connectonServer.status ? 'con' : 'lose'">
                                    <div class="round"></div>
                                    <label x-text="connectonServer.status ? 'connection':'lose connection'"></label>
                                </div>
                            </div>
                        </template>
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
                            <div class="conditionComponent">
                            @include('admin::pages.work-order.credit_note.detail.table')
                        </div>
                        <template x-if="!$store.invoiceDetail?.options?.dmcBtn">
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
                        </template>
                        <template x-if="$store.invoiceDetail?.options?.dmcBtn">
                            <div class="form-footer">
                                <button type="button" class="primary" @click="dmcSubmit()">
                                    <i class='bx bx-send bx-tada-hover'></i>
                                    <span>Submit</span>
                                </button>
                                <button type="button" class="close" @click="dialogClose()">
                                    <i class='bx bx-x bx-tada-hover'></i>
                                    <span>Close</span>
                                </button>
                            </div>
                        </template>
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
        btnDMC: false,
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
        data: {
            purchase_type: false
        },
        connectonServer: {
            status: false,
            message: null
        },
        refreshLoading: false,
        bankAccount:null,
        async init() {
            this.data = {
                purchase_type: false
            };
            this.submitLoading = true;
            this.refreshLoading = true;
            let dataStore = this.$store.invoiceDetail.options.data;
            let purchase = dataStore?.purchase;
            let btn_type = this.$store.invoiceDetail?.options?.dmcBtn;
            let url = `/admin/work-order/credit-note/view-detail/${dataStore.id}`;
            setTimeout(async () => {
                try {
                    await this.fetchData(url, (res) => {
                        this.data = res.data;
                        this.bankAccount = res.bankAccount;
                        this.data.dataCustomer = this.data?.data_customer ? this.jsonPase(this.data.data_customer) : null;
                        let conn_status = res?.server_connection_status ?? null;
                        this.connectonServer.status = (conn_status == 'login_success') ? true : false;
                        this.connectonServer.message = conn_status;
                        this.submitLoading = false;
                        this.refreshLoading = false;
                    });
                } catch (e) {
                    this.submitLoading = false;
                    this.refreshLoading = false;
                };
            }, 500);
        },
        jsonPase(data) {
            return JSON.parse(data);
        },
        async excel() {
            this.submitLoading = true;
            setTimeout(async () => {
                await this.RunExcelJSExport(this.data);
                this.submitLoading = false;
            }, 500);
        },
        async RunExcelJSExport(data) {
            let workbook = new ExcelJS.Workbook();
            const defaultColumn = ['B', 'C', 'D', 'E', 'F', 'G', 'H'];

            // Create workbook and worksheet
            const worksheet = workbook.addWorksheet("Invoice");
            // worksheet.getColumn('A').hidden = true;

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

            let columnHeaderTableMarge = ['B', 'D', 'E', 'H'];
            let columnSingnature = [
                ['B', 'C'],
                ['D', 'E'],
                ['F', 'H']
            ];

            let dataColumn = defaultColumn;

            //endConditionExportExcelLayout
            let lastColumn = dataColumn[dataColumn.length - 1];
            columnHeaderTableMarge[columnHeaderTableMarge.length - 1] = lastColumn;
            columnSingnature[columnSingnature.length - 1][1] = lastColumn;

            //dataHeader
            const dataHeaderNumberRow = this.excelHeader(worksheet, data, align_center, lastColumn,
                style_font,
                Khmer_OS_Siemreap, Times_New_Roman, Khmer_OS, Calibri, Khmer_OS_Battambang);
            //endDataHeader


            //dataHeaderTable
            const dataHeaderTableNumberRow = this.excelHeaderTable(worksheet, data, dataHeaderNumberRow,
                style_font, align_left, style_border, Calibri, Khmer_OS, columnHeaderTableMarge);
            //endDataHeaderTable

            //table
            let i = 0;
  
            i = this.excelTable(worksheet, data, Khmer_OS, style_border,
                align_center, align_left, dataHeaderNumberRow, dataHeaderTableNumberRow,
                dataColumn, Khmer_OS_Battambang, style_font);

            //endTable

            //footerTable
            const dataTableFooterLeftNumberRow = this.excelFooterTable(worksheet, data, dataColumn,
                Khmer_OS_Siemreap, style_font, align_left, style_border, align_right, align_center,
                Calibri, i, columnHeaderTableMarge);

            //endFooterTable

            //footerUnderTable
            const dataUnderTableFooterNumberRow = this.excelUnderFooterTable(worksheet, i,
                dataTableFooterLeftNumberRow, dataColumn, style_font, Calibri,
                align_left);
            //endFooterUnderTable

            //Singnature
            const singnatureNumberRow = this.excelSingnature(worksheet, i, dataTableFooterLeftNumberRow,
                dataUnderTableFooterNumberRow,
                dataColumn, style_font, Khmer_OS_Battambang, align_center, Calibri, columnSingnature
            );
            //endSignature

            // Generate Excel File with given name
            const titleExportName = "invoice_" + this.dateFormatEn(moment(), 'DD_MM_YYYY_H:mm:ss');
            workbook.xlsx.writeBuffer().then(function(data) {
                const blob = new Blob([data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                saveAs(blob, titleExportName);
            });

        },
        excelProjectName(worksheet, data, dataHeaderNumberRow, dataHeaderTableNumberRow, dataColumn,
            align_center, style_border) {
            let i = (dataHeaderNumberRow + dataHeaderTableNumberRow) + 2
            worksheet.addRow([
                "",
                "",
                "",
                data?.order?.project?.name,
                "",
                "",
                "",
                ""
            ]);
            //setStyle
            dataColumn.forEach((column) => {
                worksheet.getCell(column + i).alignment = align_center;
                worksheet.getCell(column + i).border = style_border;

            });
            //endSteStyle
            return 1;
        },

        //Table
        excelTable(worksheet, data, Khmer_OS, style_border, align_center, align_left,
            dataHeaderNumberRow,
            dataHeaderTableNumberRow,
            dataColumn, Khmer_OS_Battambang, style_font) {
            // Add Header Row
            const header = [
                "",
                "ល.រ" + '\r\n' + "Nº",
                "ប្រភេទ" + '\r\n' + "Item",
                "បរិយាយមុខទំនិញ" + '\r\n' + "Description",
                "បរិមាណ" + '\r\n' + "Quantity",
                "ឯកតា" + '\r\n' + "UOM",
                "ថ្លៃឯកតា" + '\r\n' + "Unit price",
                "ថ្លៃទំនិញ" + '\r\n' + "Amount",
            ];

            const headerRow = worksheet.addRow(header);

            // Cell Style : Fill and Border
            headerRow.eachCell((cell, number) => {
                cell.fill = {
                    type: "pattern",
                    pattern: "solid",
                    fgColor: {
                        argb: "C4D69B"
                    },
                    stroke: {
                        color: '88AAFF',
                        opacity: 1,
                        weight: 2
                    }
                };
                cell.font = {
                    name: Khmer_OS,
                    size: 8,
                    bold: true
                };

                cell.border = style_border;
                cell.alignment = align_center;
            });
            let numberProject = this.excelProjectName(worksheet, data, dataHeaderNumberRow,
                dataHeaderTableNumberRow, dataColumn, align_center, style_border);
            let i = (dataHeaderNumberRow + dataHeaderTableNumberRow) + 2 + numberProject;

            data.credit_note_details.forEach((item, index) => {
                worksheet.addRow([
                    "",
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
                    if (column == "D") {
                        worksheet.getCell(column + i).alignment = align_left;
                    }
                });
                //endSteStyle
                i++;
            });

            //setWidthHeight
            dataColumn.forEach((column, colIndex = 1) => {
                let colI = colIndex + 2;
                worksheet.getColumn(colI).width = 13;
                if (colI == 2 || colI == 6) {
                    worksheet.getColumn(colI).width = 7;
                }
                if (colI == 3) {
                    worksheet.getColumn(colI).width = 20;
                }
                if (colI == 4) {
                    worksheet.getColumn(colI).width = 30;
                }
                if (colI == 7 || colI == 8) {
                    this.excelFormatDollar(worksheet.getColumn(colI));
                }
            });
            //endSetWithHeight
            return i;
        },

        excelHeader(worksheet, data, align_center, lastColumn, style_font, Khmer_OS_Siemreap,
            Times_New_Roman,
            Khmer_OS,
            Calibri, Khmer_OS_Battambang) {
            //dataHeader
            let dataHeader = [
                "(ខេមបូឌា) ហ្វីប៊ើរអុបទិច ខមញូនីខេសិន ណេតវើក",
                "(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK Co., Ltd.",
                `លេខអត្តសញ្ញាណកម្ម អតប (VATIN) : ${data?.order?.project?.vat_tin}`,
                "អាសយដ្ឋាន៖ ផ្ទះលេខ ១៦៨ ផ្លូវលេខ ១៩៤៦ ភូមិទំនប់ សង្កាត់ ភ្នំពេញថ្មី ខណ្ឌ សែនសុខ រាជធានីភ្នំពេញ",
                "Address :No.168, St.1946, Phum Tumnub, Sangkat Phnom Penh Thmei, Khan Sen Sok, Phnom Penh , Cambodia",
                "ទូរស័ព្ទលេខ (+៨៥៥) ០២៣ ៨៨៨ ០២២/​ ០៨៦​ ៨២២ ១៧៣",
                "HP: (+855)023 888 022/ 086 822 173 Fax: +855-23 886 600",
                "វិក្កយបត្រអាករ",
                "TAX INVOICE"
            ];

            // Add Row Title and formatting
            dataHeader.forEach((item, index) => {
                const i = index + 1;
                const getCell = worksheet.getCell(`B${i}`);
                getCell.value = item;
                worksheet.mergeCells(`B${i}:${lastColumn+i}`);

                //style
                getCell.alignment = align_center;
                if (i == 1) {
                    worksheet.getRow(i).height = 40;
                }
                getCell.font = {
                    ...this.excelHeaderSetStyle(i, style_font, Khmer_OS_Siemreap,
                        Times_New_Roman, Khmer_OS, Calibri, Khmer_OS_Battambang)
                };

            });
            //endDataHeader
            return dataHeader.length;
        },
        excelHeaderSetStyle(i, style_font, Khmer_OS_Siemreap, Times_New_Roman, Khmer_OS, Calibri,
            Khmer_OS_Battambang) {
            if (i == 1) {
                style_font.name = Khmer_OS_Siemreap;
                style_font.size = 13;
                style_font.bold = true;
            } else if (i == 2) {
                style_font.name = Times_New_Roman;
                style_font.size = 10;
                style_font.bold = true;
            } else if (i == 3 || i == 4 || i == 6) {
                style_font.name = Khmer_OS;
                style_font.size = 8;
                style_font.bold = false;
            } else if (i == 5 || i == 7) {
                style_font.name = Calibri;
                style_font.size = 7;
                style_font.bold = false;
            } else if (i == 8) {
                style_font.name = Khmer_OS_Battambang;
                style_font.size = 10;
                style_font.bold = true;
            } else if (i == 9) {
                style_font.name = Times_New_Roman;
                style_font.size = 8;
                style_font.bold = true;
            } else {
                style_font.bold = false;
            }
            return style_font;
        },
        excelHeaderTable(worksheet, data, dataHeaderNumberRow, style_font, align_left, style_border,
            Calibri, Khmer_OS, columnHeaderTableMarge) {
            let dataHeaderTable = [
                [
                    `ឈ្មោះក្រុមហ៊ុន : ${data?.dataCustomer ? data.dataCustomer.name_kh : data?.customer?.name_kh}`,
                    `លេខរៀងវិក្កយបត្រ/​ Invoice Nº​ : ${data?.credit_note_number}`
                ],
                [
                    `Company name : ${data?.dataCustomer ? data.dataCustomer.name_en : data?.customer?.name_en}`,
                    `កាលបរិច្ឆេទ/ Date : ${this.dateFormat(data?.issue_date)}`
                ],
                [
                    `អាស័យដ្ឋាន : ${data?.dataCustomer ? data.dataCustomer.address_kh : data?.customer?.address_kh}`,
                    `រយៈកាលបរិច្ឆេទ/ Invoice Period : ${this.dateFormat(data?.period_start)} - ${this.dateFormat(data?.period_end)}`
                ],
                [
                    `Address : ${data?.dataCustomer ? data.dataCustomer.address_en : data?.customer?.address_en}`,
                    `លេខកិច្ចសន្យា/ Contract No. :  ${data?.order?.contract_number}  `
                ],
                [
                    `ទូរស័ព្ទលេខ/ Telephone Nº : ${data?.dataCustomer ? data.dataCustomer.phone : data?.customer?.phone}`,
                    `Ref : ${data?.invoice_number}`
                ],
                [
                    "អ្នកទទួល/ Attention : ",
                    ""
                ],
                [
                    `លេខអត្តសញ្ញាណកម្ម អតប(VATTIN) : ${data?.dataCustomer ? data.dataCustomer.vat_tin : data?.customer?.vat_tin}`,
                    ""
                ],
            ];

            dataHeaderTable.forEach((item, index) => {
                const i = dataHeaderNumberRow + (index + 1);
                let columnLeft = columnHeaderTableMarge[0] + i + ':' + columnHeaderTableMarge[1] +
                    i;
                let columnRight = columnHeaderTableMarge[2] + i + ':' + columnHeaderTableMarge[3] +
                    i;
                worksheet.mergeCells(columnLeft);
                worksheet.mergeCells(columnRight);
                const left = worksheet.getCell(`B${i}`);
                const right = worksheet.getCell(`${columnHeaderTableMarge[2]}${i}`);

                left.value = item[0];
                right.value = item[1];

                //setStyle
                style_font.name = Khmer_OS;
                style_font.size = 7;
                style_font.bold = false;

                left.font = {
                    ...style_font
                };
                left.alignment = align_left;
                left.border = style_border;

                right.font = {
                    ...style_font
                };
                right.alignment = align_left;
                right.border = style_border;
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
            return dataHeaderTable.length;
        },
        excelFooterTable(worksheet, data, dataColumn, Khmer_OS_Siemreap, style_font, align_left,
            style_border,
            align_right, align_center, Calibri, i, columnHeaderTableMarge) {
            let dataTableFooterLeft = [
                "Remark : " + (data?.remark ?? ''),
                `Note: Ref.NBC Exchange Rate On ${this.dateFormat(data?.issue_date)} 1 USD = ${data?.exchange_rate} Riel`,
                `${data?.note ?? ''}`
            ];

            let totalPrice = data?.total_price;
            let totalPriceKh = data?.total_price_kh;

            let vat = data?.vat;
            let vatKh = data?.vat_kh;

            let grandTotal = data?.total_grand;
            let grandTotalKh = data?.total_grand_kh;

            let dataTableFooterRight = [{
                    name: ["សរុប", "Sub Total"],
                    total: [Number(totalPrice), Number(totalPriceKh)]
                },
                {
                    name: ["អាករលើតម្លៃបន្ថែម ១០%", "VAT 10%"],
                    total: [Number(vat), Number(vatKh)]
                },
                {
                    name: ["សរុបរួម", "Grand Total"],
                    total: [Number(grandTotal), Number(grandTotalKh)]
                }
            ];

            //footerTable
            this.footerTable(worksheet, dataTableFooterLeft, dataTableFooterRight, i,
                style_font,
                Khmer_OS_Siemreap,
                align_left, dataColumn,
                style_border, align_right, align_center, Calibri, columnHeaderTableMarge);
            //endFooterTable
            return dataTableFooterLeft.length;
        },
        //ZeroWithSale
        footerTable(worksheet, dataTableFooterLeft, dataTableFooterRight, i, style_font,
            Khmer_OS_Siemreap, align_left, dataColumn,
            style_border, align_right, align_center, Calibri, columnHeaderTableMarge) {
            dataTableFooterLeft.forEach((item, index) => {
                const indexData = i + index;
                worksheet.addRow([
                    "",
                    item,
                    "",
                    "",
                    dataTableFooterRight[index].name[0] + '\r\n' +
                    dataTableFooterRight[index].name[1],
                    "",
                    dataTableFooterRight[index].total[0],
                    dataTableFooterRight[index].total[1],
                ]);
                worksheet.mergeCells(`B${indexData}:D${indexData}`);
                worksheet.mergeCells(`E${indexData}:F${indexData}`);
                worksheet.getRow(indexData).height = 28;

                //setStyle
                dataColumn.forEach((column) => {
                    style_font.name = Khmer_OS_Siemreap;
                    if ((index == 0 || index == 1) && (column == "B" || column == "G" || column == "H")) {
                        worksheet.getCell(column + indexData).font = {
                            ...style_font,
                            name: Calibri,
                            bold: true
                        };
                    } else if (index == 2 || column == "E" || column == "F") {
                        worksheet.getCell(column + indexData).font = {
                            ...style_font,
                            bold: true
                        };
                    }
                    worksheet.getCell(column + indexData).alignment = align_left;
                    worksheet.getCell(column + indexData).border = style_border;
                    if (column == "F") {
                        worksheet.getCell(column + indexData).alignment = align_right;
                    }
                    if (column == "G" || column == "H") {
                        worksheet.getCell(column + indexData).alignment = align_center;
                        worksheet.getCell(column + indexData).font = {
                            ...style_font,
                            name: Calibri,
                            bold: true
                        };
                    }
                    if (column == "G") {
                        this.excelFormatDollar(worksheet.getCell(column + indexData));
                    }
                    if (column == "H") {
                        this.excelFormatKh(worksheet.getCell(column + indexData));
                    }
                });
                //endSteStyle
            });
        },

        excelUnderFooterTable(worksheet, i, dataTableFooterLeftNumberRow, dataColumn, style_font, Calibri,
            align_left) {
            let dataFooter = [
                "Payment Instruction",
                "Please kindly remit payment to:",
                "(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK CO., LTD.",
            ];
            let bankDetails = this.bankAccount.map(item => item.bank_name + ' No. ' + item.account_number);
            dataFooter = dataFooter.concat(bankDetails);
            dataFooter.forEach((item, index) => {
                const indexData = (i + dataTableFooterLeftNumberRow) + index;
                worksheet.addRow(["", item]);
                worksheet.mergeCells(`B${indexData}:D${indexData}`);
                //setStyle
                dataColumn.forEach((column) => {
                    style_font.name = Calibri;
                    worksheet.getCell(column + indexData).font = {
                        ...style_font,
                        size: 7
                    };
                    worksheet.getCell(column + indexData).alignment = align_left;
                });
                //endSteStyle
            });
            return dataFooter.length;
        },
        excelSingnature(worksheet, i, dataTableFooterLeftNumberRow, dataUnderTableFooterNumberRow,
            dataColumn, style_font, Khmer_OS_Battambang, align_center, Calibri, columnSingnature) {
            //emptySignature
            let indexData = i + dataTableFooterLeftNumberRow + dataUnderTableFooterNumberRow;
            worksheet.addRow([]);
            worksheet.mergeCells(
                `B${indexData}:${columnSingnature[columnSingnature.length-1][1]}${indexData}`);
            worksheet.getRow(indexData).height = 40;
            //endEmptySignature

            //footerSignature
            let dataFooterSingnature = [
                ["ហត្ថលេខា និង ឈ្មោះអ្នកទិញ", "Customer's Signature & Name"],
                ["ត្រួតពិនិត្យដោយ", "Approved by"],
                ["ហត្ថលេខា​ និងឈ្មោះអ្នកលក់", "Seller's Signature & Name"]
            ];

            let empty = "-----------------------------------------------------------------";

            worksheet.addRow(["", empty, "", empty, "", empty, ""]);
            worksheet.getCell(`B${indexData}`).font = {
                underline: true,
                bold: true
            };
            worksheet.getCell(`D${indexData}`).font = {
                underline: true,
                bold: true
            };
            worksheet.getCell(`F${indexData}`).font = {
                underline: true,
                bold: true
            };

            worksheet.addRow([
                "",
                dataFooterSingnature[0][0],
                "",
                dataFooterSingnature[1][0],
                "",
                dataFooterSingnature[2][0],
                ""
            ]);
            worksheet.addRow([
                "",
                dataFooterSingnature[0][1],
                "",
                dataFooterSingnature[1][1],
                "",
                dataFooterSingnature[2][1],
                ""
            ]);
            //setStyle
            indexData += 1;


            dataFooterSingnature[0].forEach(val => {
                dataColumn.forEach((column) => {
                    style_font.name = Khmer_OS_Battambang;
                    worksheet.getCell(column + indexData).font = {
                        ...style_font
                    };
                    worksheet.getCell(column + indexData).alignment =
                        align_center;
                });
                const col1 =
                    `${columnSingnature[0][0]}${indexData}:${columnSingnature[0][1]}${indexData}`;
                const col2 =
                    `${columnSingnature[1][0]}${indexData}:${columnSingnature[1][1]}${indexData}`;
                const col3 =
                    `${columnSingnature[2][0]}${indexData}:${columnSingnature[2][1]}${indexData}`;
                worksheet.mergeCells(col1);
                worksheet.mergeCells(col2);
                worksheet.mergeCells(col3);

                indexData++;
            });
            dataColumn.forEach((column) => {
                style_font.name = Calibri;
                worksheet.getCell(column + indexData).font = {
                    ...style_font
                };
                worksheet.getCell(column + indexData).alignment = align_center;
            });
            const col1 = `${columnSingnature[0][0]}${indexData}:${columnSingnature[0][1]}${indexData}`;
            const col2 = `${columnSingnature[1][0]}${indexData}:${columnSingnature[1][1]}${indexData}`;
            const col3 = `${columnSingnature[2][0]}${indexData}:${columnSingnature[2][1]}${indexData}`;
            worksheet.mergeCells(col1);
            worksheet.mergeCells(col2);
            worksheet.mergeCells(col3);

            //endFooterSignature
            return indexData;
        },
        exportExcel() {
            window.location.href = `{!! url('/admin/invoice/export-invoice-excel') !!}/${this.data.id}`;
        },
        excelFormatDollar(worksheet) {
            return worksheet.numFmt = '$* #,##0.00;[Red]-#,##0.00';
        },
        excelFormatKh(worksheet) {
            return worksheet.numFmt = 'R* #,##0;[Red]-#,##0';
        },
        exportPdf() {
            let date = moment().format('MM_DD_YYYY_HH_mm_ss');
            let title = `invoice_number_${this.data.invoice_number}_on_date_${date}`;
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
                    dpi: 192,
                    scale: 2,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };
            this.submitLoading = true;
            setTimeout(() => {
                html2pdf().set(opt).from(element).then((res) => {}).save();
                this.submitLoading = false;
            }, 500);

        },
        disableLogo() {
            this.printLogo = !this.printLogo;
        },
        printPageArea(areaID) {
            let date = moment().format('MM_DD_YYYY_HH_mm_ss');
            const currTitle = document.title;
            document.title = `invoice_number_${this.data.invoice_number}_on_date_${date}`;
            printJS({
                printable: 'printableArea',
                type: 'html',
                css: [`{{ asset('admin-public/css/invoice/detail.css') }}`],
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
                    }
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
        numberFormatEn(num) {
            return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        numberFormatKh(num) {
            return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        dialogClose() {
            this.$store.invoiceDetail.active = false;
        },
        dateFormat(date = null) {
            return date ? moment(date).format('DD MMM YYYY') : "";
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
        
        dmcSubmit() {
            this.$store.confirmDialog.open({
                data: {
                    title: "Message",
                    message: "Are you sure want to submit DMC ?",
                    btnClose: "Close",
                    btnSave: "Yes",
                },
                afterClosed: (result) => {
                    if (result) {
                        this.submitLoading = true;
                        var formData = new FormData();
                        formData.append("invoice_id", this.data.id);
                        let dataTimeOut = null;
                        clearTimeout(dataTimeOut);
                        dataTimeOut = setTimeout(() => {
                            Axios.post(`{{ route('admin-work-order-credit-note-DMCSubmit') }}`,
                                formData, {
                                    headers: {
                                        'Content-Type': 'multipart/form-data',
                                    }
                                }).then(res => {
                                this.submitLoading = false;
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
                                } else if (res.data.message ==
                                    "success") {
                                    this.dialogClose();
                                    let currenturl = '{!! url()->full() !!}';
                                    reloadUrl(currenturl);
                                }
                            }).catch(error => {
                                this.submitLoading = false;
                            })
                        }, 500);
                    }
                }
            });
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
