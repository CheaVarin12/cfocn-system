<div class="tableLayoutCon tableLayoutWithFooter {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>របាយការណ៍អតិថិជន</h3>
                        <span>REPORT Customer</span>
                    </div>
                    @if (count($data) > 0)
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <p class="fontWeight">Operator Name : (Cambodia) Fiber Optic Communication Network
                                        (CFOCN)</p>
                                </div>
                            </div>
                            <div class="excel-header-right">
                                @if (count($projectInExport) > 0)
                                    <p class="fontWeight">Project :
                                        <?php $i = 0; ?>
                                        @foreach ($projectInExport as $index => $item)
                                            <span>{{ $item->vat_tin }}</span>
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
                                    <th class="row table-row-10">In-Active Date</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                @foreach ($data as $index => $item)
                                    <tr @click="detailDialog({{$item}})">
                                        <td class="row table-row-10">
                                            {{ $item?->customer?->register_date ? date('d-M-Y', strtotime($item->customer->register_date)) : '' }}
                                        </td>
                                        <td class="row table-row-5">{{ $item?->customer?->customer_code ?? '' }}</td>
                                        <td class="row table-row-10 textLeft">{{ $item?->customer?->name_en ?? '' }}</td>
                                        <td class="row table-row-5 textLeft">{{ $item->po_number ? $item->po_number : '' }}
                                        </td>
                                        <td class="row table-row-5 textLeft">{{ $item->pac_number ? $item->pac_number : '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item->type->name ? $item->type->name : '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item->pac_type ? $item->pac_type : '' }}
                                        </td>
                                        <td class="row table-row-10">{{ $item->total_qty ? $item->total_qty : '' }}
                                        </td>
                                        <td class="row table-row-5">{{ $item->length ? $item->length : '' }}</td>
                                        <td class="row table-row-10">
                                            @if ($item->customer->status == 1)
                                                Active
                                            @else
                                                Deactive
                                            @endif
                                        </td>
                                        <td class="row table-row-10">
                                            @if ($item->customer->status == 1)
                                                Active
                                            @else
                                                Deactive
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
