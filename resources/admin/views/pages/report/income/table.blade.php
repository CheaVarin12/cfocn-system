<div class="tableLayoutCon {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>របាយការណ៍ចំណូល</h3>
                        <span>REPORT Income</span>
                    </div>
                    @if (count($data) > 0)
                        {{-- <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p class="fontWeight">Operator Name : (Cambodia) Fiber Optic Communication Network
                                        (CFOCN)</p>
                                </div>
                            </div>
                            <div class="excel-header-right">
                            </div>
                        </div> --}}
                    @endif
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
                                    <th class="row table-row-5">No</th>
                                    <th class="row table-row-10">Receipt Number</th>
                                    <th class="row table-row-15">Customer Name</th>
                                    <th class="row table-row-10">Total Amount</th>
                                    <th class="row table-row-10">Paid Amount</th>
                                    <th class="row table-row-10">Paid Date</th>
                                    <th class="row table-row-10">Payment Method</th>
                                    <th class="row table-row-10">Payment Status</th>
                                    <th class="row table-row-20">Description</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td class="row table-row-5">{{ $index + 1 }}</td>
                                        <td class="row table-row-10">
                                            {{ $item->receipt_number ? $item->receipt_number : '--' }}
                                        </td>
                                        <td class="row table-row-15 textLeft">
                                            {!! $item?->data_customer ? json_decode($item->data_customer)->name_en : $item?->customer?->name_en !!}
                                        </td>
                                        <td class="row table-row-10">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ $item->total_grand ? number_format($item->total_grand, 2) :'' }}</span>
                                            </div>
                                        <td class="row table-row-10">
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ $item->paid_amount ? number_format($item->paid_amount, 2) :'' }}</span>
                                            </div>
                                        </td>
                                        <td class="row table-row-10"> {{ $item->paid_date ? $item->paid_date : '' }}
                                        </td>
                                        <td class="row table-row-10">
                                            {{ $item->payment_method ? $item->payment_method : '' }}
                                        </td>
                                        <td class="row table-row-10">
                                            {{ $item->payment_status ? $item->payment_status : '' }}
                                        </td>
                                        <td class="row table-row-20">
                                            {{ $item->payment_des ? $item->payment_des : '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    @component('admin::components.emptyReport', [
                        'name' => 'Income empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 10px 0 80px 0;',
                    ])
                    @endcomponent
                @endif
            </div>
        </div>
    </div>
</div>
