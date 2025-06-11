<table>
  <thead>
      <tr>
      <td colspan="10" style="text-align: center;vertical-align:middle;">{{ $date }} Submarine_Invoice_Summary</td>
      </tr>
     <tr>
      <th style="vertical-align:middle; text-align:center;">No.</th>
      <th style="vertical-align:middle; text-align:center;">Date</th>
      <th style="vertical-align:middle; text-align:center;">Invoice No.</th>
      <th style="vertical-align:middle; text-align:center;">Type</th>
      <th style="vertical-align:middle; text-align:center;">customer</th>
      <th style="vertical-align:middle; text-align:center;">Description</th>
      <th style="vertical-align:middle; text-align:center;">Amount</th>
      <th style="vertical-align:middle; text-align:center;">VAT</th>
      <th style="vertical-align:middle; text-align:center;">Total Amount</th>
      <th style="vertical-align:middle; text-align:center;">Remark</th>
     </tr>
  </thead>
  <tbody>
      @foreach($data as $key => $item)
          <tr>
              <td style="vertical-align:middle; text-align:center;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{ $key+1 }}</td>
              <td style="vertical-align:middle; text-align:center;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{ $item?->issue_date }}</td>
              <td style="vertical-align:middle; text-align:center;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{ $item?->credit_note_number ?? $item?->invoice_number }}</td>
              <td style="vertical-align:middle;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">
                @if($item?->purchase)
                    {{ $item?->purchase ? $item?->purchase?->type?->name : '--' }}
                @elseif($item?->order)
                    {{ $item?->order ? $item?->order?->type?->name : '--' }}
                @else
                --
                @endif
              </td>
              <td style="vertical-align:middle;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{ $item?->customer?->name_en }}</td>
              <td style="{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">
                @foreach($item->invoiceDetail as $invoice)
                  {{ $invoice->des }}, <br>
                @endforeach
              </td>
              <td style="vertical-align:middle; text-align:center;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{ $item->total_price }}</td>
              <td style="vertical-align:middle; text-align:center;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{ $item->vat }}</td>
              <td style="vertical-align:middle; text-align:center;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{$item->total_grand }}</td>
              <td style="vertical-align:middle; text-align:center;{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'color:red;':'' }}">{{ isset($item->deleted_at) ? 'Void' : '' }}</td>    
          </tr> 
  @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td style="background-color: #C0C0C0;"></td>
      <td style="background-color: #C0C0C0;"></td>
      <td style="background-color: #C0C0C0;"></td>
      <td style="background-color: #C0C0C0;"></td>
      <td style="background-color: #C0C0C0;"></td>
      <td style="background-color: #C0C0C0;"></td>
      <td style="vertical-align:middle; text-align:center;background-color: #C0C0C0;">{{ $totalPrice }}</td>
      <td style="vertical-align:middle; text-align:center;background-color: #C0C0C0;">{{ $totalVat }}</td>
      <td style="vertical-align:middle; text-align:center;background-color: #C0C0C0;">{{ $totalGrand }}</td>
      <td style="vertical-align:middle;background-color: #C0C0C0;text-align:left;"></td>
    </tr>
  </tfoot>
</table>