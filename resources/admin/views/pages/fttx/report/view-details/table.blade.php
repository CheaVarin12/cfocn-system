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
                                        <th class="row">JAN </th>
                                        <th class="row">FEB </th>
                                        <th class="row">MAR </th>
                                        <th class="row">APR </th>
                                        <th class="row">MAY </th>
                                        <th class="row">JUN </th>
                                        <th class="row">JUL </th>
                                        <th class="row">AUG </th>
                                        <th class="row">SEP </th>
                                        <th class="row">OCT </th>
                                        <th class="row">NOV </th>
                                        <th class="row">DEC </th>
                                        <th class="row">Total </th>
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
                                            <td class="row" x-text="item.pos_speed_text ?? ''"></td>
                                            <template x-for="(value,key)  in item.amountByMonth">  
                                                <td class="row" x-text="value>0 ? numberRound(value,2) : '-'"></td>
                                            </template>
                                           
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
