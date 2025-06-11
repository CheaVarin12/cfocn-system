<table>
    <tr>
        <td style="vertical-align:middle; text-align:left;" colspan="6">Operator Name :(Cambodia) Fiber Optic
            Communication Network (CFOCN)</td>
        <td style="vertical-align:middle; text-align:right;" colspan="6">Project&nbsp;:&nbsp;
            @foreach ($projectInExport as $index => $item)
                <span>{{ $item->vat_tin }}</span>
                @if (count($projectInExport) > $index)
                    <span>/</span>
                @endif
            @endforeach
        </td>
    </tr>
    <tr>
        <th style="vertical-align:middle; text-align:center;">Register Date</th>
        <th style="vertical-align:middle; text-align:center;">Customer ID</th>
        <th style="vertical-align:middle; text-align:center;">Customer Name</th>
        <th style="vertical-align:middle; text-align:center;">Po No</th>
        <th style="vertical-align:middle; text-align:center;">PAC No</th>
        <th style="vertical-align:middle; text-align:center;">Customer Address </th>
        <th style="vertical-align:middle; text-align:center;">Products (Leasing(Capacity) or ...)</th>
        <th style="vertical-align:middle; text-align:center;">Description (Start Point to End Point)</th>
        <th style="vertical-align:middle; text-align:center;">Type (Cores or Mbps...) </th>
        <th style="vertical-align:middle; text-align:center;">QTY (Cores or Mbps ...)</th>
        <th style="vertical-align:middle; text-align:center;">Length (km)</th>
        <th style="vertical-align:middle; text-align:center;">Status (Active or Deactive or ..)</th>
    </tr>
    @foreach ($pac as $item)
        <tr>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item->customer ? date('d-M-Y', strtotime($item->customer->register_date)) : '--' }}</td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item->customer ? $item->customer->customer_code : '--' }}</td>
            <td style="vertical-align:middle;">{{ $item->customer ? $item->customer->name_en : '--' }} </td>
            <td style="vertical-align:middle;text-align:center;">{{ $item->po_number ? $item->po_number : '--' }}</td>
            <td style="vertical-align:middle;text-align:center;">{{ $item->pac_number ? $item->pac_number : '--' }}</td>
            <td style="vertical-align:middle;"> {{ $item->customer ? $item->customer->address_en : '--' }}</td>
            <td style="vertical-align:middle; text-align:center;">{{ $item->type ? $item->type->name : '--' }}</td>
            <td style="vertical-align:middle;">
                @foreach ($item->purchaseDetail as $itemdes)
                    <div>-&nbsp;{{ $itemdes->des ? $itemdes->des : '--' }}</div><br>
                @endforeach
            </td>
            <td style="vertical-align:middle; text-align:center;">{{ $item->pac_type ? $item->pac_type : '--' }}</td>
            <td style="vertical-align:middle; text-align:center;">{{ $item->total_qty ? $item->total_qty : '--' }}</td>
            <td style="vertical-align:middle; text-align:left;">{{ $item->length ? $item->length : '--' }}</td>
            <td style="vertical-align:middle; text-align:center;">
                @if ($item->customer->status == 1)
                    Active
                @else
                    Deactive
                @endif
            </td>
        </tr>
    @endforeach
</table>
