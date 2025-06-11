<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class PurchaseExport implements FromView, ShouldAutoSize,WithEvents
{
    use Exportable;
    public $purchase;
    public $purchase_detail;
    public $customer;
    public $contact;
    public $sub_total_khmer;
    public $vat_dollar;
    public $vat_khmer;
    public $grand_total_dollar;
    public $grand_total_khmer;
   
   public function __construct($data){
     $this->purchase = $data['purchase'];
     $this->purchase_detail = $data['purchase_detail'];
     $this->customer = $data['customer'];
     $this->contact = $data['contact'];
     $this->sub_total_khmer = $data['sub_total_khmer'];
     $this->vat_dollar = $data['vat_dollar'];
     $this->vat_khmer = $data['vat_khmer'];
     $this->grand_total_dollar = $data['grand_total_dollar'];
     $this->grand_total_khmer = $data['grand_total_khmer'];
   }
 
   public function view():view
   {
      
       return view("admin::pages.purchase.excel-export",[
                   'purchase' =>  $this->purchase,
                   'purchase_detail' => $this->purchase_detail,
                   'customer'=>$this->customer ,
                   'contact'=> $this->contact,
                   'sub_total_khmer'=> $this->sub_total_khmer,
                   'vat_dollar'=>$this->vat_dollar,
                   'vat_khmer'=>$this->vat_khmer,
                   'grand_total_dollar'=>$this->grand_total_dollar,
                   'grand_total_khmer'=>$this->grand_total_khmer,
       ]);
   }
   public function registerEvents(): array{
     return [
       AfterSheet::class    => function (AfterSheet $event) {
         $startRows = 10;
         $countRows = $this->purchase_detail->count() + $startRows+15;
         $cellRange = 'A' . $startRows . ':H' . $startRows; // All headers
         $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
         $alignment =new  \PhpOffice\PhpSpreadsheet\Style\Alignment();
         $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setName('Khmer OS Battambang');
         $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setSize(9);
         $event->sheet->getStyle('A10')->getAlignment()->setWrapText(true);
         $event->sheet->getStyle('A11')->getAlignment()->setWrapText(true);
         
         for ($i = 1; $i <= 9; $i++) {
               $row="A$i:H$i";
               if($i<3 || ($i>7 && $i<10)){
                 $size=13;
                 if($i==8 || $i==9){
                   $size=10;
                 }
                 $event->sheet->styleCells("$row", [
                   'font' => array(
                     'bold' => true,
                     'size' => "$size"
             ),
 ]);	
               }
               else{
                 $size=9;
               }
               $event->sheet->getDelegate()->getStyle("$row")
               ->getAlignment()
               ->setHorizontal($alignment::HORIZONTAL_CENTER);
               $event->sheet->styleCells("$row", [
                 'font' => array(
                 'size'=> "$size"
           ),
 ]);	
       }
       $event->sheet->getDelegate()->getStyle('A17:H18')
       ->getFill()
       ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
       ->getStartColor()
       ->setARGB('c4d69b');
     $i=$countRows+5;
     for( $i;$i<=$countRows+8;$i++){
       $row="A$i:H$i";
       $event->sheet->getDelegate()->getStyle($row)
       ->getAlignment()
       ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
     }
            //border
            $a1 = [
             'borders' => [
                 'outline' => [
                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                 ]
             ]
         ];
         
 
         foreach ($columns as $column) {
           $event->sheet->getDelegate()->getStyle($column . $startRows . ':' . $column . $countRows)->applyFromArray($a1); //border column
           for ($i = $startRows; $i <= $countRows; $i++) {
               $event->sheet->getDelegate()->getStyle($column . $i)->applyFromArray($a1); //border row
               $event->sheet->getDelegate()->getStyle($column . $i)->applyFromArray($a1); //border row
           }
       }
       }
     ];
   }
 
}
