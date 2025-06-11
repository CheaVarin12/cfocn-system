<table>
    <thead>
        <tr>
            <th colspan="27" style="vertical-align:middle; text-align:center;">Invoice &amp; Receipt Summary</th>
        </tr>
        <tr>
            <th colspan="14" style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Invoice</th>
            <th colspan="13" style="vertical-align:middle; text-align:center;background-color:#E2EFDA;">Receipt</th>
        </tr>
        <tr>
            <th style="vertical-align:middle; text-align:center;background-color:#E2EFDA;"></th>
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
            @foreach ($item['customer'] as $value)
                <tr>
                    <td style="padding-left:20px;">
                        {{ $value['name_en'] }}
                    </td>
                    @foreach ($value['invoice'] as $amount)
                        <td>{{ $amount ? '$ ' . number_format($amount, 2) : 0 }}</td>
                    @endforeach
                    @foreach ($value['receipt'] as $amount)
                        <td>{{ $amount ? '$ ' . number_format($amount, 2) : 0 }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr>
                <td style="background-color: #4279e5;color:white">
                    <div>{{ $key }}</div>
                </td>
                @foreach ($item['total_amount_invoice'] as $totalAmount)
                    <td style="background-color: #4279e5;color:white">
                        {{ $totalAmount ? '$ ' . number_format($totalAmount, 2) : 0 }}
                    </td>
                @endforeach
                @foreach ($item['total_amount_receipt'] as $totalAmount)
                    <td style="background-color: #4279e5;color:white">
                        {{ $totalAmount ? '$ ' . number_format($totalAmount, 2) : 0 }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total:</td>
            @foreach ($totalAllInvoiceByMonth as $value)
                <td style="vertical-align:middle; text-align:center;background-color:#E2EFDA;"> {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
            @endforeach
            @foreach ($totalAllReceiptByMonth as $value)
                <td style="vertical-align:middle; text-align:center;background-color:#E2EFDA;"> {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
            @endforeach
        </tr>
    </tfoot>

</table>
