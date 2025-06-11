<table>
    @php
        $amountLease = 0;
        $amountSale = 0;
        $amountSevice = 0;
    @endphp
    <tr>
        <td colspan="5" style="vertical-align:bottom; text-align:center;"> (Cambodia) Fiber Optic Communication Network
            Co.,Ltd.</td>
    </tr>
    <tr></tr>
    @foreach ($data['data'] as $index=>$item)
        @foreach ($data['lease'] as $value)
            @if ($value->purchase->project_id == $item->id)
                {{ $amountLease += $value->total_grand }}
            @endif
        @endforeach
        @foreach ($data['sale'] as $value)
            @if ($value->purchase->project_id == $item->id)
                {{ $amountSale += $value->total_grand }}
            @endif
        @endforeach
        @foreach ($data['service'] as $value)
        @if ($value->purchase->project_id == $item->id)
            {{ $amountSevice += $value->total_grand }}
        @endif
    @endforeach
        <tr>
            <td colspan="5" style="vertical-align:middle; text-align:center;">CFOCN 2022 Income ({{ $item->name }}
                Project) </td>
        </tr>
        <tr>
            <th style="vertical-align:middle; text-align:center;"><b>Decription</b></th>
            <th style="vertical-align:middle; text-align:center;"><b>Amount</b></th>
            <th style="vertical-align:middle; text-align:center;"><b>Percentage</b></th>
            <th style="vertical-align:middle; text-align:center;"><b>License fee</b></th>
            <th style="vertical-align:middle; text-align:center;"><b>Noted</b></th>
        </tr>
        <tr>
            <td style="vertical-align:middle;">Lease Income</td>
            <td style="vertical-align:middle; text-align:right;">{{ $amountLease }}</td>
            <td style="vertical-align:middle; text-align:center;">
                @foreach ($data['licenseFee'] as $value)
                    @if ($value->project_id == $item->id)
                        <span>{{ $value->percentage ?? 0 }}%</span>
                    @endif
                @endforeach
            </td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle;">Sale Income</td>
            <td style="vertical-align:middle; text-align:right;">{{ $amountSale }}</td>
            <td style="vertical-align:middle; text-align:center;">
                @foreach ($data['licenseFee'] as $value)
                    @if ($value->project_id == $item->id)
                        <span>{{ $value->percentage ?? 0 }}%</span>
                    @endif
                @endforeach
            </td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle;">Sevice Income</td>
            <td style="vertical-align:middle; text-align:right;">{{ $amountSevice }}</td>
            <td style="vertical-align:middle; text-align:center;">
                @foreach ($data['licenseFee'] as $value)
                    @if ($value->project_id == $item->id)
                        <span>{{ $value->percentage ?? 0 }}%</span>
                    @endif
                @endforeach
            </td>
            <td style="vertical-align:middle; text-align:right;"></td>
            <td style="vertical-align:middle;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle; text-align:right;"><b>Total ({{ ++$index }}) :</b></td>
            <td style="vertical-align:middle; text-align:right;"><b></b></td>
            <td style="vertical-align:middle;"></td>
            <td style="vertical-align:middle; text-align:right;"><b></b></td>
            <td style="vertical-align:middle;"></td>
        </tr>
        <tr></tr>
        <tr></tr>
        @php
            $amountLease = 0;
            $amountSale = 0;
            $amountSevice = 0;
        @endphp
    @endforeach
        <tr>
            <td style="vertical-align:middle; text-align:right;"> <b></b></td>
            <td style="vertical-align:middle; text-align:right;"><b></b></td>
            <td style="vertical-align:middle; text-align:right;"><b></b></td>
            <td style="vertical-align:middle; text-align:right;"><b></b></td>
            <td style="vertical-align:middle; text-align:right;"><b></b></td>
        </tr>

</table>
