<div class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    @if (count($data) > 0)
                        <div class="reportTitle">
                            <h3> Annual Report</h3>
                            <span></span>
                        </div>
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p class="fontWeight"></p>
                                </div>
                            </div>
                            <div class="excel-header-right">

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="tableCustomScroll">
            <div class="table excel">
                @if (count($data) > 0)
                    <div class="excel-body">
                        <table class="tableWidth">
                            <thead class="column">
                                <tr>
                                    <th class="row" colspan="15">
                                        Statistical functions,Total: {{ count($data) }} types
                                        <br>
                                        统计功能，总共 {{ count($data) }} 个类型
                                    </th>
                                </tr>
                                <tr>
                                    <th class="row">No <br>序号</th>
                                    <th class="row">ISP customer name <br> ISP客户名称</th>
                                    <th class="row">JAN <br> 1月</th>
                                    <th class="row">FEB <br> 2月</th>
                                    <th class="row">MAR <br> 3月</th>
                                    <th class="row">APR <br> 4月</th>
                                    <th class="row">MAY <br> 5月</th>
                                    <th class="row">JUN <br> 6月</th>
                                    <th class="row">JUL <br> 7月</th>
                                    <th class="row">AUG <br> 8月</th>
                                    <th class="row">SEP <br> 9月</th>
                                    <th class="row">OCT <br> 10月</th>
                                    <th class="row">NOV <br> 11月</th>
                                    <th class="row">DEC <br> 12月</th>
                                    <th class="row">Total {{ $from_date }} - {{ $to_date }}</th>
                                </tr>
                            </thead>
                            <tbody class="column" style="margin-bottom: 12px;">
                                <tr>
                                    <td colspan="15" style="border-left: none;border-right: none;">
                                        <div class="p-2"></div>
                                    </td>
                                </tr>
                                @foreach ($data as $key => $item)
                                    @foreach ($item['isp'] as $keyIsp => $value)
                                        <tr @click="reportDetailDialog({{ json_encode(array_diff_key($value, ['total' => ''])) }})">
                                            <td>{{ $keyIsp + 1 }}</td>
                                            <td class="row textLeft" style="padding-left:20px;">
                                                {{ $value['name_en'] }}
                                            </td>
                                            @foreach ($value['total'] as $amount)
                                                <td>{{ $amount ? '$ ' . number_format($amount, 2) : 0 }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr style="background-color: #4279e5">
                                        <td class="row textLeft text-white" colspan="2">
                                            @foreach (config('dummy.fttx_status_total') as $value)
                                                @if (in_array($value['key'] , $fttx_status))
                                                    <div style="font-size: 12px;">{{ $value['text'] }} ({{ $key }})</div>
                                                @endif
                                            @endforeach
                                        </td>
                                        @foreach ($item['total_amount'] as $totalAmount)
                                            <td class="text-white" style="font-size: 12px;">
                                                {{ $totalAmount ? '$ ' . number_format($totalAmount, 2) : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td colspan="15" style="border-left: none;border-right: none;">
                                            <div class="p-2"></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                <tr>
                                    <td class="textRight" colspan="2" style="font-size: 13px;">Total&nbsp;:</td>
                                    @foreach ($totalAllAmountByMonth as $value)
                                        <td style="white-space: nowrap;font-size: 13px;"> {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
                                    @endforeach
                                </tr>
                            </tfoot> 
                        </table>
                    </div>
                @else
                    @component('admin::components.emptyReport', [
                        'name' => 'Report empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 10px 0 80px 0;',
                    ])
                    @endcomponent
                @endif
            </div>
        </div>
    </div>
</div>
