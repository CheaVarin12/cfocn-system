@component('admin::components.dialog', ['dialog' => 'reportCustomerDetail'])
    <div x-data="reportCustomerDetail" class="dialog-form reportDetail" style="width: 700px">
        <div class="titleReportDetail">
            <h3>Customer detail</h3>
            <button type="button" @click="$store.reportCustomerDetail.close(false)" class="btnClose"><i
                    class='bx bx-x'></i></button>
        </div>
        <div class="dialog-form-body">
            <div class="itemText">
                <label style="width: 250px;">Register Date</label>
                <span>:</span>
                <p x-text="dateFormat(data?.register_date)"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">Customer ID</label>
                <span>:</span>
                <p x-text="data?.customer_code ?? ''"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">Customer Name</label>
                <span>:</span>
                <p x-text="data?.name_en ?? ''"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">PO No</label>
                <span>:</span>
                <p x-text="data?.latest_purchase?.po_number ?? ''"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">PAC No.</label>
                <span>:</span>
                <p x-text="data?.latest_purchase?.pac_number ?? ''"></p>
            </div>

            <div class="itemText">
                <label style="width: 250px;">Customer Address</label>
                <span>:</span>
                <div class="div">
                    <p x-text="data?.address_en ?? ''"></p>
                </div>
            </div>
            <div class="itemText">
                <label style="width: 250px;">Products (Leasing(Capacity) or ...)</label>
                <span>:</span>
                <p x-text="data?.latest_purchase?.type?.name ?? ''"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">Description (Start Point to End Point)</label>
                <span>:</span>
                <div class="div">
                    <template x-for="item in data?.latest_purchase?.purchase_detail">
                        <p x-text="item?.des ?? ''"></p>
                    </template>
                </div>
            </div>
            <div class="itemText">
                <label style="width: 250px;">Type（Cores or Mbps...）</label>
                <span>:</span>
                <p x-text="data?.latest_purchase?.pac_type ?? ''"></p>
            </div>

            <div class="itemText">
                <label style="width: 250px;">QTY (Cores or Mbps …)</label>
                <span>:</span>
                <p x-text="data?.latest_purchase?.total_qty ?? ''" class="redQty"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">Length (km)</label>
                <span>:</span>
                <p x-text="data?.latest_purchase?.length ?? ''"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">Status (Active or Deactive or ...)</label>
                <span>:</span>
                <p x-text="data?.status == 1 ? 'Active':'Deactive'"></p>
            </div>
            <div class="itemText">
                <label style="width: 250px;">In Active Date</label>
                <span>:</span>
                <template x-if="data?.status == 2">
                    <p x-text="data?.in_active_date ? dateFormat(data?.in_active_date) : dateFormat(data?.updated_at)"></p>
                </template>
            </div>
            {{-- <div class="itemText">
                <label>បរិយាយ</label>
                <span>:</span>
                <div class="div">
                    <template x-for="item in data?.invoice_detail">
                        <p x-text="item?.des ?? ''"></p>
                    </template>
                </div>
            </div> --}}
        </div>

        {{-- <div class="dialog-form-footer dmc-status-footer">
            <button type="button" class="close" @click="$store.reportSaleJournalDetail.close(false)"
                x-text="data?.btnClose || 'Close'"></button>
        </div> --}}
    </div>
    <script>
        Alpine.data("reportCustomerDetail", () => ({
            data: null,
            disabled: false,
            loading: false,
            timeCloseAuto: 0,
            init() {
                this.data = this.$store.reportCustomerDetail.data;
                this.data.des = null;
                if (this.data?.invoice_detail?.length > 0) {

                }
                feather.replace();
            },
            onConfirm() {
                this.disabled = true;
                this.$store.reportSaleJournalDetail.close(true);
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
            dateFormat(date) {
                return date ? moment(date).format('YYYY-MM-DD') : '---';
            },
        }))
    </script>
@endcomponent
