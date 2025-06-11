<div class="tableLayoutCon {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>របាយការណ៍គណនីទទួលបន្ទុក</h3>
                        <span>REPORT AR Acging</span>
                    </div>
                    @if (count($data) > 0)
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p>Company: (Cambodia) Fiber Optic Communication Network</p>
                                </div>
                            </div>
                            <div class="excel-header-right"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if (count($data) > 0)
            <div class="tableCustomScroll">
                <div class="table excel">
                    <div class="excel-body">
                        <table class="tableWidth">
                            <thead class="column">
                                <tr>
                                    <th rowspan="2" class="row table-row-5">No</th>
                                    <th class="font-size-1 row table-row-95 textCenter" colspan="10">PO
                                        Information
                                    </th>
                                </tr>
                                <tr>
                                    <th class="row table-row-10">Customer ID</th>
                                    <th class="row table-row-20">Customer Name</th>
                                    <th class="row table-row-10">Length</th>
                                    <th class="row table-row-10">Core/Km</th>
                                    <th class="row table-row-10">Total Amount</th>
                                    <th class="row table-row-10">Paid Amount</th>
                                    <th class="row table-row-10">Remaining Amount</th>
                                    <th class="row table-row-20">Note</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td class="row table-row-5">{{ $index + 1 }}</td>
                                        <td class="row table-row-10">{{ $item?->customer_code ?? '--' }}</td>
                                        <td class="row table-row-20 textLeft">
                                            <span>{{ $item->name_en ?? '--' }}</span>
                                            <span>({{ $item->name_kh ?? '--' }})</span>
                                        </td>
                                        <td class="row table-row-10">{{ $item->purchase_totalLength ?? 0 }}</td>
                                        <td class="row table-row-10">{{ $item->purchase_totalCoreKm ?? 0 }}</td>
                                        <td class="row table-row-10">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ number_format($item->invoice_totalAmount, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="row table-row-10">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ number_format($item->invoice_receipt_paid_amount, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="row table-row-10">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ number_format($item->invoice_totalAmount - $item->invoice_receipt_paid_amount, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="row table-row-20"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column">
                                <tr>
                                    <td colspan="3" class="textRight">Total</td>
                                    <td class="">{{ $totalLength }}</td>
                                    <td class="">{{ $totalCoreKm }}</td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalAmount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($paidAmount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalRemainingAmount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @else
            @component('admin::components.emptyReport', [
                'name' => 'AR acging empty',
                'msg' => 'There is no data.',
                'style' => 'padding: 10px 0 80px 0;',
            ])
            @endcomponent
        @endif
    </div>
</div>
