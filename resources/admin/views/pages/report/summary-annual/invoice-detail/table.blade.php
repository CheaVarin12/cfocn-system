<div class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    @if (count($data) > 0)
                        <div class="reportTitle">
                            <h3>Invoice Detail</h3>
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
                                    <th class="row" colspan="21">Invoice</th>
                                </tr>
                                <tr>
                                    <th class="row">Customer Name</th>
                                    <th class="row">Po No</th>
                                    <th class="row">Core</th>
                                    <th class="row">Price</th>
                                    <th class="row">PAC Length</th>
                                    <th class="row">Billing length</th>
                                    <th class="row">Monthly Income</th>
                                    <th class="row">Billing start date</th>
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
                            <tbody class="column">
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <td class="row textLeft ">
                                            {{ $item?->customer?->name_en ?? $item?->customer?->name_kh ?? '-' }}
                                        </td>
                                        <td class="row textLeft ">
                                            {{ $item->po_number ?? $item->order_number }}
                                        </td>
                                        <td>
                                            {{ $item->cores ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->total_unit_price ? '$ '.number_format($item->total_unit_price, 2):0 }}
                                        </td>
                                        <td>
                                            {{ $item->length ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $item->length ?? '-' }}
                                        </td>
                                          <td>
                                             {{ $item->total_price ? '$ '.number_format($item->total_price, 2):0 }}
                                        </td>
                                        <td>
                                            {{ $item->issue_date ?? '-' }}
                                        </td>
                                        @foreach ($item->totalInvoice as $value)
                                            <td>  {{ $value ? '$ '.number_format($value, 2):0 }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                <tr>
                                    <td colspan="8" class="textRight">Total&nbsp;:</td>
                                    @foreach ($totalAmountOfInvoiceDetail as $value)
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
