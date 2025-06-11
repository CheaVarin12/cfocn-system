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
                {{ $item?->register_date ? date('d-M-Y', strtotime($item->register_date)) : '--' }}</td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->customer_code ?? '--' }}</td>
            <td style="vertical-align:middle;">{{ $item?->name_en ?? '--' }} </td>
            <td style="vertical-align:middle;text-align:center;">
                {{ $item?->latestPurchase?->po_number ?? '' }}
            </td>
            <td style="vertical-align:middle;text-align:center;">
                {{ $item?->latestPurchase?->pac_number ?? '' }}
            </td>
            <td style="vertical-align:middle;">
                {{ $item?->address_en ?? '--' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->latestPurchase?->type?->name ?? '' }}
            </td>
            <td style="vertical-align:middle;">
                @if ($item?->latestPurchase?->purchaseDetail)
                    @foreach ($item->latestPurchase->purchaseDetail as $itemdes)
                        <div>-&nbsp;{{ $itemdes->des ? $itemdes->des : '--' }}</div><br>
                    @endforeach
                @endif
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->latestPurchase?->pac_type ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->latestPurchase?->total_qty ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:left;">
                {{ $item?->latestPurchase?->length ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item->status == 1 ? 'Active' : 'Deactive' }}
            </td>
        </tr>
    @endforeach
</table>
