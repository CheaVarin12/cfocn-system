<div class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>{{ $year . $month }} Infra_Invoice_Summary</h3>
                        <span></span>
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
                                    <th>No.</th>
                                    <th>Date</th>
                                    <th>Invoice No.</th>
                                    <th>Type</th>
                                    <th>customer</th>
                                    <th>Description</th>
                                    <th>Length(km)</th>
                                    <th>Amount</th>
                                    <th>VAT</th>
                                    <th>Total Amount</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                @foreach ($data as $index => $item)
                                    <tr class="{{ $item->type_invoice == 'void_invoice' || $item->type_invoice == 'credit_note' ? 'bgRed' : '--' }}">
                                        <td>{{ $index+1 }}</td>
                                        <td class="textLeft">
                                            {{ $item?->issue_date ? $item?->issue_date : '--' }}
                                        </td>
                                        <td class="textLeft" style="width: 200px !important">
                                            {{ $item?->credit_note_number ?? $item?->invoice_number }}
                                        </td>
                                        <td class="textLeft">
                                            @if($item?->purchase)
                                                {{ $item?->purchase ? $item?->purchase?->type?->name : '--' }}
                                            @elseif($item?->order)
                                                 {{ $item?->order ? $item?->order?->type?->name : '--' }}
                                            @else
                                            --
                                            @endif
                                        </td>
                                        <td class="textLeft">
                                            {{ $item?->customer ? $item?->customer?->name_en : '--' }}
                                        </td>
                                        <td class="textLeft" style="white-space: unset !important;">
                                            @foreach($item->invoiceDetail as $invoice)
                                              {{ $invoice->des }}, <br>
                                            @endforeach
                                          </td>
                                        <td class="textLeft">
                                            {{ $item?->purchase ? $item?->purchase?->length : '--' }}
                                        </td>
                                        <td class="textLeft">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ number_format($item->total_price, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="textLeft">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ number_format($item->vat, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="textLeft">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ number_format($item->total_grand, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="textLeft">
                                            {{ isset($item->deleted_at) ? 'Void' : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                <tr>
                                    <td colspan="7" class="textRight">Total&nbsp;:</td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalPrice, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalVat, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalGrand, 2) }}</span>
                                        </div>
                                    </td>
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
