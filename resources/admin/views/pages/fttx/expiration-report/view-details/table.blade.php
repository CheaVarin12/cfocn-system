<div class="content-body footerTableScroll">
    <div class="tableLayoutCon tableLayoutWithFooter " style="height: 90vh;">
        <div class="tableLy">
            <div class="tableCustomScroll">
                <div class="table excel">

                    <template x-if="reportData.length > 0">
                        <div class="excel-body">
                            <table class="tableWidth">
                                <thead class="column">
                                    <tr>
                                        <th class="row">No</th>
                                        <th class="row">Work order isp</th>
                                        <th class="row">Work order cfocn</th>
                                        <th class="row">Status</th>
                                        <th class="row">Name</th>
                                        <th class="row">Pos speed </th>
                                        <th class="row">Deadline</th>
                                        <th class="row">Rental Price</th>
                                        <th class="row">PPCC </th>
                                        <th class="row">Rental pole </th>
                                        <th class="row">Total</th>
                                        <th class="row">Expired month</th>
                                        <th class="row">Grand total</th>
                                    </tr>
                                </thead>
                                <tbody class="column" style="margin-bottom: 12px;">
                                    <template x-for="(item,index) in reportData">
                                        <tr>
                                            <td class="row" x-text="index+1"></td>
                                            <td class="row" x-text="item.work_order_isp ?? ''"></td>
                                            <td class="row" x-text="item.work_order_cfocn ?? ''"></td>
                                            <td class="row" x-text="item.status_text ?? ''"></td>
                                            <td class="row" x-text="item.name ?? ''"></td>
                                            <td class="row" x-text="item.pos_speed.split_pos ?? ''"></td>
                                            <td class="row" x-text="moment(item.deadline).format('DD-MMM-YY') ?? '-'"></td>
                                            <td class="row" x-text="item.rental_price ?? ''"></td>
                                            <td class="row" x-text="item.ppcc ?? ''"></td>
                                            <td class="row" x-text="item.rental_pole ?? ''"></td>
                                            <td class="row" x-text="item.total ?? ''"></td>
                                              <td class="row" x-text="item.expired_months ?? ''"></td>
                                            <td class="row" x-text="item.grand_total  ?? ''"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                    <tr>

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
