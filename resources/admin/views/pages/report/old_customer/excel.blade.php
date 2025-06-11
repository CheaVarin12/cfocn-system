<table>
    <tr>
        <td style="vertical-align:middle; text-align:left;" colspan="6">
            Operator Name :(Cambodia) Fiber Optic Communication Network (CFOCN)
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
        <th style="vertical-align:middle; text-align:center;">Deactive Date</th>
    </tr>
    @foreach ($pac as $item)
        <tr>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->register_date ? date('d/m/Y', strtotime($item->register_date)) : '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->customer_code ?? '' }}</td>
            <td style="vertical-align:middle;">{{ $item?->customer_name ?? '' }} </td>
            <td style="vertical-align:middle;text-align:center;">
                {{ $item?->po_number ?? '' }}
            </td>
            <td style="vertical-align:middle;text-align:center;">
                {{ $item?->pac_number ?? '' }}
            </td>
            <td style="vertical-align:middle;">
                {{ $item?->customer_address ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->service_type ?? '' }}
            </td>
            <td style="vertical-align:middle;">{{ $item->description ?? '' }}</td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->type ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->qty ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:left;">
                {{ $item?->length ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item->status == 1 ? 'Active' : 'Deactive' }}
            </td>
            <td class="row table-row-10">
                @if ($item->status == 2)
                    {{ $item->inactive_date ? \Carbon\Carbon::parse($item->inactive_date)->format('d/m/Y') : \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }}
                @endif
            </td>
        </tr>
    @endforeach
</table>
