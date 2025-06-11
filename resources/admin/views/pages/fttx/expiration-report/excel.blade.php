<thead>
    <tr></tr>
    <tr>
        <th colspan="{{ count($columns)+3 }}" style="vertical-align:middle; text-align:center; background:#E2EFDA;">
            Expiration Income Report
        </th>
    </tr>
    <tr>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">No</th>
        <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">ISP customer name </th>
        @foreach ($columns as $column)
            <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">{{ $column }}</th>
        @endforeach
         <th style="vertical-align:middle; text-align:center; background:#E2EFDA;">Total</th>

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
                    <td style="vertical-align:middle; text-align:center;">
                        {{ $amount ? '$ ' . number_format($amount, 2) : 0 }}</td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td colspan="2" style="background-color: #4279e5;color:white;word-wrap: break-word;">
                Total ({{ $key }})
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
            <td style="vertical-align:middle; text-align:center; background:#E2EFDA;">
                {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
        @endforeach
    </tr>
</tfoot>
