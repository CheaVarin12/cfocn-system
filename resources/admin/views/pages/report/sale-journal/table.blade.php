<div class="tableLayoutCon {{ count($data) <= 0 ? 'heightAuthReport' : '' }}">
    <div class="tableLy">
        <div class="out_table_css">
            <div class="excel-content">
                <div class="excel-wrapper">
                    <div class="reportTitle">
                        <h3>របាយការណ៍ទិនានុប្បវត្តិលក់</h3>
                        <span>REPORT Sale Journal</span>
                        <label>
                            ចាប់ពីថ្ងៃទី&nbsp;<span x-text="from_date_label?.day"></span>
                            &nbsp;ខែ&nbsp;<span x-text="from_date_label?.month"></span>
                            &nbsp;ឆ្នាំ&nbsp;<span x-text="from_date_label?.year"></span>
                        </label>
                        <label>
                            ដល់ថ្ងៃទី&nbsp;<span x-text="to_date_label?.day"></span>
                            &nbsp;ខែ&nbsp;<span x-text="to_date_label?.month"></span>
                            &nbsp;ឆ្នាំ&nbsp;<span x-text="to_date_label?.year"></span>
                        </label>
                    </div>
                    @if (count($data) > 0)
                        <div class="excel-header">
                            <div class="excel-header-left">
                                <div class="excel-header-left-wrapper">
                                    <h3>(ខេមបូឌា) ហ្វឺប៊ើរ អុបទិច ខមញូនីខេសិន ណេតវើក</h3>
                                    <p>ផ្ទះលេខ ១៦៨ ផ្លូវ ១៩៤៦ ភូមិ ទំនប់ សង្កាត់ ភ្នំពេញថ្មី ខណ្ឌសែនសុខ រាជធានី ភ្នំពេញ
                                    </p>
                                    <p>ទូរស័ព្ទ ០២៣-៨៨៦៦០០</p>
                                </div>
                            </div>
                            <div class="excel-header-right">
                                <div class="excel-header-right-wrapper">
                                    @if (count($projectInExport) > 0)
                                        <p class="fontWeight">លេខអត្តសញ្ណាណកម្ម អតប :
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
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tableCustomScroll">
            <div class="table excel">
                @if (count($data) > 0)
                    <div class="excel-body">
                        <table class="tableWidth" id="THEAD">
                            <thead class="column">
                                <tr>
                                    <th class="row table-row-10 text-center" colspan="2"></th>
                                    <th class="row table-row-20 text-center" colspan="3">វិក័្កយប័ត្រ</th>
                                    <th class="row table-row-50 text-center" colspan="11">ការលក់</th>
                                    <th class="row table-row-10 text-center" rowspan="3" colspan="2">
                                        សរុបថ្លៃលក់រួម
                                    </th>
                                    <th class="row table-row-10 text-center" rowspan="3">អត្រាប្តូរប្រាក់</th>
                                </tr>
                                <tr>
                                    <th rowspan="2" class="row table-row-5 text-center">ថ្ងៃខែ</th>
                                    <th rowspan="2" class="row table-row-5 text-center">លេខវិក្ក័យបត្រ</th>
                                    <th rowspan="2" class="row table-row-10 text-center">អតិថិជន</th>
                                    <th rowspan="2" class="row table-row-5 text-center">លេខអត្តសញ្ញាណកម្មអតិថិជន</th>
                                    <th rowspan="2" class="row table-row-5 text-center">បរិមាណ</th>
                                    <th colspan="2" rowspan="2" class="row table-row-15 text-center">
                                        លក់មិនជាប់អាករ
                                    </th>
                                    <th rowspan="2" rowspan="2" class="row table-row-15 text-center">នាំចេញ ឬ
                                        លក់ចេញក្រៅស្រុក</th>
                                    <th colspan="4" class="row table-row-15 text-center">លក់អោយបុគ្គលជាប់អាករ</th>
                                    <th colspan="4" class="row table-row-15 text-center">លក់អោយអ្នកប្រើប្រាស់</th>
                                </tr>
                                <tr>
                                    <th colspan="2">តំលៃជាប់អាករ</th>
                                    <th colspan="2">អាករ</th>
                                    <th colspan="2">តំលៃជាប់អាករ</th>
                                    <th colspan="2">អាករ</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    {{-- <th></th> --}}
                                    <th></th>
                                    <th></th>
                                    {{-- a --}}
                                    <th>US</th>
                                    <th>KHR</th>
                                    <th>US</th>
                                    {{-- a --}}
                                    {{-- b --}}
                                    <th>US</th>
                                    <th>KHR</th>
                                    <th>US</th>
                                    <th>KHR</th>
                                    {{-- b --}}
                                    {{-- c --}}
                                    <th>US</th>
                                    <th>KHR</th>
                                    <th>US</th>
                                    <th>KHR</th>
                                    {{-- c --}}

                                    <th>US</th>
                                    <th>KHR</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $item)
                                    <tr @click="detailDialog({{ $item }})"
                                        class="{{ $item->type_invoice == 'credit_note' ? 'bgRed' : '' }}">
                                        <td>{{ $item->issue_date ? date('d-m-Y', strtotime($item->issue_date)) : '--' }}
                                        </td>
                                        <td>{{ $item->type_invoice == 'credit_note' ? $item->credit_note_number : $item?->invoice_number }}</td>
                                        <td class="textLeft">
                                            {!! $item?->data_customer ? json_decode($item->data_customer)->name_en : $item?->customer?->name_en !!}
                                        </td>
                                        <td>
                                            @if ($item->customer_vat_tin)
                                                {!! $item?->data_customer ? json_decode($item->data_customer)->vat_tin : $item?->customer?->vat_tin !!}
                                            @endif
                                        </td>
                                        <td>{{ $item->total_qty ? $item->total_qty : '' }}</td>
                                        <td>
                                            @if ($item->location_display=='sale_not_tax')
                                            <div>
                                                <span>$&nbsp;</span>
                                                <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format($item->total_price, 2) !!}</span>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->location_display=='sale_not_tax')
                                            <div>
                                                <span>R&nbsp;</span>
                                                <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format(round($item->total_price_kh)) !!}</span>
                                            </div>
                                        @endif
                                        </td>
                                        <td></td>

                                        {{-- oneDerf --}}
                                        <td>
                                            @if ($item->location_display=='sale_tax')
                                                <div>
                                                    <span>$&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format($item->total_price, 2) !!}</span>
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($item->location_display=='sale_tax')
                                                <div>
                                                    <span>R&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format(round($item->total_price_kh)) !!}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->location_display=='sale_tax')
                                                <div>
                                                    <span>$&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format($item->total_rate, 2) !!}</span>
                                                </div>
                                            @endif
                                        <td>
                                            @if ($item->location_display=='sale_tax')
                                                <div>
                                                    <span>R&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format(round($item->total_rate_kh)) !!}</span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- twoDerf --}}
                                        <td>
                                            @if ($item->location_display=='sale_user')
                                                <div>
                                                    <span>$&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format($item->total_price, 2) !!}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->location_display=='sale_user')
                                                <div>
                                                    <span>R&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format(round($item->total_price_kh)) !!}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->location_display=='sale_user')
                                                <div>
                                                    <span>$&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format($item->total_rate, 2) !!}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->location_display=='sale_user')
                                                <div>
                                                    <span>R&nbsp;</span>
                                                    <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format(round($item->total_rate_kh)) !!}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <span> $&nbsp;</span>
                                                <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{!! number_format($item->total_grand, 2) !!}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span>R&nbsp;</span>
                                                <span>{{ $item->type_invoice == 'credit_note' ? '-' : '' }}{{ number_format(round($item->total_grand_kh)) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span>R&nbsp;</span>
                                                <span>{{ number_format(round($item->exchange_rate)) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="column" style=" position: sticky;inset-inline-start: 0;">
                                <tr>
                                    <td colspan="5" class="textRight">Total&nbsp;:</td>
                                    <td class="textLeft">
                                        <span>$&nbsp;</span>
                                        <span>{{ number_format($totalSaleNotTax->price_dollar, 2) }}</span>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>R&nbsp;</span>
                                            <span>{{ number_format(round($totalSaleNotTax->price_khmer)) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft"></td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalSaleTax->price_dollar, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>R&nbsp;</span>
                                            <span>{{ number_format(round($totalSaleTax->price_khmer)) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalSaleTax->rate_dollar, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>R&nbsp;</span>
                                            <span>{{ number_format(round($totalSaleTax->rate_khmer)) }}</span>
                                        </div>
                                    </td>

                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalSaleUser->price_dollar, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>R&nbsp;</span>
                                            <span>{{ number_format(round($totalSaleUser->price_khmer)) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($totalSaleUser->rate_dollar, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>R&nbsp;</span>
                                            <span>{{ number_format(round($totalSaleUser->rate_khmer)) }}</span>
                                        </div>
                                    </td>

                                    <td class="textLeft">
                                        <div>
                                            <span>$&nbsp;</span>
                                            <span>{{ number_format($total_grand->price_dollar, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft">
                                        <div>
                                            <span>R&nbsp;</span>
                                            <span>{{ number_format(round($total_grand->price_khmer)) }}</span>
                                        </div>
                                    </td>
                                    <td class="textLeft"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    @component('admin::components.emptyReport', [
                        'name' => 'Sale journal empty',
                        'msg' => 'There is no data.',
                        'style' => 'padding: 10px 0 80px 0;',
                    ])
                    @endcomponent
                @endif

            </div>
        </div>
    </div>
</div>
