<table>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="10" style="vertical-align:middle; text-align:center;"><b>សៀវភៅទិន្នានុប្បវត្តិលក់</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3"><b>(ខេមបូឌា) ហ្វឺប៊ើរ អុបទិច​ ខមញូនីខេសិន​ ណេតវើក</b></td>
        <td colspan="10" style="vertical-align:middle; text-align:center;">
            <label>
                <b>
                    ចាប់ពីថ្ងៃទី&nbsp;<span>{{ $date?->form_date_label?->day }}</span>
                    &nbsp;ខែ&nbsp;<span>{{ $date?->form_date_label?->month }}</span>
                    &nbsp;ឆ្នាំ&nbsp;<span>{{ $date?->form_date_label?->year }}</span>
                </b>
            </label>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>ផ្ទះលេខ ១៦៨ ផ្លូវ ១៩៤៦</td>
        <td></td>
        <td></td>
        <td colspan="10" style="vertical-align:middle; text-align:center;">
            <label>
                <b>
                    ដល់ថ្ងៃទី&nbsp;<span>{{ $date?->to_date_label?->day }}</span>
                    &nbsp;ខែ&nbsp;<span>{{ $date?->to_date_label?->month }}</span>
                    &nbsp;ឆ្នាំ&nbsp;<span>{{ $date?->to_date_label?->year }}</span>
                </b>
            </label>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>ភូមិ​ ទំនប់ សង្កាត់ ភ្នំពេញថ្មី ខណ្ឌសែនសុខ រាជធានី​ ភ្នំពេញ</td>
        <td></td>
        <td></td>
        <td colspan="10" style="vertical-align:middle; text-align:center;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>ទូរសព្ទ័​ ០២៣-៨៨៦៦០០</td>
        <td></td>
        <td></td>
        <td colspan="10" style="vertical-align:middle; text-align:center;"></td>
        <td colspan="7" style="vertical-align:middle;text-align:right;"><b>លេខអត្តសញ្ណាណកម្ម អតប :
                @if (count($projectInExport) > 0)
                    <?php $i = 0; ?>
                    @foreach ($projectInExport as $index => $item)
                        <span>{{ $item->vat_tin }}</span>
                        <?php $i++; ?>
                        @if (count($projectInExport) > $i)
                            <span>/</span>
                        @endif
                    @endforeach
                @endif
            </b>
        </td>
    </tr>
    <tr>
        <th colspan="6">វិក័្កយប័ត្រ</th>
        <th colspan="11">ការលក់</th>
        <th rowspan="3" colspan="2">សរុបថៃ្លលក់រួម</th>
        <th rowspan="3">អត្រាប្តូរប្រាក់</th>
    </tr>
    <tr>
        <th rowspan="2">ថៃ្ងខែ</th>
        <th rowspan="2">លេខវិក័្កយប័ត្រ</th>
        <th rowspan="2">អតិថិជន</th>
        <th rowspan="2">លេខអត្តសញ្ញាណកម្មសារពើពន្ធ​ TIN</th>
        <th rowspan="2">បរិយាយ</th>
        <th rowspan="2">បរិមាណ</th>
        <th colspan="2" rowspan="2">លក់មិន​​ជាប់អាករ</th>
        <th rowspan="2">នាំចេញ​​​ ឬ លក់ចេញក្រៅស្រុក</th>
        <th colspan="4">លក់អោយបុគ្គលជាប់អាករ</th>
        <th colspan="4">លក់អោយអ្នកប្រើប្រាស់</th>
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
        <th></th>
        <th></th>
        <th></th>
        <th>US</th>
        <th>KHR</th>
        <th>US</th>
        <th>US</th>
        <th>KHR</th>
        <th>US</th>
        <th>KHR</th>
        <th>US</th>
        <th>KHR</th>
        <th>US</th>
        <th>KHR</th>
        <th>US</th>
        <th>KHR</th>
        <th></th>
    </tr>
    @foreach ($invoice as $item)
        <tr>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item?->issue_date ? date('d-m-Y', strtotime($item->issue_date)) : '--' }}</td>
            <td style="vertical-align:middle; text-align:center;">
                {{ $item->type_invoice == 'invoice' ? $item->invoice_number : $item?->credit_note_number }}</td>
            <td style="vertical-align:middle; text-align:left;">
                {{ $item?->data_customer ? json_decode($item->data_customer)->name_en : $item?->customer?->name_en }}
            </td>
            <td style="vertical-align:middle; text-align:left;">
                @if ($item->customer_vat_tin)
                    {{ $item?->data_customer ? json_decode($item->data_customer)->vat_tin : $item?->customer?->vat_tin }}
                @endif
            </td>
            <td>
                @if ($item->ftth_invoice == 'invoice')  
                    @if ($item->type_invoice == 'invoice')
                        @foreach ($item->invoiceDetail as $value)
                            {{ $value->des ? $value->des : '--' }} <br>
                        @endforeach
                    @else
                        @foreach ($item->creditNoteDetail as $value)
                            {{ $value->des ? $value->des : '--' }} <br>
                        @endforeach
                    @endif
                @else
                    @if ($item->type_invoice == 'invoice')
                        @foreach ($item->invoiceDetail as $value)
                            {{ $value->des ? $value->des : '--' }} <br>
                        @endforeach
                    @else
                        @foreach ($item->creditNoteDetails as $value)
                            {{ $value->des ? $value->des : '--' }} <br>
                        @endforeach
                    @endif
                @endif
            </td>
            <td style="vertical-align:middle; text-align:center;">{{ $item->total_qty ? $item->total_qty : '--' }}</td>
            <td>{{ $item->location_display=='sale_not_tax' ? $item?->total_price : '' }}</td>
            <td>{{ $item->location_display=='sale_not_tax'? $item?->total_price_kh : '' }}</td>
            <td></td>

            <td>{{ $item->location_display=='sale_tax' ? $item?->total_price : '' }}</td>
            <td>{{ $item->location_display=='sale_tax'? $item?->total_price_kh : '' }}</td>
            <td>{{ $item->location_display=='sale_tax' ? $item?->total_rate : '' }}</td>
            <td>{{ $item->location_display=='sale_tax'? $item?->total_rate_kh : '' }}</td>

            <td>{{ $item->location_display=='sale_user' ? $item?->total_price : '' }}</td>
            <td>{{ $item->location_display=='sale_user'? $item?->total_price_kh : '' }}</td>
            <td>{{ $item->location_display=='sale_user' ? $item?->total_rate : '' }}</td>
            <td>{{ $item->location_display=='sale_user'? $item?->total_rate_kh : '' }}</td>

            <td>{{ $item?->total_grand }}</td>
            <td>{{ $item?->total_grand_kh }}</td>

            <td style="vertical-align:middle; text-align:center;">{{ $item?->exchange_rate }}</td>
        </tr>
    @endforeach
    <tr>
        <th colspan="6" style="vertical-align:middle;text-align:right;"><b> សរុប : </b></th>
        <th><b>{{ number_format($totalSaleNotTax?->price_dollar, 2) }}</b></th>
        <th><b>{{ number_format($totalSaleNotTax?->price_khmer, 0) }}</b></th>
        <th><b></b></th>
        <th><b>{{ $totalSaleTax?->price_dollar }}</b></th>
        <th><b>{{ $totalSaleTax?->price_khmer }}</b></th>
        <th><b>{{ $totalSaleTax?->rate_dollar }}</b></th>
        <th><b>{{ $totalSaleTax?->rate_khmer }}</b></th>

        <th><b>{{ $totalSaleUser?->price_dollar }}</b></th>
        <th><b>{{ $totalSaleUser?->price_khmer }}</b></th>
        <th><b>{{ $totalSaleUser?->rate_dollar }}</b></th>
        <th><b>{{ $totalSaleUser?->rate_khmer }}</b></th>

        <th><b>{{ $total_grand?->price_dollar }}</b></th>
        <th><b>{{ $total_grand?->price_khmer }}</b></th>

        <th><b></b></th>
    </tr>

</table>
