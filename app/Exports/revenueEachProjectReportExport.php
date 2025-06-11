<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;


\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});
class revenueEachProjectReportExport implements FromView, WithEvents, WithTitle
{
  public $data;

  public function __construct($data)
  {
    $this->data = $data;

  }
  public function view(): view
  {
    return view("admin::pages.report.revenue.excel.each-project", [
      'data' => $this->data,
    ]);
  }
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
        //print fit column
        $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
        $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
        // Zoom Scale
        $event->sheet->getDelegate()->getSheetView()->setZoomScale(100);
        // set widht column
        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 0) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(3.7);
          } elseif ($i == 1) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(12.81);
          } elseif ($i == 2) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(27.7);
          } elseif ($i == 3 || $i == 4 || $i == 5 || $i == 6 || $i == 7 || $i == 8 || $i == 9 || $i == 10 || $i == 11 || $i == 12 || $i == 13 || $i == 14) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(11.48);
          } else {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(12.7);
          }
        }

        //set hight
        for ($i = 1; $i <= 15; $i++) {
          if ($i >= 1 && $i <= 3) {
            $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(19.8);
          } elseif ($i >= 4 && $i <= 10) {
            $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(23.6);
          } else {
            $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(13.8);
          }
        }
        // set wrap text
        $event->sheet->getDelegate()->getStyle("B6")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("D4:O4")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("A10:O10")->getAlignment()->setWrapText(true);
        for ($i = 6; $i <= 10; $i++) {
          $event->sheet->getDelegate()->getStyle("P$i")->getAlignment()->setWrapText(true);
        }

        //set font size
  
        $event->sheet->styleCells("A1:P3", [
          'font' => array(
            'size' => 15,
          ),
        ]);

        $event->sheet->styleCells("A4:P15", [
          'font' => array(
            'size' => 10,
          ),
        ]);

        // set bold
        $event->sheet->styleCells("A2:P5", [
          'font' => array(
            'bold' => true,
          ),
        ]);
        $event->sheet->styleCells("A10:P10", [
          'font' => array(
            'bold' => true,
          ),
        ]);
        $event->sheet->styleCells("B13", [
          'font' => array(
            'bold' => true,
          ),
        ]);
        //set background color
        $event->sheet->getDelegate()->getStyle("A4:P5")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('BFBFBF');

        $event->sheet->getDelegate()->getStyle("A10:P10")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('BDB3ED');

        // set bolder 
        $event->sheet->getStyle("A4:P10")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        //set text color
        $event->sheet->getDelegate()->getStyle('B15')
          ->getFont()
          ->getColor()
          ->setARGB('FF0000');

        //formart date
  
        $event->sheet->getDelegate()->getStyle("D5:O5")->getNumberFormat()
          ->setFormatCode('mmm-yy');

        //calculate total money by month
        for ($j = 3; $j < count($columns) - 1; $j++) {

          $event->sheet->getDelegate()->getCell("$columns[$j]10")->setValue("=SUM($columns[$j]6:$columns[$j]9)");
          $event->sheet->getDelegate()->getStyle("$columns[$j]10")->getNumberFormat()
            ->setFormatCode('"$"* #,##0.00');

        }
        //caculate total year by category
        for ($i = 6; $i <= 9; $i++) {
          $event->sheet->getDelegate()->getCell("P$i")->setValue("=SUM(D$i:O$i)");
          $event->sheet->getDelegate()->getStyle("P$i")->getNumberFormat()
            ->setFormatCode('"$"* #,##0.00');
        }
        // calculate total year
        $event->sheet->getDelegate()->getCell("P10")->setValue("=SUM(P6:P9)");
        $event->sheet->getDelegate()->getStyle("P10")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');
  
        //set worksheet color
        $event->sheet->getDelegate()->getTabColor()->setRGB('A9D08E');
  
      }
    ];
  }
  public function title(): string
  {
    return $this->data['project']->name . ' Revenue' . ' Y' . $this->data['year']?$this->data['year']:now()->year ;
  }
}