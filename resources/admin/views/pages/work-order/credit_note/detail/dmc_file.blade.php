<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice Credit Note</title>

    <style type="text/css">
        @font-face {
            font-family: 'battambang';
            src: url("https://fonts.googleapis.com/css2?family=Noto+Serif+Khmer&family=Roboto:wght@100&display=swap");
        }

        body {
            font-family: "battambang", sans-serif, sans-serif;
        }

        p {
            font-family: battambang, khmeros_muol, arial, sans-serif;
            width: 100%;
            color: black;
            line-height: 15px;
        }

        .head-content {
            text-align: center;
        }

        .head-content h2 {
            margin: 0;
        }

        .m-0 {
            margin: 0px;
        }

        .p-0 {
            padding: 0px;
        }

        .pt-5 {
            padding-top: 5px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .text-center {
            text-align: center !important;
        }

        .w-100 {
            width: 100%;
        }

        .w-40 {
            width: 40%;
        }

        .w-60 {
            width: 60%;
        }

        .w-85 {
            width: 85%;
        }

        .w-15 {
            width: 15%;
        }

        .size-15 {
            font-size: 15px;
        }

        .size-11 {
            font-size: 11px;
        }

        .font-bold {
            font-weight: bold;
        }

        .m-0 {
            margin: 0px;
        }

        .float-left {
            float: left;
        }

        .float-right {
            float: right;
        }

        .text-right {
            text-align: right;
        }

        .logo {
            margin-bottom: 35px;
        }

        .logo img {
            width: 100%;
            height: 100%;
        }

        hr {
            border: 0.5px solid #5D5D5D;
        }

        .gray-color {
            color: #5D5D5D;
        }

        .black-color {
            color: #000;
        }

        .text-bold {
            font-weight: bold;
        }

        .bold-700 {
            font-weight: 700;
        }

        .border {
            border: 1px solid black;
        }

        .container {
            padding: 0px -20px;
        }

        .table-header {
            line-height: 1.5;
        }

        .text-decoration {
            text-decoration: underline;
        }

        table {
            border-collapse: collapse;
        }

        table tr th {
            border: 1px solid #d2d2d2;
            font-size: 11px;
            padding: 2px;
            line-height: 14px;
        }

        table tr td {
            border: 1px solid #d2d2d2;
            border-collapse: collapse;
            padding: 2px;
            font-size: 11px;
            text-align: left;
            line-height: 14px;
        }

        table.space-between-table {
            width: 100%;
            border: none;
        }

        table.space-between-table tr,
        table.space-between-table tr td {
            border: none;
            padding: 0;
        }

        table.space-between-table tr td.text-left {
            text-align: left;
            padding: 0;
        }

        table.space-between-table tr td.text-right {
            text-align: right;
            padding: 0;
        }

        @page {
            margin: 1% 5%;
        }

        @media print {
            .flex {
                display: -webkit-box;
                /* wkhtmltopdf uses this one */
                display: -webkit-flex;
                display: flex;
                -webkit-box-pack: space-between;
                /* wkhtmltopdf uses this one */
                -webkit-justify-content: space-between;
                justify-content: space-between;
            }
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="table">
            <div class="table-header">
                <div class="logo" style="height: 100px;width: 100%;">
                    <img width="100%" src="{{ public_path('images/Capture.png') }}" alt=""
                        style="height: 100px;">
                </div>
                <div class="size-15 m-0 font-bold" align="center">(ខេមបូឌា) ហ្វីប៊ើរអុបទិច ខមញូនីខេសិន ណេតវើក</div>
                <div class="size-15 m-0 font-bold" align="center">(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK Co., Ltd.
                </div>
                <p class="size-11 m-0" align="center">លេខអត្តសញ្ញាណកម្ម អតប
                    (VATIN)&nbsp;:&nbsp;{{ $data?->order?->project?->vat_tin ?? '' }}</p>
                <p class="size-11 m-0" align="center">អាសយដ្ឋាន៖ ផ្ទះលេខ ១៦៨ ផ្លូវលេខ ១៩៤៦ ភូមិទំនប់ សង្កាត់ ភ្នំពេញថ្មី
                    ខណ្ឌ សែនសុខ រាជធានីភ្នំពេញ</p>
                <p class="size-11 m-0" align="center">ទូរស័ព្ទលេខ (+៨៥៥) ០២៣ ៨៨៨ ០២២/ ០៨៦​​​ ៨២២​​​​ ១៧៣</p>
                <p class="size-11 m-0" align="center">HP: (+855)023 888 022/ 086 822 173&nbsp;&nbsp;&nbsp; Fax: +855-23
                    886 600</p>
                <p class="size-11 m-0" align="center">ទូរស័ព្ទលេខ (+៨៥៥) ០២៣ ៨៨៨ ០២២/ ០៨៦​​​ ៨២២​​​​ ១៧៣</p>
                <p class="size-11 m-0 font-bold" align="center" style="margin-top: 15px;">ប័ណ្ណឥណទាន</p>
                <p class="size-11 m-0 font-bold" align="center">CREDIT NOTE</p>
            </div>
            <div class="table-body">
                <table class="w-100">
                    <thead>
                        <tr>
                            <th class="w-50" colspan="3" align="left" style="font-weight: unset">
                                ឈ្មោះក្រុមហ៊ុន&nbsp;:&nbsp;{{ $data?->dataCustomer?->name_kh ?? '' }}
                            </th>
                            <th class="w-50" colspan="4" align="left" style="font-weight: unset">
                                លេខរៀងវិក្កយបត្រ/​ Invoice&nbsp;:&nbsp;{{ $data?->credit_note_number ?? '' }}
                            </th>
                        </tr>
                        <tr>
                            <th class="w-50" colspan="3" align="left" style="font-weight: unset">
                                Company name&nbsp;:&nbsp;{{ $data?->dataCustomer?->name_en ?? '' }}
                            </th>
                            <th class="w-50" colspan="4" align="left" style="font-weight: unset">
                                កាលបរិច្ឆេទ/ Date&nbsp;:&nbsp;{{ formatDate($data?->issue_date, 'DD MMM Y') }}
                            </th>
                        </tr>

                        <tr>
                            <th class="w-50" colspan="3" align="left" style="font-weight: unset">
                                អាស័យដ្ឋាន&nbsp;:&nbsp;{{ $data?->dataCustomer?->address_kh ?? '' }}</th>
                            <th class="w-50" colspan="4" align="left" style="font-weight: unset">
                                រយៈកាលបរិច្ឆេទ/ Invoice
                                Period&nbsp;:&nbsp;{{ formatDate($data?->period_start, 'DD MMM Y') }}{!! $data?->period_end ? '&nbsp;-&nbsp;' . formatDate($data?->period_end, 'DD MMM Y') : '' !!}
                            </th>
                        </tr>
                        <tr>
                            <th class="w-50" colspan="3" align="left" style="font-weight: unset">
                                Address&nbsp;:&nbsp;{{ $data?->dataCustomer?->address_en ?? '' }}
                            </th>
                            <th class="w-50" colspan="4" align="left" style="font-weight: unset">លេខកិច្ចសន្យា/
                                Contract No. &nbsp;:&nbsp; {{ $data?->order?->contract_number ?? '' }}
                            </th>
                        </tr>
                        <tr>
                            <th class="w-50" colspan="3" align="left" style="font-weight: unset">
                                ទូរសព្ទ័លេខ/Telephone&nbsp;:&nbsp;{{ $data?->dataCustomer?->phone ?? '' }}
                            </th>
                            <th class="w-50" colspan="4" align="left" style="font-weight: unset">
                                Ref&nbsp;:&nbsp;{{ $data?->invoice_number ?? '' }}
                            </th>
                        </tr>
                        <tr>
                            <th class="w-50" colspan="3" align="left" style="font-weight: unset">អ្នកទទួល /
                                Attention :
                            </th>
                            <th class="w-50" colspan="4" align="left" style="font-weight: unset"></th>
                        </tr>
                        <tr>
                            <th class="w-50" colspan="3" align="left" style="font-weight: unset">
                                លេខអត្តសញ្ញាណកម្ម អតប (VATIN)&nbsp;:&nbsp;{{ $data?->dataCustomer?->vat_tin ?? '' }}
                            </th>
                            <th class="w-50" colspan="4" align="left" style="font-weight: unset"></th>
                        </tr>

                        <tr style="background: #c4d69b">
                            <th class="font-bold">
                                ល.រ<br>No
                            </th>
                            <th class="font-bold" style="width: 15%">
                                ប្រភេទ<br>Item
                            </th>
                            <th class="font-bold" style="width: 30%">
                                បរិយាយាយមុខទំនិញ<br>Description
                            </th>
                            <th class="font-bold">
                                បរិមាណ<br>Quality
                            </th>
                            <th class="font-bold">
                                ឯកតា<br>UOM
                            </th>
                            <th class="font-bold">
                                ថ្លៃឯកតា<br>Unit Price($)
                            </th>
                            <th class="font-bold">
                                ថ្លៃទំនិញ($)<br>Amount($)
                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="font-bold">{{ $data?->order?->project?->name ?? '' }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- list --}}
                        @foreach ($data->creditNoteDetails as $index => $item)
                            <tr>
                                <td style="text-align: center">{{ $index + 1 }}</td>
                                <td style="text-align: center">{{ $item?->service?->name ?? '' }}</td>
                                <td>{{ $item?->des ?? '' }}</td>
                                <td style="text-align: center">{{ $item?->qty ?? '' }}</td>
                                <td style="text-align: center">{{ $item?->uom ?? '' }}</td>
                                <td style="text-align: center;">
                                    <table class="space-between-table">
                                        <tr>
                                            <td class="text-left">$</td>
                                            <td class="text-right">
                                                {{ $item?->price ? number_format((float) $item->price, 2) : '' }}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table class="space-between-table">
                                        <tr>
                                            <td class="text-left">$</td>
                                            <td class="text-right">
                                                {{ $item?->amount ? number_format((float) $item->amount, 2) : '' }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endforeach

                        {{-- footer --}}
                        <tr>
                            <td colspan="3" rowspan="3" class="w-50">
                                Remark&nbsp;:&nbsp;{{ $data?->remark ?? '' }}
                                <br><br>
                                Note&nbsp;:&nbsp;Ref.NBC Exchang Rate
                                On&nbsp;{{ formatDate($data?->issue_date, 'DD MMM Y') }}&nbsp;1&nbsp;USD&nbsp;=&nbsp;{{ $data?->exchange_rate }}&nbsp;Riel<br><br>{{ $data?->note ?? '' }}
                            </td>
                            <td colspan="2" style="text-align: right; font-size: 11px">
                                សរុប<br>Sub total
                            </td>
                            <td>
                                <table class="space-between-table">
                                    <tr>
                                        <td class="text-left">$</td>
                                        <td class="text-right">
                                            {{ $data?->total_price ? number_format((float) $data->total_price, 2) : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: center;">
                                <table class="space-between-table">
                                    <tr>
                                        <td class="text-left">R</td>
                                        <td class="text-right">
                                            {{ $data?->total_price_kh ? number_format((float) $data->total_price_kh) : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right; font-size: 11px">
                                អាករលើតម្លៃបន្ថែម១០%<br>VAT10%
                            </td>
                            <td style="text-align: center;">
                                <table class="space-between-table">
                                    <tr>
                                        <td class="text-left">$</td>
                                        <td class="text-right">
                                            {{ $data?->vat ? number_format((float) $data->vat, 2) : '' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: center;">
                                <table class="space-between-table">
                                    <tr>
                                        <td class="text-left">R</td>
                                        <td class="text-right">
                                            {{ $data?->vat_kh ? number_format((float) $data->vat_kh) : '' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right; font-size: 11px">
                                សរុបរួម<br>Grand Total
                            </td>
                            <td style="text-align: center;">
                                <table class="space-between-table">
                                    <tr>
                                        <td class="text-left">$</td>
                                        <td class="text-right">
                                            {{ $data?->total_grand ? number_format((float) $data->total_grand, 2) : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: center;">
                                <table class="space-between-table">
                                    <tr>
                                        <td class="text-left">R</td>
                                        <td class="text-right">
                                            {{ $data?->total_grand_kh ? number_format((float) $data->total_grand_kh) : '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="line-height: 1.5;">
                    <p class="size-11 m-0">Payment Instruction</p>
                    <p class="size-11 m-0">Please kindly remit payment to:</p>
                    <p class="size-11 m-0">(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK CO., LTD.</p>
                    @if (count($bankAccounts) > 0)
                        @foreach ($bankAccounts as $bankAccount)
                            <p class="size-11 m-0">{{ $bankAccount->bank_name }} NO.
                                {{ $bankAccount->account_number }}</p>
                        @endforeach
                    @endif
                </div>
                <table class="w-100" style="margin-top:40px;">
                    <tbody>
                        <tr>
                            <td style="width: 33%; border: none; text-align: center;padding: 0px 35px 0 0;">
                                <hr>
                                ហត្ថលេខា និង ឈ្មោះអ្នកទិញ
                                <br>
                                Customer's Signature & Name
                            </td>
                            <td style="width: 44%; border: none; text-align: center;padding: 0px 35px 0 0;">
                                <hr>
                                ត្រួតពិនិត្យដោយ
                                <br>
                                Approved By
                            </td>
                            <td style="width: 33%; border: none; text-align: center;">
                                <hr>
                                ហត្ថលេខា និង ឈ្មោះអ្នកលក់
                                <br>
                                Seller's Signature & Name
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
