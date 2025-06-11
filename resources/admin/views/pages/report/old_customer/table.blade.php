<div class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>របាយការណ៍អតិថិជន</h3>
                        <span>REPORT CUSTOMER</span>
                    </div>
                    @if (count($data) > 0)
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p class="fontWeight">Operator Name : (Cambodia) Fiber Optic Communication Network (CFOCN)</p>
                                </div>
                            </div>
                            <div class="excel-header-right"></div>
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
                                    <th class="row table-row-5">Nº</th>
                                    <th class="row table-row-10">Register Date</th>
                                    <th class="row table-row-5">Customer ID</th>
                                    <th class="row table-row-10">Customer Name</th>
                                    <th class="row table-row-5">PO No</th>
                                    <th class="row table-row-5">PAC No.</th>
                                    <th class="row table-row-10">Products (Leasing(Capacity) or ...)</th>
                                    <th class="row table-row-10">Type（Cores or Mbps...）</th>
                                    <th class="row table-row-10">QTY (Cores or Mbps …)</th>
                                    <th class="row table-row-5">Length (km)</th>
                                    <th class="row table-row-10">Status (Active or Deactive or ...)</th>
                                    <th class="row table-row-10">Dective Date</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                @foreach ($data as $index => $item)
                                    <tr @click="detailDialog({{ $item }})">
                                        <td class="row table-row-10">
                                            {{$index+1}}
                                        </td>
                                        <td class="row table-row-10">
                                            {{ $item?->register_date ? date('d/m/Y', strtotime($item->register_date)) : '' }}
                                        </td>
                                        <td class="row table-row-5">{{ $item?->customer_code ?? '' }}</td>
                                        <td class="row table-row-10 textLeft">{{ $item?->customer_name ?? '' }}</td>
                                        <td class="row table-row-5 textLeft">
                                            {{ $item?->po_number ?? '' }}
                                        </td>
                                        <td class="row table-row-5 textLeft">
                                            {{ $item?->pac_number ?? '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item?->service_type ?? '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item?->type ?? '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item?->qty_cores ?? '' }}
                                        </td>
                                        <td class="row table-row-5">{{ $item?->length ?? '' }}</td>
                                        <td class="row table-row-10">
                                            {{ $item->status == 1 ? 'Active' : 'Deactive' }}
                                        </td>
                                        <td class="row table-row-10">
                                            @if ($item->status == 2)
                                                {{ $item->inactive_date ? \Carbon\Carbon::parse($item->inactive_date)->format('d/m/Y') : \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    @component('admin::components.emptyReport', [
                        'name' => 'Customer empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 10px 0 80px 0;',
                    ])
                    @endcomponent
                @endif
            </div>
        </div>
    </div>
</div>
