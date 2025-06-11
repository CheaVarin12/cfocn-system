<div class="revenuelayoutTab">
    <div class="reportTitle">
        <h3>រាយការណ៍ចំណូល</h3>
        <span>(Cambodia) Fiber Optic Communication Network Co.,Ltd.</span>
    </div>
    <template x-for="(item,index) in data">
        <div class="revenueItem">
            <div class="excel-header">
                <h3 class="revenue-text-project" x-text="item?.project_name ?? '---'"></h3>
            </div>
            <div class="excel-body">
                <table>
                    <thead>
                        <tr>
                            <th class="font-size-1">Description</th>
                            <th class="font-size-1">Amount ($)</th>
                            <th class="font-size-1">Percentage (%)</th>
                            <th class="font-size-1">License Fee</th>
                            <th class="font-size-1">Noted</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- numberFormat(numberRound(data?.total_grand, 2)) --}}
                        <template x-for="(val,valIndex) in item?.dataDefault">
                            <tr>
                                <td x-text="val?.name_project_child ?? '---'"></td>
                                <td class="textRight" x-text="numberFormat(numberRound(val?.amount, 2))"></td>
                                <td class="textRight" x-text="val?.licenseFeePercentage"></td>
                                <td class="textRight" x-text="numberFormat(numberRound(val?.licenseFeeAmount, 2))"></td>
                                <td class="textRight"></td>
                            </tr>
                        </template>
                        <tr class="footer">
                            <td class="textRight">Total <span x-text="'('+(index+1)+')'"></span> :</td>
                            <td class="textRight" x-text="numberFormat(numberRound(item?.totalAmount, 2))"></td>
                            <td class="textRight"></td>
                            <td class="textRight" x-text="numberFormat(numberRound(item?.totalLicenseFee, 2))"></td>
                            <td class="textRight"></td>
                        </tr>
                    </tbody>
                    <tfoot x-show ='data.length == (index+1)'>
                        <tr><td class="colEmptyBorder" colspan="5"></td></tr>
                        <tr><td class="colEmptyBorder" colspan="5"></td></tr>
                        <tr class="footer bgGrad">
                            <td class="textRight">Total
                                <template x-for="(item,index) in data.length">
                                    <span x-text="'('+(index+1)+') '"></span>
                                </template>
                                :
                            </td>
                            <td class="textRight" x-text="numberFormat(numberRound(totalAmount, 2))"></td>
                            <td class="textRight"></td>
                            <td class="textRight" x-text="numberFormat(numberRound(totalLicenseFee, 2))"></td>
                            <td class="textRight"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </template>
</div>
