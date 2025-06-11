<table>
    <thead>
        <tr>
            <th colspan=8>(ខេមបូឌា) ហ្វីប៊ើអុបទិច​ខមញូនិខេសិន ណេតវើក </th>
        </tr>
        <tr>
            <th colspan=8>(CAMBODIA) FIBER OPTIC CAMMUNICATION NETWORK Co.,ltd.</th>
        </tr>
        <tr>
            <td colspan=8>លេខអត្តសញ្ញាណកម្ម អតប(VATIN)L001-901700659</td>
        </tr>
        <tr>
            <td colspan=8>{{ $contact->address }}</td>
        </tr>
        <tr>
            <td colspan=8>Address :No.168, St.1946, Phum Tumnub, Sangkat Phnom Penh Thmei, Khan Sen Sok, Phnom Penh ,
                Cambodia</td>
        </tr>
        <tr>
            <td colspan=8>ទូរស័ព្ទលេខ (+៨៥៥) ០២៣ ៨៨៨ ០២២/​ ០៨៦​ ៨២២ ១៧៣</td>
        </tr>
        <tr>
            <td colspan=8>HP: (+855)023 888 022/ 086 822 173 &nbsp;&nbsp;&nbsp; Fax: +855-23 886 600</td>
        </tr>
        <tr>
            <th colspan=8>ប័ណ្ណឥណទាន</th>
        </tr>
        <tr>
            <th colspan=8>CREDIT NOTE</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3">
                <span>ឈ្មោះក្រុមហ៊ុន : {{ $customer->name_en ? $customer->name_en : '--' }}</span>
            </td>
            <td colspan="5">
                <span>លេខរៀងវិក្កយបត្រ/​ Invoice Nº​ : XLCR22-0006</span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span>Company name : {{ $customer->name_kh ? $customer->name_kh : '--' }}</span>
            </td>
            <td colspan="5">
                <span>កាលបរិច្ឆេទ/ Date : </span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span>អាស័យដ្ឋាន : {{ $customer->address_kh ? $customer->address_kh : '--' }}</span>
            </td>
            <td colspan="5">
                <span>រយៈកាលបរិច្ឆេទ/ Invoice Period:<span id="exDate"></span> </span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span>Address: {{ $customer->address_en ? $customer->address_en : '--' }}</span>
            </td>
            <td colspan="5">
                <span>លេខកិច្ចសន្យា/ Contract No.: </span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span>ទូរសព្ទ័លេខ/Telephone : {{ $customer->phone ? $customer->phone : '--' }}</span>
            </td>
            <td colspan="5">
                <span><b>P.O. Nº {{ $purchase->po_number }}</b></span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span>អ្នកទទួល / Attention : </span>
            </td>
            <td colspan="5">
                <span><b>Ref.INV 20-2350</b> </span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span>លេខអត្តសញ្ញាណកម្ម អតប(VATIN) : <span></span></span>
            </td>
            <td colspan="5">
            </td>
        </tr>
        <tr>
            <th>ល.រ</th>
            <th style="width: 280px">ប្រភេទ</th>
            <th style="width: 300px"> បរិយាយមុខទំនិញ</th>
            <th>បរិមាណ </th>
            <th>ឯកតា</th>
            <th>ថ្លៃឯកតា</th>
            <th>អត្រាប្រចាំឆ្នាំ</th>
            <th>ថ្លៃទំនិញ</th>

        </tr>
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Descriptiont</th>
            <th>Quality</th>
            <th>UOM</th>
            <th>Unit Price</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><b>{{ $purchase->project?$purchase->project->name:"--" }}</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @foreach ($purchase_detail as $k => $item)
        <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $item->service ? $item->service->name :'--'}}</td>
                <td>{{ $item->des ? $item->des:'' }}</td>
                <td>{{ $item->qty ? $item->qty:'' }}</td>
                <td>$ {{ $item->price ? number_format($item->price , 2):'--' }}</td>
                <td>{{ $item->uom ? $item->uom:'' }}</td>
                <td>R {{ $item->rate? number_format($item->rate, 2):'' }}</td>
                <td>$ {{ $item->amount ? number_format( $item->amount,2):'--' }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3" rowspan="6" style="font-family:Khmer OS Battambang;font-size: 9px;">
                <span>Remak: </span> <br>
                <span>Note: Ref.NBC Exchang 1/26/2023 1 USD=4092  Riel </span> <br>
                <span>មួយរយម្ភៃប្រាំ និងសែសិបសេន ដុល្លារអាមេរិក / USD​ One Hundred Twenty Five and Cents FortyOnly</span><br>
            </td>
            <td>សរុប</td>
            <td colspan="2" rowspan="2">$ {{ $purchase->total_price ? number_format( $purchase->total_price, 2):'--' }}</td>
            <td colspan="2" rowspan="2">R {{ $sub_total_khmer ?  number_format($sub_total_khmer, 2):'--'}}</td>
        </tr>
        <tr>
            <td> Sub Total</td>
        </tr>
        <tr>
            <td>អាករលើតម្លៃបន្ថែម (10%)</td>
            <td colspan="2" rowspan="2">$ {{ $vat_dollar ? number_format( $vat_dollar, 2):'--' }}</td>
            <td colspan="2" rowspan="2">R {{ $vat_khmer ? number_format( $vat_khmer, 2):'--' }}</td>
        </tr>
        <tr>
            <td>VAT(10%)</td>
        </tr>
        <tr>
            <td>សរុបរួម</td>
            <td colspan="2" rowspan="2">$ {{ $grand_total_dollar ? number_format( $grand_total_dollar, 2):'--' }}</td>
            <td colspan="2" rowspan="2">R {{ $grand_total_khmer ? number_format( $grand_total_khmer, 2):'--' }}</td>
        </tr>
        <tr>
            <td>Grand Total</td>
        </tr>
        <tr>
            <td colspan="8">Payment Instruction</td>
        </tr>
        <tr>
            <td colspan="8">Please kindly remit payment to:</td>
        </tr>
        <tr>
            <td colspan="8">(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK CO., LTD.</td>
        </tr>
        <tr>
            <td colspan="8">CANADIA BANK PLC. A/C NO. 001-0000117418</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="border-bottom:2px solid black;"></td>
            <td></td>
            <td colspan="2" style="border-bottom:2px solid black;"></td>
            <td></td>
            <td colspan="2" style="border-bottom:2px solid black;"></td>
        </tr>
        <tr>
          <td colspan="2"><span>ហត្ថលេខា និង ឈ្មោះអ្នកទិញ</span></td>
          <td></td>
          <td colspan="2"><span>ត្រួតពិនិត្យដោយ</span></td>
          <td></td>
          <td colspan="2"><span>ហត្ថលេខា​ និងឈ្មោះអ្នកលក់</span></td>
        </tr>
        <tr>
            <td colspan="2"><span>Customer's Signature &amp; Name</span></td>
            <td></td>
            <td colspan="2"><span>Approved by</span></td>
            <td></td>
            <td colspan="2"><span>Seller's Signature &amp; Name</span></td>
          </tr>
    </tbody>
</table>
