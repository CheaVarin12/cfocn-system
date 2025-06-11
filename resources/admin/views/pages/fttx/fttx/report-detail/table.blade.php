<div class="content-body footerTableScroll">
    <div class="tableLayoutCon tableLayoutWithFooter " style="height: 80vh;">
        <div class="content-form-filter">
            <div>

            </div>
            <div class="form-filter">
                <div class="form-row row-search">
                    <input type="date" id="startDate"
                        x-model="formDataSearch.start_date" name="start_date" x-ref="start_date">
                </div>
                <div class="form-row row-search">
                    <input type="date" id="endDate" 
                        x-model="formDataSearch.end_date" name="end_date" x-ref="end_date">
                </div>
                <button mat-flat-button="" type="submit" class="btn-create bg-primary minWithAuto" @click="getDetailData(dialogData.id)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
                <button class="refresh" @click="refresh(dialogData.id)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg>
                </button>
            </div>
        </div>
        <div class="tableLy">
            <div class="tableCustomScroll">
                <div class="table excel">
                    <template x-if="reportData.length > 0">
                        <div class="excel-body">
                            <table class="tableWidth">
                                <thead class="column">
                                    <tr>
                                        <th class="row">No</th>
                                        <th class="row">Date</th>
                                        <th class="row">Expiry Date</th>
                                        <th class="row">New Installation Fee</th>
                                        <th class="row">Fiber Jumper Fee</th>
                                        <th class="row">Digging Fee</th>
                                        <th class="row">Rental Unit Price</th>
                                        <th class="row">ppcc</th>
                                        <th class="row">Pole Rental Fee</th>
                                        <th class="row">Other Fee</th>
                                        <th class="row">Discount</th>
                                        <th class="row">Remark</th>
                                        <th class="row">Invoice Number</th>
                                        <th class="row">Receipt Number</th>
                                        <th class="row">Total Amount</th>
                                        <th class="row">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="column" style="margin-bottom: 12px;">
                                    <template x-for="(item,index) in reportData">
                                        <tr @contextmenu.prevent="storeDialog(item)">
                                            <td class="row" x-text="index+1"></td>
                                            <td class="row" x-text="moment(item.date).format('MMM-YYYY') ?? '-'">
                                            </td>
                                            <td class="row"
                                                x-text="moment(item.expiry_date).format('DD-MMM-YY') ?? '-'">
                                            </td>
                                            <td class="row"
                                                x-text="item.new_installation_fee ? item.new_installation_fee + '$' : '-'">
                                            </td>
                                            <td class="row"
                                                x-text="item.fiber_jumper_fee ? item.fiber_jumper_fee + '$' : '-'">
                                            </td>
                                            <td class="row" x-text="item.digging_fee ? item.digging_fee + '$' :'-'">
                                            </td>
                                            <td class="row"
                                                x-text="item.rental_unit_price ? item.rental_unit_price + '$' : '-'">
                                            </td>
                                            <td class="row" x-text="item.ppcc ? item.ppcc + '$' : '-'">
                                            </td>
                                            <td class="row"
                                                x-text="item.pole_rental_fee ? item.pole_rental_fee + '$' : '-'">
                                            </td>
                                            <td class="row" x-text="item.other_fee ? item.other_fee + '$' : '-'">
                                            </td>
                                            <td class="row" x-text="item.discount ? item.discount + '$' : '-'"></td>
                                            <td class="row" x-text="item.remark ? item.remark : '-'">
                                            </td>
                                            <td class="row" x-text="item.invoice_number ? item.invoice_number : '-'">
                                            </td>
                                            <td class="row" x-text="item.receipt_number ? item.receipt_number : '-'">
                                            </td>
                                            <td class="row"
                                                x-text="item.total_amount ? item.total_amount + '$' : '-'">
                                            </td>
                                            <td class="row">
                                                <i @click="storeDialog(item)"
                                                    onMouseOver="this.style.border='1px solid #1266f1'"
                                                    onMouseOut="this.style.border='none'"
                                                    style="color: #1266f1; font-size: 16px;"
                                                    class="material-symbols-outlined">edit</i>
                                                <i @click="onDelete(item,index)"
                                                    onMouseOver="this.style.border='1px solid #f93154'"
                                                    onMouseOut="this.style.border='none'"
                                                    style="margin-left:3px; color: #f93154 ;font-size: 16px;"
                                                    class="material-symbols-outlined">delete</i>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                    <tr>
                                        <td class="textRight" colspan="3">Total&nbsp;:</td>
                                        <td class="row" x-text="total.new_installation_fee + '$'"></td>
                                        <td class="row" x-text="total.fiber_jumper_fee + '$'"></td>
                                        <td class="row" x-text=" total.digging_fee + '$'"></td>
                                        <td class="row" x-text="total.rental_unit_price + '$'"></td>
                                        <td class="row" x-text="total.ppcc + '$'"></td>
                                        <td class="row" x-text="total.pole_rental_fee + '$'"></td>
                                        <td class="row" x-text="total.other_fee + '$'"></td>
                                        <td class="row" x-text="total.discount + '$'"></td>
                                        <td class="row"></td>
                                        <td class="row"></td>
                                        <td class="row"></td>
                                        <td class="row" x-text="total.total_amount + '$'">Total Amount</td>
                                        <td class="row"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </template>
                    <template x-if="reportData.length < 1">
                        @component('admin::components.emptyReport', [
                            'name' => 'Report is empty',
                            'msg' => 'There is no data.',
                            'style' => 'padding: 10px 0 80px 0;',
                        ])
                        @endcomponent
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
