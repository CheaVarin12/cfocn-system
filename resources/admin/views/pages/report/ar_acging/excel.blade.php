<table>
    <thead>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <th colspan="4" style="vertical-align:middle; text-align:left">Company: (Cambodia) Fiber Optic
                Communication Network</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th rowspan="2" style="vertical-align:middle; text-align:center"> No</th>
            <th colspan="12" style="vertical-align:middle; text-align:center"> PO Information </th>
        </tr>
        <tr>
            <th style="vertical-align:middle; text-align:center">Customer ID</th>
            <th style="vertical-align:middle; text-align:center">Customer Name</th>
            <th style="vertical-align:middle; text-align:center">Customer type</th>
            <th style="vertical-align:middle; text-align:center">Income type</th>
            <th style="vertical-align:middle; text-align:center">Cores</th>
            <th style="vertical-align:middle; text-align:center">Unit Price</th>
            <th style="vertical-align:middle; text-align:center">Length </th>
            <th style="vertical-align:middle; text-align:center">Core/Km</th>
            <th style="vertical-align:middle; text-align:center">Total Amount</th>
            <th style="vertical-align:middle; text-align:center">Paid Amount</th>
            <th style="vertical-align:middle; text-align:center">Remaining Amount</th>
            <th style="vertical-align:middle; text-align:center">Note</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
            <tr>
                <th style="vertical-align:middle; text-align:center">{{ ++$index }}</th>
                <th style="vertical-align:middle; text-align:left">{{ $item?->customer_code ?? '--' }}</th>
                <th style="vertical-align:middle; text-align:left">{{ $item?->name_en }}&nbsp;({{ $item?->name_kh }})
                </th>
                <th style="vertical-align:middle; text-align:center"></th>
                <th style="vertical-align:middle; text-align:center"></th>
                <th style="vertical-align:middle; text-align:center"></th>
                <th style="vertical-align:middle; text-align:center"></th>
                <th style="vertical-align:middle; text-align:right">{{ $item->purchase_totalLength ?? 0 }}</th>
                <th style="vertical-align:middle; text-align:right">{{ $item->purchase_totalCoreKm ?? 0 }}</th>
                <th style="vertical-align:middle; text-align:right">
                    $&nbsp;{{ number_format($item->invoice_totalAmount, 2) }}</th>
                <th style="vertical-align:middle; text-align:right">
                    $&nbsp;{{ number_format($item->invoice_receipt_paid_amount, 2) }}</th>
                <th style="vertical-align:middle; text-align:right">
                    $&nbsp;{{ number_format($item->invoice_totalAmount - $item->invoice_receipt_paid_amount, 2) }}</th>
                <th style="vertical-align:middle; text-align:right"></th>
            </tr>
        @endforeach


    </tbody>
    <tfoot>
        <tr>
            <th style="vertical-align:middle; text-align:center" colspan="2"><b>Total</b></th>
            <th style="vertical-align:middle; text-align:center"></th>
            <th style="vertical-align:middle; text-align:center"></th>
            <th style="vertical-align:middle; text-align:center"></th>
            <th style="vertical-align:middle; text-align:center"></th>
            <th style="vertical-align:middle; text-align:right"></th>
            <th style="vertical-align:middle; text-align:right">{{ $totalLength }}</th>
            <th style="vertical-align:middle; text-align:right">{{ $totalCoreKm }}</th>
            <th style="vertical-align:middle; text-align:right">$&nbsp;{{ number_format($totalAmount, 2) }}</th>
            <th style="vertical-align:middle; text-align:right">$&nbsp;{{ number_format($paidAmount, 2) }}</th>
            <th style="vertical-align:middle; text-align:right">$&nbsp;{{ number_format($totalRemainingAmount, 2) }}
            </th>
            <th style="vertical-align:middle; text-align:right"></th>
        </tr>
    </tfoot>
</table>
