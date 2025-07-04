<div class="tableLayoutCon tableLayoutWithFooter ">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    @if (count($data) > 0)
                        <div class="reportTitle">
                            <h3>Expiration Income Report</h3>
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
                                    <th class="row">No </th>
                                    <th class="row">ISP customer name</th>
                                    @foreach ($columns as $column)
                                        <th class="row">{{ $column }}</th>
                                    @endforeach
                                    <th class="row">Total</th>

                                </tr>
                            </thead>
                            <tbody class="column" style="margin-bottom: 12px;">
                                <tr>
                                    <td colspan="{{ count($columns)+3 }}" style="border-left: none;border-right: none;">
                                        <div class="p-2"></div>
                                    </td>
                                </tr>
                                @foreach ($data as $key => $item)
                                    @foreach ($item['isp'] as $keyIsp => $value)
                                        <tr
                                            @click="reportDetailDialog({{ json_encode(array_diff_key($value, ['total' => ''])) }})">
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
                                        <td class="row text-white" colspan="2" style="text-align: right !important">
                                            Total ({{ $key }})
                                        </td>
                                        @foreach ($item['total_amount'] as $totalAmount)
                                            <td class="text-white" style="font-size: 12px;">
                                                {{ $totalAmount ? '$ ' . number_format($totalAmount, 2) : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td colspan="{{ count($columns)+3 }}" style="border-left: none;border-right: none;">
                                            <div class="p-2"></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                <tr>
                                    <td class="textRight" colspan="2" style="font-size: 13px;">Total&nbsp;:</td>
                                    @foreach ($totalAllAmountByMonth as $value)
                                        <td style="white-space: nowrap;font-size: 13px;">
                                            {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
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
