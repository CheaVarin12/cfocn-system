<div class="tableLayoutCon {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>របាយការណ៍ទទួលការបង់ប្រាក់</h3>
                        <span>REPORT receive payment</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="tableCustomScroll">
            <div class="table excel">
                @if (count($data) > 0)
                    <div class="excel-body">
                        <table class="tableWidth">
                            <thead class="column">
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Invoice Number</th>
                                    <th>Business Type</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                <tr>
                                    <td class="textLeft" colspan="6" style="font-weight: unset;-webkit-text-stroke: 0.5px;">
                                        <span>{{ $from_date ? date('d-M-Y', strtotime($from_date)) : '' }}</span>
                                        <span>&nbsp;/&nbsp;</span>
                                        <span>{{ $to_date ? date('d-M-Y', strtotime($to_date)) : '' }}</span>
                                    </td>
                                </tr>
                                @foreach ($data as $index => $item)
                                    <tr class="{{ isset($item->credit_note_number) ? 'bgRed' : '' }}">
                                        <td class="textLeft">
                                            {{ $item?->issue_date ? date('d-M-Y', strtotime($item->issue_date)) : '' }}
                                        </td>
                                        <td class="textLeft">{{ $item->type_invoice == 'credit_note' ?$item?->credit_note_number : $item?->receipt_number }}</td>
                                        <td class="textLeft">
                                            <span>{{ $item?->data_customer ? json_decode($item->data_customer)->name_en : ($item?->customer?->name_en ?? $item?->customer?->name_kh) }}</span>
                                        </td>
                                        <td class="textLeft">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ number_format($item->total_grand, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="textLeft">
                                            {{ $item?->invoices?->invoice_number }}
                                        </td>
                                        <td class="textLeft">
                                            {{ $item?->invoices?->purchase?->type?->name ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                <tr>
                                    <td colspan="3" class="textRight">Total&nbsp;:</td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($paidAmount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft"></td>
                                    <td class="textLeft"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    @component('admin::components.emptyReport', [
                        'name' => 'Empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 10px 0 80px 0;',
                    ])
                    @endcomponent
                @endif
            </div>
        </div>
    </div>
</div>
