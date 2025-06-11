<template x-data="{}" x-if="$store.detail.active">
    <div class="dialog" x-data="xDetail" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container dialogContainerForm">
            <div class="diglogForm" x-bind:style="{ width: $store.detail?.config?.width }">
                <div class="dialog-form-header" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
                    <div class="h3HeaderLeft">
                        <h3 x-text="$store.detail?.options?.title"></h3>
                        <template x-if="$store.detail?.options?.dmcBtn">
                            <div class="imgIcon" x-show="connectonServer.message">
                                <div class="icon" :class="connectonServer.status ? 'con' : 'lose'">
                                    <div class="round"></div>
                                    <label x-text="connectonServer.status ? 'connection':'lose connection'"></label>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="headerRight">
                        <span class="material-symbols-outlined" @click="dialogClose()">close</span>
                    </div>
                </div>
                <div class="form-admin" id="my-section">
                    <form id="form" class="form-wrapper px-4" action="#" method="POST">
                        <div class="form-body formBodyInvoiceDetail">
                            <div id="printableArea">
                                <style>
                                    .table .table-header-custom .row {
                                        height: 60px !important;
                                    }
                                </style>
                                <div class="invoiceDetailLayout" id="printPage" style="color: black !important">
                                    {{-- header print invoice --}}
                                    <table>
                                        <thead>
                                            <tr>
                                                <td>
                                                    <div class="headerPrintInvoice">
                                                        <div style="text-align: right;">
                                                            <img src="{{ asset('images/logo/order_logo.jpg') }}"
                                                                alt="">
                                                        </div>
                                                        <div>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="all-content">
                                                        {{-- header --}}
                                                        <div class="headerInvoice">
                                                            <div class="txt bold" style="font-size:18pt;">
                                                                <div>Lease Order</div>
                                                            </div>
                                                            <div style="font-size:11pt;margin-top:1rem;">
                                                                <div>Phnom Penh, <span x-text="changFormatDate(data.issue_date)"></span>
                                                                </div>
                                                                <div>To: Cambodia Fiber Optic Communication Network Co., Ltd.(CFOCN)</div>
                                                                <div>No. 168, St. 1946, Phum Tumnub, Sangkat Phnom Penh Thmei, Khan Sen Sok,
                                                                    Phnom Penh, Cambodia</div>
                                                            </div>
                                                            <div style="font-size:12pt;margin-top:1rem;">
                                                                <div>Subject: <span x-text="data.subject"></span></div>
                                                            </div>
                    
                                                            <div style="margin:1rem 0;">
                                                                <div style="font-size:12pt;">Dear Sirs,</div>
                                                                <div style="font-size:11pt;" x-text="data.order_agreement"></div>
                                                            </div>
                    
                                                        </div>
                                                        <div id="form" class="form-wrapper">
                                                            <div class="form-body">
                                                                {{-- New --}}
                                                                <div class="row">
                                                                    <div class="table customTable">
                                                                        <div class="table-wrapper purchaseInvoice">
                                                                            <div class="table-header table-header-custom "
                                                                                style="height: auto; background-color:white;">
                                                                                <div class="row table-row-20 text-center">
                                                                                    <span style="font-size:10pt;">Service
                                                                                        Term</span>
                                                                                </div>
                                                                                <div class="row table-row-30 text-center">
                                                                                    <span style="font-size:10pt;"
                                                                                        class="text-center">Project
                                                                                        description</span>
                                                                                </div>
                                                                                <div class="row table-row-10 text-center">
                                                                                    <span style="font-size:10pt;">Qty of
                                                                                        Cores</span>
                                                                                </div>
                                                                                <div class="row table-row-10 text-center">
                                                                                    <span style="font-size:10pt;">Unit
                                                                                        Price (USD)</span>
                                                                                </div>
                                                                                <div class="row table-row-10 text-center">
                                                                                    <span style="font-size:10pt;">Unit (KM)</span>
                                                                                </div>
                                                                                <div class="row table-row-20 text-center">
                                                                                    <span style="font-size:10pt;">Price
                                                                                        (USD)</span>
                                                                                </div>
                                                                            </div>
                    
                                                                            <div class="table-body">
                                                                                {{-- body --}}
                                                                                <template x-for="(item,index) in purchaseOrderDetails">
                                                                                    <div class="column" id="columnIDInvoiceDetail">
                                                                                        <div class="row table-row-20">
                                                                                            <span style="font-size:9pt"
                                                                                                x-text="item.service?.name || ''"></span>
                                                                                        </div>
                                                                                        <div class="row table-row-30">
                                                                                            <span style="font-size:9pt"
                                                                                                x-text="item.description || ''"></span>
                                                                                        </div>
                                                                                        <div class="row table-row-10">
                                                                                            <span style="font-size:9pt"
                                                                                                x-text="item.qty_of_core || ''"></span>
                                                                                        </div>
                                                                                        <div class="row table-row-10">
                                                                                            <div class="text-spaceBetween">
                                                                                                <span style="font-size:9pt">$</span>
                                                                                                <span style="font-size:9pt"
                                                                                                x-text="item?.price ? numberFormat(item?.price):''"></span>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row table-row-10">
                                                                                            <span style="font-size:9pt"
                                                                                                x-text="item.unit || ''"></span>
                                                                                        </div>
                                                                                        <div class="row table-row-20">
                                                                                            <div class="text-spaceBetween">
                                                                                                <span style="font-size:9pt">$</span>
                                                                                                <span style="font-size:9pt"
                                                                                                x-text="item?.amount ? numberFormat(item?.amount):''"></span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </template>
                                                                                <div class="column" id="columnIDInvoiceDetail">
                                                                                    <div class="table-row-60"
                                                                                        style="border-right: 1px solid #a49999;text-align:right;">
                                                                                        <span
                                                                                            style="font-size:10pt; padding-right:10px;"><b>Monthly
                                                                                                Recurring Charge</b></span>
                                                                                    </div>
                    
                                                                                    <div class="row table-row-20">
                                                                                            <div class="text-spaceBetween">
                                                                                                <span style="font-size:9pt"
                                                                                                    x-text="data?.total_unit ? numberFormat(data.total_unit) : ''"></span>
                                                                                                    <span style="font-size:9pt">(KM)</span>
                                                                                                </div>
                                                                                    </div>
                                                                                    <div class="row table-row-20">
                                                                                        <div class="text-spaceBetween">
                                                                                            <span style="font-size:9pt">$</span>
                                                                                            <span style="font-size:9pt"
                                                                                                x-text="data?.total_price ? numberFormat(data?.total_price) : ''"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="column" id="columnIDInvoiceDetail">
                                                                                    <div class="pr-2 table-row-80"
                                                                                        style="border-right: 1px solid #a49999;padding-right:10px;text-align:right;">
                                                                                        <span style="font-size:9pt"><b>One Time
                                                                                                Charge</b></span>
                                                                                    </div>
                                                                                    <div class="row table-row-20">
                                                                                        <div class="text-spaceBetween">
                                                                                            <span style="font-size:9pt">$</span>
                                                                                            <span style="font-size:9pt"
                                                                                                x-text="data?.total_price ? numberFormat(data?.total_price) : ''"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- footer --}}
                                                        <div id="footerInvoiceDetail" style="margin-top:25px;">
                                                            <div style="font-size:11pt;" x-html="data.description">
                                                            </div>
                                                            <div class="footerSignature" style="margin-top: 50px">
                                                                <div class="column">
                                                                    <div style="font-size: 11pt;margin-bottom: 190px;">
                                                                        Yours faithfully,
                                                                    </div><br>
                                                                    <div>
                                                                        <div style="border-bottom: 1px solid;">
                                                                        </div>
                                                                        <div style="font-size: 11pt;">
                                                                            Stamp:
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="column" style="text-align: center">
                                                                </div>
                                                                <div class="column">
                                                                    <div style="font-size: 11pt;margin-bottom: 190px;">
                                                                        Acknowledge</div><br>
                                                                    <span>
                                                                        <div style="border-bottom: 1px solid;">
                                                                        </div>
                                                                        <div style="font-size: 11pt;">
                                                                            Stamp: <br>
                                                                            <div
                                                                                style="margin-top: 10px;">CFOCN</div>
                                                                        </div>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="footerSignature" style="margin-top: 50px">
                                                                <div
                                                                    style="font-size:12pt;font-family:Trebuchet MS;margin-top:100px;">
                                                                    TOPOLOGY:</div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div id="footer-view">
                                                        <div>
                                                            <hr>
                                                        </div>
                                                        <div style="text-align: right;margin-top: -15px;">
                                                            <span style="font-size: 9pt;">FIBER OPTIC PROJECT
                                                                CONFIDENTIAL
                                                                INFORMATION</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>
                                                    <div style="height: 70px;display:none;" id="footer-print" >
                                                        <div style="position: fixed;bottom: 0;width: 98% !important;height: 70px;">
                                                            <div>
                                                                <hr>
                                                            </div>
                                                            <div style="text-align: right;margin-top: -15px;">
                                                                <span style="font-size: 9pt;">FIBER OPTIC PROJECT
                                                                    CONFIDENTIAL
                                                                    INFORMATION</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
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
    Alpine.data('xDetail', () => ({
        submitLoading: false,
        data: null,
        purchaseOrderDetails: [],
        async init() {
            this.submitLoading = true;
            let dataStore = this.$store.detail.options.data;
            this.data = dataStore;
            setTimeout(() => {
                this.submitLoading = false;
            }, 400);
            this.purchaseOrderDetails = dataStore.purchase_order_detail;
        },
        dialogClose() {
            this.$store.detail.active = false;
        },
        numberFormat(num) {
            return new Intl.NumberFormat().format(num.toFixed(2));
        },
        exportPdf() {
            let date = moment().format('MM_DD_YYYY_HH_mm_ss');
          
            let title = `${this.data.subject}_on_date_${date}`;
            var element = document.getElementById('printableArea');
            var opt1 = {
                margin: 0.7,
                filename: `${title}.pdf`,
                jsPDF: {
                    unit: 'in',
                    format: 'A4',
                    orientation: 'portrait'
                }
            };
            let nbPages = 3;
            var opt = {
                margin: 0.7,
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

        printPageArea(areaID) {
            $('#footer-view').hide();
            $('#footer-print').show();
            let date = moment().format('MM_DD_YYYY_HH_mm_ss');
            const currTitle = document.title;
            document.title = `${this.data.subject}_on_date_${date}`;;
            printJS({
                printable: areaID,
                type: 'html',
                css: [`{{ asset('admin-public/css/invoice/detail.css') }}`],
                onPrintDialogClose: (res) => {
                    document.title = currTitle;
                },
                onIncompatibleBrowser: (res) => {},
                modalMessage: 'Retrieving Document...',
            });
            $('#footer-print').hide();
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
        changFormatDate(dateString) {
            const date = new Date(dateString);

            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };

            const formattedDate = date.toLocaleDateString('en-US', options);
            const formattedDateWithSuffix = formattedDate.replace(/\b(\d{1,2})\b/g, this.addOrdinalSuffix);

            return formattedDateWithSuffix;
        },
        addOrdinalSuffix(number) {
            const suffixes = ['th', 'st', 'nd', 'rd'];
            const suffix = suffixes[(number - 20) % 10] || suffixes[number] || suffixes[0];
            return number + suffix;
        }

    }));
</script>

{{-- store --}}
<script>
    Alpine.store('detail', {
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
    window.detail = (options) => {
        Alpine.store('detail', {
            active: true,
            config: options?.config ?? null,
            options: {
                ...Alpine.store('detail').options,
                ...options
            }
        });
    };
</script>
