<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<style>
    div tr{
        border: solid 2px blue;
    }
</style>
<table>
    <tr>
        <td colspan="8" style="font-family:'Khmer OS Battambang';text-align:center;font-weight:bold;font-size:13px;background-color:coral">(ខេមបូឌា) ហ្វីប៊ើរអុបទិច ខមញូនីខេសិន ណេតវើក</td>
    </tr>
    <tr>
        <td colspan="8" style="text-align:center;font-weigh:bold;font-size:10px;">(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK Co., Lp.</td>

    </tr>
    <tr>
        <td colspan="8" style="font-family:'Khmer OS Battambang';text-align:center;font-size:8px;">លេខអត្តសញ្ញាណកម្ម អតប(VATTIN) L001-901700661</td>

    </tr>
    <tr>
        <td colspan="8" style="font-family:'Khmer OS Battambang';text-align:center;font-weigh:bold;font-size:8px;">អាសយដ្ឋាន៖ ផ្ទះលេខ ១៦៨  ផ្លូវលេខ ១៩៤៦  ភូមិទំនប់  សង្កាត់ ភ្នំពេញថ្មី  ខណ្ឌ សែនសុខ  រាជធានីភ្នំពេញ</td>

    </tr>
    <tr>
        <td colspan="8" style="text-align:center;font-weigh:bold;font-size:7px;">Address: No.168, St.1946, Phum Tumnub, Sangkat Phnom Penh pmei, Khan Sen Sok, Phnom Penh, Cambodia.</td>

    </tr>
    <tr>
        <td colspan="8" style="font-family:'Khmer OS Battambang';text-align:center;font-weigh:bold;font-size:8px;">ទូរស័ព្ទលេខ (+៨៥៥) ០២៣ ៨៨៨ ០២២/​ ០៨៦​ ៨២២ ១៧៣</td>

    </tr>
    <tr>
        <td colspan="8" style="text-align:center;font-weigh:bold;font-size:7px;">HP: (+855)023 888 022/ 086 822 173     Fax: +855-23 886 600</td>

    </tr>
    <tr>
        <td colspan="8" style="font-family:'Khmer OS Battambang';text-align:center;font-weight:bold;font-size:10px;">ប័ណ្ណឥណទាន</td>

    </tr>
    <tr>
        <td colspan="8" style="text-align:center;font-weigh:bold;font-size:8px;">CREDIT NOTE</td>

    </tr>
    <tr style="border:1px solid black">
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">ឈ្មោះក្រុមហ៊ុន :​ <?php echo $invoice[0]->customer->name_kh; ?></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">លេខរៀងវិក្កយបត្រ/​ Invoice Nº​ :<?php echo $invoice[0]->invoice_number; ?> </td>
    </tr>
    <tr style="border:1px solid black">
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:left">Company name:<?php echo $invoice[0]->customer->name_en; ?></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">កាលបរិច្ឆេទ/ Date: <?php echo $invoice[0]->created_at->format('M-j-y'); ?> </td>
    </tr>
    <tr style="border:1px solid black">
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">អាស័យដ្ឋាន:  <?php echo $invoice[0]->customer->address_kh; ?></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">រយៈកាលបរិច្ឆេទ/ Invoice Period: <?php echo isset($invoice[0]->Invoice_Period) ? $invoice[0]->Invoice_Period : '' ; ?> </td>
    </tr>
    <tr style="border:1px solid black">
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:left">Address: <?php echo $invoice[0]->customer->address_en; ?></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">លេខកិច្ចសន្យា/ Contract No. : <?php echo isset($invoice[0]->Contract_No) ? $invoice[0]->Contract_No : '' ; ?> </td>
    </tr>
    <tr style="border:1px solid black">
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">ទូរស័ព្ទលេខ/ Telephone Nº : <?php echo isset($invoice[0]->customer->phone) ? $invoice[0]->customer->phone :''; ?></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:left">P.O. Nº LO58 <?php echo $invoice[0]->invoice_number; ?> </td>
    </tr>
    <tr style="border:1px solid black">
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">អ្នកទទួល/ Attention: <?php echo isset($invoice[0]->recipient) ? $invoice[0]->recipient : 'No data' ; ?></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:left">Ref.INV 20-2350 <?php echo isset($invoice[0]->ref) ? $invoice[0]->ref : 'No data' ; ?> </td>
    </tr>
    <tr style="border:1px solid black">
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left">លេខអត្តសញ្ញាណកម្ម អតប(VATTIN) :  <?php echo isset($invoice[0]->customer->vat_tin) ? $invoice[0]->customer->vat_tin : 'No data' ; ?></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left"></td>
    </tr>


    <tr>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;text-align:center;background-color:#C0C0C0">ល.រ</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;text-align:center;background-color:#C0C0C0">ប្រភេទ</td>
        <td style="width:300px;text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;text-align:center ;background-color:#C0C0C0">បរិយាយមុខទំនិញ</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;text-align:center ;background-color:#C0C0C0">បរិមាណ</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;text-align:center ;background-color:#C0C0C0">ឯកតា</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;text-align:center ;background-color:#C0C0C0">ថ្លៃឯកតា</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;text-align:center ;background-color:#C0C0C0">អត្រាប្រចាំឆ្នាំ</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:none;border-right:1px solid black;text-align:center ;background-color:#C0C0C0">ថ្លៃទំនិញ</td>
    </tr>
    <tr>

        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;text-align:center; background-color:#C0C0C0">Nº</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;text-align:center ;background-color:#C0C0C0">Item</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;text-align:center ;background-color:#C0C0C0">Description</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;text-align:center ;background-color:#C0C0C0">Quantity</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;text-align:center ;background-color:#C0C0C0">UOM</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;text-align:center ;background-color:#C0C0C0">Unit price</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;text-align:center ;background-color:#C0C0C0">Annual Rate</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border-top:none;border-right:1px solid black;text-align:center ;background-color:#C0C0C0">Amount</td>
    </tr>
    <tr>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;">Underground Project</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
    </tr> 

{{-- Item --}}
    <tr>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center;"> 
        <?php
            $i = 0;
            $i++;
            echo $i;         
        ?>
        </td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:left;"><?php echo isset($invoice[0]->item) ? $invoice[0]->item : 'No data' ; ?></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;"><?php echo isset($invoice[0]->description ) ? $invoice[0]->description  : 'No data' ; ?></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;"><?php echo isset($invoice[0]->quatity ) ? $invoice[0]->quatity  : 'No data' ; ?></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;">Km</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;">$ <?php echo isset($invoice[0]->price) ? $invoice[0]->price : 'No data' ; ?></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;"><?php echo isset($invoice[0]->rate ) ? $invoice[0]->rate  : 'No data' ; ?></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;">$ <?php echo isset($invoice[0]->totalAmount) ? $invoice[0]->totalAmount : 'No data' ; ?></td>
    </tr> 

    <tr>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
    </tr> 
    <tr>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
    </tr> 
    <tr>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
    </tr> 
    <tr>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
    </tr> 

{{-- End Item --}}

    <tr>
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left;">Remak: </td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td colspan="2" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:right;">សរុប Sub Total</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;">$ <?php echo isset($invoice[0]->totalAmount) ? $invoice[0]->totalAmount : 'No data' ; ?> </td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;">រៀល
            <?php 

                $a = $invoice[0]->totalAmount;
                //=========
                $b = 0.10;
                $c = $invoice[0]->totalAmount;
                $d =  $b*$c;
                $result = $a+$d;
                echo number_format($result)*4092/1.1;

            ?>
        </td>
    </tr> 
    <tr>
        <td colspan="3" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left;"> </td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;"></td>
        <td colspan="2" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:right;">អាករលើតម្លៃបន្ថែម១០% VAT10%</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:center ;">$ 
            <?php 
                $a = 0.10;
                $b = $invoice[0]->totalAmount;
                $c = $a * $b;
                echo  number_format($c) ;
            ?>
        </td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center;font-family:'Khmer OS Battambang'">រៀល​​ 
            <?php 

                $a = $invoice[0]->totalAmount;
                //=========
                $b = 0.10;
                $c = $invoice[0]->totalAmount;
                $d =  $b*$c;
                $result = $a+$d*4092/1.1;
              
                $x = $invoice[0]->totalAmount;
                    //=========
                $y = 0.10;
                $z = $invoice[0]->totalAmount;
                $n =  $y* $z;
                $show = $y+$n;
                $total =  $result-$show;
                echo number_format($total);


            ?>
        </td>
    </tr> 
    <tr>
        <td colspan="4" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:left;"> </td>
        <td colspan="2" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:1px solid black;text-align:right;">សរុបរួម
            Grand Total</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center ;"> $  
            <?php 
                $a = $invoice[0]->totalAmount;
                //=========
                $b = 0.10;
                $c = $invoice[0]->totalAmount;
                $d =  $b* $c;
                $result = $a+$d;
                echo number_format($result);
            ?> 
        </td>
        <td style="width:100px;text-align:center;font-weigh:bold;font-size:8px;border:1px solid black;text-align:center;font-family:'Khmer OS Battambang'">រៀល​ 
            <?php

                $a = $invoice[0]->totalAmount;
                //=========
                $b = 0.10;
                $c = $invoice[0]->totalAmount;
                $d =  $b* $c;
                $result = $a+$d;
                echo number_format($result)*4092;
            ?>         
        </td>
    </tr> 

    <tr>
        <td colspan="4" style="text-align:center;font-weigh:bold;font-size:8px;border:none;text-align:left;">Payment Instruction</td>
        <td  style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
    </tr> 
    <tr>
        <td colspan="4" style="text-align:center;font-weigh:bold;font-size:8px;border:none;text-align:left;">Please kindly remit payment to:</td>
        <td  style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
    </tr> 
    <tr>
        <td colspan="4" style="text-align:center;font-weigh:bold;font-size:8px;border:none;text-align:left;">(CAMBODIA) FIBER OPTIC COMMUNICATION NETWORK CO., LTD.</td>
        <td  style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
    </tr> 
    <tr>
        <td colspan="4" style="text-align:center;font-weigh:bold;font-size:8px;border:none;text-align:left;">CANADIA BANK PLC.   A/C NO. 001-0000117418</td>
        <td  style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;"></td>
    </tr> 

    <tr>
        <td colspan="2" style="height:100px; text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border-bottom:2px solid black;text-align:left;"></td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;border-bottom:2px solid black"></td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:left;border-bottom:2px solid black"></td>
    </tr> 

    <tr>
        <td colspan="2"  style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:center;">ហត្ថលេខា និង ឈ្មោះអ្នកទិញ</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:center;">​​ត្រួតពិនិត្យដោយ</td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;font-family:'Khmer OS Battambang';border:none;text-align:center;"> ហត្ថលេខា​ និងឈ្មោះអ្នកលក់</td>
    </tr> 

    <tr>
        <td colspan="2"  style="text-align:center;font-weigh:bold;font-size:8px;border:none;text-align:center;">Customer's Signature and name</td>
        <td style="text-align:center;font-weigh:bold;font-size:8px;border:none;text-align:center;">Approved by</td>
        <td colspan="5" style="text-align:center;font-weigh:bold;font-size:8px;border:none;text-align:center;">Seller's Signature and Name</td>
    </tr> 

</table>

