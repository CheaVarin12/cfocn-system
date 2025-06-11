<table>
    <tr>
        <td style="vertical-align:middle; text-align:left;" colspan="6">
            Operator Name :(Cambodia) Fiber Optic Communication Network (CFOCN)
        </td>
        <td style="vertical-align:middle; text-align:right;" colspan="8">Project&nbsp;:&nbsp;
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
        <th style="vertical-align:middle; text-align:center;">Register Date (Purchase Order)</th>
        <th style="vertical-align:middle; text-align:center;">Po No (Purchase Order)</th>
        <th style="vertical-align:middle; text-align:center;">Date (PAC)/Billing Date</th>
        <th style="vertical-align:middle; text-align:center;">PAC No (Purchase Acceptance Certificate)</th>
        <th style="vertical-align:middle; text-align:center;">Customer Address</th>
        <th style="vertical-align:middle; text-align:center;">Products (Leasing(Capacity) or ...)</th>
        <th style="vertical-align:middle; text-align:center;">Type（Cores or Mbps...）</th>
        <th style="vertical-align:middle; text-align:center;">QTY (Cores or Mbps …)</th>
        <th style="vertical-align:middle; text-align:center;">Length (km)</th>
        <th style="vertical-align:middle; text-align:center;">Status (Active or Deactive or ...)</th>
        <th style="vertical-align:middle; text-align:center;">Location</th>
        <th style="vertical-align:middle; text-align:center;">Deactive Date</th>
    </tr>
    @foreach ($pac as $item)
        <tr>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->register_date ? date('d/M/Y', strtotime($item->register_date)) : 'N/A' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->customer_code ?? '' }}
            </td>
            <td style="vertical-align:middle;">
                {{ $item?->customer_name ?? '' }} 
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->po_date ? date('d/M/Y', strtotime($item->po_date)) : '' }}
            </td>
            <td style="vertical-align:middle;text-align:center;">
                {{ $item?->po_number ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->pac_date ? date('d/M/Y', strtotime($item->pac_date)) : '' }}
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
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->type ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->qty_cores ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:left;">
                {{ $item?->length ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->status == 1 ? 'Active' : 'Deactive' }}
            </td>
            <td style="vertical-align:middle; text-align:left;">
                {{ $item?->location ?? '' }}
            </td>
            <td style="vertical-align:middle; text-align:center;">
                @if ($item?->status == 2)
                    {{ $item?->inactive_date ? date('d/M/Y', strtotime($item->inactive_date)) : '' }}
                @endif
            </td>
        </tr>
    @endforeach
</table>
