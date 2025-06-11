<div class="tableLayoutCon {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>របាយការណ៍អតិថិជន</h3>
                        <span>REPORT Customer Info</span>
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
                                    <th class="row table-row-10">Register Date</th>
                                    <th class="row table-row-5">ID</th>
                                    <th class="row table-row-10 textLeft">Name</th>
                                    <th class="row table-row-5 textLeft">Phone Number</th>
                                    <th class="row table-row-10 textLeft">VAT</th>
                                    <th class="row table-row-10 textLeft">Email</th>
                                    <th class="row table-row-10 textLeft">Status</th>
                                    <th class="row table-row-10 textLeft">Address</th>
                                </tr>
                            </thead>
                            <tbody class="column">
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td class="row table-row-10">
                                            {{ $item?->register_date ? date('d-M-Y', strtotime($item->register_date)) : '' }}
                                        </td>
                                        <td class="row table-row-5">{{ $item?->customer_code ?? '' }}</td>
                                        <td class="row table-row-10 textLeft">
                                            <span>{{ $item?->name_en ?? '' }}</span><br/>
                                            <span>{{ $item?->name_kh ?? '' }}</span>
                                        </td>
                                        <td class="row table-row-5 textLeft span">
                                            <span>{{ $item?->phone ?? '' }}</span>
                                        </td>
                                        <td class="row table-row-10 textLeft">
                                            {{ $item?->vat_tin ?? '' }}
                                        </td>
                                        <td class="row table-row-10 textLeft">
                                            {{ $item?->email ?? '' }}
                                        </td>
                                        <td class="row table-row-10">
                                            @if ($item?->status == 1)
                                                Active
                                            @else
                                                Deactive
                                            @endif
                                        </td>
                                        <td class="row table-row-10 textLeft">
                                            <span>{{ $item?->address_en ?? '' }}</span><br/>
                                            <span>{{ $item?->address_kh ?? '' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
