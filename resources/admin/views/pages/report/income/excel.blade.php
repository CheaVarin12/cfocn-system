<table>
    <tr>
        <td style="vertical-align:middle; text-align:center;" colspan="9">Operator Name : CFOCN</td>
    </tr>
    <tr>
        <th style="vertical-align:middle; text-align:center;">No</th>
        <th style="vertical-align:middle; text-align:center;">Receipt Number</th>
        <th style="vertical-align:middle; text-align:center;">Customer Name</th>
        <th style="vertical-align:middle; text-align:center;">Total Amount</th>
        <th style="vertical-align:middle; text-align:center;">Paid Amount </th>
        <th style="vertical-align:middle; text-align:center;">Paid Date</th>
        <th style="vertical-align:middle; text-align:center;">Payment Method</th>
        <th style="vertical-align:middle; text-align:center;">Payment Status</th>
        <th style="vertical-align:middle; text-align:center;">Description</th>
    </tr>
    
      @foreach ($receipts as $key=> $receipt )
     <tr>
         <td style="vertical-align:middle; text-align:center;">{{ $key+1 }}</td>
         <td style="vertical-align:middle; text-align:center;">{{ $receipt->receipt_number ?$receipt->receipt_number:'--' }}</td>
         <td>{!! $receipt?->data_customer ? json_decode($receipt->data_customer)->name_en : $receipt?->customer?->name_en !!}</td>
         <td style="vertical-align:middle; text-align:center;">{{ $receipt->total_grand ? $receipt->total_grand: '--'}}</td>
         <td style="vertical-align:middle; text-align:center;">{{ $receipt->paid_amount ? $receipt->paid_amount: '--'}}</td>
         <td style="vertical-align:middle; text-align:center;">{{ $receipt->paid_date ? $receipt->paid_date : '--'}}</td>
         <td style="vertical-align:middle; text-align:center;">{{ $receipt->payment_method ? $receipt->payment_method : '--'}}</td>
         <td style="vertical-align:middle; text-align:center;">{{ $receipt->payment_status ? $receipt->payment_status : '--'}}</td>
         <td>{{ $receipt->payment_des ? $receipt->payment_des : '--'}} </td>
     </tr>
          
      @endforeach

</table>
