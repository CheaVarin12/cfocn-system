<table>
    @php
        $invoiceSaleLease = [];
        $invoiceServiceInProject = [];
        $invoiceCreditNoteInProject = [];
        
        $allInvoicesSaleLease = array_merge($data['lease'], $data['sale']);
        
        foreach ($allInvoicesSaleLease as $invoice) {
            if ($invoice?->purchase?->project_id == $data['project']->id) {
                array_push($invoiceSaleLease, $invoice);
            }
        }
        foreach ($data['service'] as $invoice) {
            if ($invoice?->purchase?->project_id == $data['project']->id) {
                array_push($invoiceServiceInProject, $invoice);
            }
        }
        foreach ($data['creditNote'] as $invoice) {
            if ($invoice?->purchase?->project_id == $data['project']->id) {
                array_push($invoiceCreditNoteInProject, $invoice);
            }
        }
        static $n = 1;
    @endphp

    <tr>
        <th colspan="4" style="vertical-align:middle; text-align:center;"><b>{{ $data['project']->name }} Cable
                Networks - Items List</b> </th>
    </tr>
    <tr>
        <td rowspan="2" style="vertical-align:middle; text-align:center;"><b>#</b></td>
        <td style="vertical-align:middle; text-align:center;"><b>Revenue Category</b></td>
        <td rowspan="2" style="vertical-align:middle; text-align:center;"><b>Item Name</b></td>
        <td rowspan="2" style="vertical-align:middle; text-align:center;"><b>Description</b></td>
    </tr>
    <tr>
        <td style="vertical-align:middle; text-align:center;">
            <i>(Submarine Cable Chages/ Other Charges / Credit Note / Debit Note)</i>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:middle; text-align:center;"><i>e.g.</i></td>
        <td style="vertical-align:middle; text-align:center;">
            @if ($data['project']->id == 2)
                <i>Submarine Cable Charges</i>
            @else
                <i>Optical Cable Networks Charges</i>
            @endif
        </td>
        <td style="vertical-align:middle; text-align:center;">
            <i>
                @if ($data['project']->id == 2)
                    Leasing (capacity)
                @else
                    Duct Leasing
                @endif
            </i>
        </td>
        <td style="vertical-align:middle; text-align:left;"><i>Monthly subscription fee</i></td>
    </tr>

    @foreach ($invoiceSaleLease as $invoice)
        <tr>
            <td style="vertical-align:middle; text-align:center;">{{ $n++ }}</td>
            <td style="vertical-align:middle; text-align:center;">
                @if ($data['project']->id == 2)
                    Submarine Cable Charges
                @else
                    Optical Cable Networks Charges
                @endif
            </td>
            <td style="vertical-align:middle; text-align:left;">{{ $invoice?->purchase?->type?->name }}</td>
            <td style="vertical-align:middle; text-align:left;"></td>
        </tr>
    @endforeach
    @foreach ($invoiceServiceInProject as $invoice)
        <tr>
            <td style="vertical-align:middle; text-align:center;">{{ $n++ }}</td>
            <td style="vertical-align:middle; text-align:center;">Other Charges</td>
            <td style="vertical-align:middle; text-align:left;">{{ $invoice?->purchase?->type?->name }}</td>
            <td style="vertical-align:middle; text-align:left;"></td>
        </tr>
    @endforeach
    @foreach ($invoiceCreditNoteInProject as $invoice)
        <tr>
            <td style="vertical-align:middle; text-align:center;">{{ $n++ }}</td>
            <td style="vertical-align:middle; text-align:center;">Credit Notes</td>
            <td style="vertical-align:middle; text-align:left;">{{ $invoice?->purchase?->type?->name }}</td>
            <td style="vertical-align:middle; text-align:left;"></td>
        </tr>
    @endforeach
</table>
