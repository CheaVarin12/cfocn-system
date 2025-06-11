<template x-data="{}" x-if="$store.reportDetailDialog.active">
    <div class="dialog" x-data="xReportDetail" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" :style="{ width: $store.reportDetailDialog.options.config.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <h3>Report Details ( <span x-text="dialogData?.name_en ?? dialogData?.name_kh"></span> )</h3>
                    <div style="display: flex;">
                        <button style="background-color: #00b74a;margin-right: 10px;" type="button"
                            class="btn-create mr-3" @click="exportExcel()">
                            <i style="font-size: 18px;" class="material-symbols-outlined">download</i>
                            <span style="color: white;font-size: 14px;">Export Excel</span>
                        </button>
                        <button style="background: rgba(255, 0, 0, 0.7607843137)" type="button" class="btn-create mr-3"
                            @click="dialogClose()">
                            <i style="font-size: 18px;" class="material-symbols-outlined">close</i>
                            <span style="color: white;font-size: 14px;">Close</span>
                        </button>
                    </div>
                </div>
                @include('admin::pages.fttx.expiration-report.view-details.table')
                <template x-if="submitLoading">
                    @include('admin::components.spinner')
                </template>
            </div>
        </div>
    </div>
</template>
<script>
    Alpine.data('xReportDetail', () => ({
        submitLoading: false,
        reportData: [],
        formSubmitData: {
            disable: false
        },

        async init() {
            this.submitLoading = true;
            let data = this.$store.reportDetailDialog.options.data;
            this.dialogData = data;
            await this.getDetailData();
        },
        async getDetailData() {
            const params = new URLSearchParams({
                customer_id: this.dialogData.id || '',
                pos_speed_id: this.dialogData.pos_speed_id || ''
            }).toString();

            await Axios.get(`/admin/fttx/expiration-report/get-detail?${params}`)
                .then(resp => {
                    this.reportData = resp.data.data;
                    setTimeout(() => {
                        this.submitLoading = false;
                    }, "500");
                });
        },

        numberRound(num, decimalPlaces = null) {
            if (!decimalPlaces) {
                return Math.round(num);
            }
            var p = Math.pow(10, decimalPlaces);
            return Math.round(num * p) / p;
        },

        dialogClose() {
            this.$store.reportDetailDialog.active = false;
        },

        exportExcel() {
            this.submitLoading = true;
            setTimeout(async () => {
                await this.RunExcelJSExport(this.reportData);
                this.submitLoading = false;
            }, 500);
        },
        async RunExcelJSExport(data) {
            let workbook = new ExcelJS.Workbook();
            const dataColumn = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
            // Create workbook and worksheet
            const worksheet = workbook.addWorksheet("Expiration Income Report");
            worksheet.views = [{
                state: 'normal',
                zoomScale: 110
            }];

            //set width 
            dataColumn.forEach((column, colIndex = 1) => {
                let colI = colIndex + 1;
                worksheet.getColumn(colI).width = 13;
                if (colI == 1) {
                    worksheet.getColumn(colI).width = 5;
                } else {
                    worksheet.getColumn(colI).width = 30;
                }
            });

            //fontName
            const Khmer_OS_Siemreap = "Khmer OS Siemreap";
            const Times_New_Roman = "Times New Roman";
            const Khmer_OS = "Khmer OS";
            const Calibri = "Calibri";
            const Khmer_OS_Battambang = "Battambang";

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

            // header

            const localizedString = "BEE UNION (CAMBODIA) TELECOM CO.,LTD";
            worksheet.addRow([localizedString]);

            const getCell = worksheet.getCell('A1:M1');
            worksheet.mergeCells('A1:M1');
            worksheet.getRow(1).height = 40;

            // add style
            getCell.font = {
                name: Khmer_OS_Battambang,
                size: 17,
                bold: true
            };
            getCell.alignment = align_left;

            // header table
            worksheet.addRow([
                "No",
                "Work order isp",
                "Work order cfocn",
                "Status",
                "Name",
                "Pos speed",
                "Deadline",
                "Rental Price",
                "PPCC",
                "Rental pole",
                "Total",
                "Expired month",
                "Grand total",
            ]);
            worksheet.getRow(2).height = 24;
            // Apply style to cells in row 2 (header row)
            dataColumn.forEach((col) => {
                const cell = worksheet.getCell(`${col}2`);
                cell.font = {
                    name: 'Battambang',
                    size: 11,
                };
                cell.alignment = align_center;
                cell.border = style_border;
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {
                        argb: 'D4D4E5'
                    },
                    stroke: {
                        color: {
                            argb: '88AAFF'
                        },
                        opacity: 1,
                        weight: 2
                    }
                }

            });


            // data table 

            let i = 3;
            data.forEach((item, index) => {
                worksheet.addRow([
                    index + 1,
                    item?.work_order_isp,
                    item?.work_order_cfocn,
                    item?.status_text,
                    item?.name,
                    item?.pos_speed.split_pos,
                    moment(item.deadline).format('DD-MMM-YY') ?? '-',
                    item?.rental_price,
                    item?.ppcc,
                    item?.rental_pole,
                    item?.total,
                    item?.expired_months,
                    item?.grand_total,
                ]);
                //setStyle
                dataColumn.forEach((column) => {
                    style_font.name = Khmer_OS_Battambang;
                    worksheet.getCell(column + i).font = {
                        name: 'Battambang',
                        size: 10,
                    };
                    worksheet.getCell(column + i).alignment = align_center;
                    worksheet.getCell(column + i).border = style_border;
                });
                //endSteStyle
                i++;
            });

            // Generate Excel File with given name
            const titleExportName = "Expiration Income Report" + this.dateFormatEn(moment(),
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
<script>
    Alpine.store('reportDetailDialog', {
        active: false,
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
    window.reportDetailDialog = (options) => {
        Alpine.store('reportDetailDialog', {
            active: true,
            options: {
                ...Alpine.store('reportDetailDialog').options,
                ...options
            }
        });
    };
</script>
