<div class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    @if (count($data) > 0)
                        <div class="reportTitle">
                            <h3>Invoice & Receipt</h3>
                            <span></span>
                        </div>
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p class="fontWeight"></p>
                                </div>
                            </div>
                            <div class="excel-header-right">

                            </div>
                        </div>
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
                                    <th class="row" colspan="14">Invoice</th>
                                    <th class="row">
                                        <div style="width: 10px;"></div>
                                    </th>
                                    <th class="row" colspan="14">Receipt</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th class="row">Jan-{{ $shortYear }}</th>
                                    <th class="row">Feb-{{ $shortYear }}</th>
                                    <th class="row">Mar-{{ $shortYear }}</th>
                                    <th class="row">Apr-{{ $shortYear }}</th>
                                    <th class="row">May-{{ $shortYear }}</th>
                                    <th class="row">Jun-{{ $shortYear }}</th>
                                    <th class="row">Jul-{{ $shortYear }}</th>
                                    <th class="row">Aug-{{ $shortYear }}</th>
                                    <th class="row">Sep-{{ $shortYear }}</th>
                                    <th class="row">Oct-{{ $shortYear }}</th>
                                    <th class="row">Nov-{{ $shortYear }}</th>
                                    <th class="row">Dec-{{ $shortYear }}</th>
                                    <th class="row">Total</th>
                                    <th class="row"></th>
                                    <th class="row">Jan-{{ $shortYear }}</th>
                                    <th class="row">Feb-{{ $shortYear }}</th>
                                    <th class="row">Mar-{{ $shortYear }}</th>
                                    <th class="row">Apr-{{ $shortYear }}</th>
                                    <th class="row">May-{{ $shortYear }}</th>
                                    <th class="row">Jun-{{ $shortYear }}</th>
                                    <th class="row">Jul-{{ $shortYear }}</th>
                                    <th class="row">Aug-{{ $shortYear }}</th>
                                    <th class="row">Sep-{{ $shortYear }}</th>
                                    <th class="row">Oct-{{ $shortYear }}</th>
                                    <th class="row">Nov-{{ $shortYear }}</th>
                                    <th class="row">Dec-{{ $shortYear }}</th>
                                    <th class="row">Total</th>
                                </tr>
                            </thead>
                            <tbody class="column" style="margin-bottom: 12px;">
                                @foreach ($data as $key => $item)
                                    @foreach ($item['customer'] as $value)
                                        <tr>
                                            <td class="row textLeft" style="padding-left:20px;">
                                                {{ $value['name_en'] }}
                                            </td>
                                            @foreach ($value['invoice'] as $amount)
                                                <td>{{ $amount ? '$ ' . number_format($amount, 2) : 0 }}</td>
                                            @endforeach
                                            <td class="row"></td>
                                            @foreach ($value['receipt'] as $amount)
                                                <td>{{ $amount ? '$ ' . number_format($amount, 2) : 0 }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr style="background-color: #4279e5">
                                        <td class="row textLeft text-white">
                                            <div>{{ $key }}</div>
                                        </td>
                                        @foreach ($item['total_amount_invoice'] as $totalAmount)
                                            <td class="text-white">
                                                {{ $totalAmount ? '$ ' . number_format($totalAmount, 2) : 0 }}
                                            </td>
                                        @endforeach
                                        <td class="row text-white"></td>
                                        @foreach ($item['total_amount_receipt'] as $totalAmount)
                                            <td class="text-white">
                                                {{ $totalAmount ? '$ ' . number_format($totalAmount, 2) : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                <tr>
                                    <td class="textRight">Total&nbsp;:</td>
                                    @foreach ($totalAllInvoiceByMonth as $value)
                                        <td style="white-space: nowrap;"> {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
                                    @endforeach
                                    <td></td>
                                    @foreach ($totalAllReceiptByMonth as $value)
                                        <td style="white-space: nowrap;"> {{ $value ? '$ ' . number_format($value, 2) : 0 }}</td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    @component('admin::components.emptyReport', [
                        'name' => 'Customer DMC Report empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 10px 0 80px 0;',
                    ])
                    @endcomponent
                @endif
            </div>
        </div>
    </div>
</div>
