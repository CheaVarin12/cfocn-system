<div class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    @if (count($data) > 0)
                        <div class="reportTitle">
                            <h3>របាយការណ៍អតិថិជន</h3>
                            <span>CUSTOMER DMC REPORT</span>
                        </div>
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p class="fontWeight">Operator Name : (Cambodia) Fiber Optic Communication Network (CFOCN)</p>
                                </div>
                            </div>
                            <div class="excel-header-right">
                                @if (count($projectInExport) > 0)
                                    <p class="fontWeight">Project :
                                        <?php $i = 0; ?>
                                        @foreach ($projectInExport as $index => $item)
                                            <span>{{ $item?->vat_tin }}</span>
                                            <?php $i++; ?>
                                            @if (count($projectInExport) > $i)
                                                <span>/</span>
                                            @endif
                                        @endforeach
                                @endif
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
                                    <th class="row table-row-5">Nº</th>
                                    <th class="row table-row-10">Register Date</th>
                                    <th class="row table-row-5">Customer ID</th>
                                    <th class="row table-row-10">Customer Name</th>
                                    <th class="row table-row-10">Register Date (Purchase Order)</th>
                                    <th class="row table-row-5">Po No (Purchase Order)</th>
                                    <th class="row table-row-10">Date (PAC)/Billing Date</th>
                                    <th class="row table-row-5">PAC No (Purchase Acceptance Certificate)</th>
                                    <th class="row table-row-10">Customer Address</th>
                                    <th class="row table-row-10">Products (Leasing(Capacity) or ...)</th>
                                    <th class="row table-row-10">Type（Cores or Mbps...）</th>
                                    <th class="row table-row-10">QTY (Cores or Mbps …)</th>
                                    <th class="row table-row-5">Length (km)</th>
                                    <th class="row table-row-10">Status (Active or Deactive or ...)</th>
                                    <th class="row table-row-10">Location</th>
                                    <th class="row table-row-10">Deactive Date</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                @foreach ($data as $index => $item)
                                    <tr @click="editInformationDialog({{ $item }})" id="row-id{{ $item->id }}">
                                        <td class="row table-row-5 textLeft">
                                            {{$index+1}}
                                        </td>
                                        <td class="row table-row-10 textLeft">
                                            {{ $item?->register_date ? date('d/M/Y', strtotime($item->register_date)) : 'N/A' }}
                                        </td>
                                        <td class="row table-row-5">{{ $item?->customer_code ?? '' }}</td>
                                        <td class="row table-row-10 textLeft">{{ $item?->customer_name ?? '' }}</td>
                                        <td class="row table-row-10 textLeft">
                                            {{ $item?->po_date ? date('d/M/Y', strtotime($item->po_date)) : 'N/A' }}
                                        </td>
                                        <td class="row table-row-5 textLeft">
                                            {{ $item?->po_number ?? '' }}
                                        </td>
                                        <td class="row table-row-10 textLeft">
                                            {{ $item?->pac_date ? date('d/M/Y', strtotime($item->pac_date)) : 'N/A' }}
                                        </td>
                                        <td class="row table-row-5 textLeft">
                                            {{ $item?->pac_number ?? '' }}
                                        </td>
                                        <td class="row table-row-10 textLeft">
                                            {{ $item?->customer_address ?? '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item?->service_type ?? '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item?->type ?? '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item?->qty_cores ?? '' }}
                                        </td>
                                        <td class="row table-row-5">{{ $item?->length ?? '' }}</td>
                                        <td class="row table-row-10">
                                            {{ $item?->status == 1 ? 'Active' : 'Deactive' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item?->location ?? '' }}
                                        </td>
                                        <td class="row table-row-10">
                                            @if ($item?->status == 2)
                                                {{ $item?->inactive_date ? date('d/M/Y', strtotime($item->inactive_date)) : '' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
