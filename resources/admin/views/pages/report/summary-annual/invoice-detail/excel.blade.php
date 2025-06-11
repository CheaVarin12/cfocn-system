<table>
    <thead>
        <tr>
            <th colspan="21" style="vertical-align:middle; text-align:center;">Invoice Detail Summary</th>
        </tr>
        <tr>
            <th colspan="21" style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Invoice</th>
        </tr>
        <tr>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Customer Name</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">PO No</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Core</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Price</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">PAC Length</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Billing length</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Monthly Income</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Billing start date</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Jan-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Feb-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Mar-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Apr-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">May-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Jun-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Jul-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Aug-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Sep-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Oct-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Nov-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Dec-{{ $shortYear }}</th>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Total</th>
        </tr>
    </thead>
    <tbody style="margin-bottom: 12px;">
        @foreach ($data as $key => $item)
            <tr>
                <td> {{ $item?->customer?->name_en ?? ($item?->customer?->name_kh ?? '-') }}</td>
                <td> {{ $item->po_number ?? $item->order_number}}</td>
                <td> {{ $item->cores }}</td>
                <td> {{ $item->total_unit_price ?? 0 }}</td>
                <td> {{ $item->length }}</td>
                <td> {{ $item->length }}</td>
                <td> {{ $item->total_price ?? 0}}</td>
                <td>{{ $item->issue_date }}</td>
                @foreach ($item->totalInvoice as $value)
                    <td> {{ $value ?? 0 }}</td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td colspan="8" style="vertical-align:middle; text-align:right;background-color:#E2EFDA;" >Total&nbsp;:</td>
            @foreach ($totalAmountOfInvoiceDetail as $value)
                <td style="vertical-align:middle; text-align:right;background-color:#E2EFDA;"> {{ $value ?? 0 }}</td>
            @endforeach
        </tr>
    </tbody>
</table>
