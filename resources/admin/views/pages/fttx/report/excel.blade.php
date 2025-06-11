<thead>
    <tr></tr>
    <tr>
        <th colspan="15" style="vertical-align:middle; text-align:center; background:#E2EFDA;">

        </th>
    </tr>
    <tr>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">No 序号</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">ISP customer name ISP客户名称</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">JAN 1月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">FEB 2月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">MAR 3月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">APR 4月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">MAY 5月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">JUN 6月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">JUL 7月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">AUG 8月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">SEP 9月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">OCT 10月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">NOV 11月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">DEC 12月</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">{{ $from_date }}-
            {{ $to_date }}</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $key => $item)
        @foreach ($item['isp'] as $keyIsp => $value)
            <tr>
                <td style="vertical-align:middle; text-align:center;">{{ $keyIsp + 1 }}</td>
                <td style="padding-left:20px;vertical-align:middle;">
                    {{ $value['name_en'] }}
                </td>
                @foreach ($value['total'] as $amount)
                    <td style="vertical-align:middle; text-align:center;">{{ $amount ? '$ ' . number_format($amount, 2) : 0 }}</td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td colspan="2" style="background-color: #4279e5;color:white;word-wrap: break-word;">
                @foreach (config('dummy.fttx_status_total') as $value)
                    @if (in_array($value['key'] , $fttx_status))
                        {{ $value['text'] }} ({{ $key }})
                    @endif
                @endforeach
            </td>
            @foreach ($item['total_amount'] as $totalAmount)
                <td style="vertical-align:middle; text-align:center;background-color: #4279e5;color:white;">
                    {{ $totalAmount ? '$ ' . number_format($totalAmount, 2) : 0 }}
                </td>
            @endforeach
        </tr>
    @endforeach
</tbody>
<tfoot>
    <tr>
        <td colspan="2" style="vertical-align:middle; text-align:center; background:#E2EFDA;">Total&nbsp;:</td>
        @foreach ($totalAllAmountByMonth as $value)
            <td style="vertical-align:middle; text-align:center; background:#E2EFDA;"> {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
        @endforeach
    </tr>
</tfoot>
