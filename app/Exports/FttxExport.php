<?php

namespace App\Exports;

use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;

\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class FttxExport implements FromView, WithEvents
{

  public $data;
  public $firstDate;
  public $totalMonth;
  public function __construct($data)
  {
    $this->data = $data['data'];
    $this->firstDate = $data['firstDate'];
    $this->totalMonth = $data['totalMonth'];
  }

  public function view(): view
  {
    return view("admin::pages.fttx.fttx.excel", [
      'data' => $this->data,
      'firstDate' => $this->firstDate,
      'totalMonth' => $this->totalMonth,
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {

        $getDelegate = $event->sheet->getDelegate();

        //print fit column
        $getDelegate->getPageSetup()->setFitToWidth(1);
        $getDelegate->getPageSetup()->setFitToHeight(0);
        // Zoom Scale
        $getDelegate->getSheetView()->setZoomScale(100);

       

        $lastColumn = 56 + ($this->totalMonth * 13); //
      
        //set default font size
        $getDelegate->getStyle("A1:{$this->getColumnNameByIndex($lastColumn)}1")->getFont()->setSize(10);

        // set text wrap
        $getDelegate->getStyle("A1:{$this->getColumnNameByIndex($lastColumn)}5")->getAlignment()->setWrapText(true);

        $count =1 ;
        //set show hide column
        for ($i = 47; $i <=  $lastColumn-10; $i++) {
          if($count !=13){
            $getDelegate->getColumnDimension($this->getColumnNameByIndex($i))->setOutlineLevel(1)->setCollapsed(true);
            $getDelegate->getColumnDimension($this->getColumnNameByIndex($i))->setVisible(false);
          }else{
            $count = 1;
            continue;
          }
          $count ++;
        }
    
        // height header
        $getDelegate->getRowDimension('3')->setRowHeight(12);
        $getDelegate->getRowDimension('4')->setRowHeight(57);
        $getDelegate->getRowDimension('5')->setRowHeight(57);

        //set width column
        $getDelegate->getDefaultColumnDimension()->setWidth(14);
        $getDelegate->getColumnDimension('A')->setWidth(6);


        // set border

        $end_row = count($this->data) + 5;
        $event->sheet->getStyle("A3:{$this->getColumnNameByIndex(45)}$end_row")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        $event->sheet->getStyle("{$this->getColumnNameByIndex(47)}3:{$this->getColumnNameByIndex($lastColumn-10)}$end_row")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        $event->sheet->getStyle("{$this->getColumnNameByIndex($lastColumn-8)}3:{$this->getColumnNameByIndex($lastColumn)}$end_row")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);
      }

    ];
  }

  function getColumnNameByIndex($index)
  {
    $columnName = '';
    while ($index >= 0) {
      $columnName = chr($index % 26 + 65) . $columnName; 
      $index = floor($index / 26) - 1;
    }
    return $columnName;
  }
}
