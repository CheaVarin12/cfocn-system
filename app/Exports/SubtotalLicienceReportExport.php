<?php

namespace App\Exports;

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
class SubtotalLicienceReportExport implements FromView, WithEvents, WithTitle
{

  public $data;

  public function __construct($data)
  {
    $this->data = $data;
  }
  public function view(): view
  {

    return view("admin::pages.report.revenue.excel.subtotal-license", [
      'data' => $this->data,
    ]);
  }
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $columns = ['A', 'B', 'C', 'D', 'E'];
        //print fit column
        $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
        $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
        // Zoom Scale
        $event->sheet->getDelegate()->getSheetView()->setZoomScale(100);

        //set width column
        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 0 || $i == 1) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(23.81);
          } elseif ($i == 2) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(13.25);
          } elseif ($i == 3) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(24.25);
          } elseif ($i == 4) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(12.25);
          }
        }

        // set style head title
        $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(19.2);
        $event->sheet->styleCells("A1:E1", [
          'font' => array(
            'name' => 'MS Sans Serif',
            'size' => 15,
            'bold' => true,
          ),
        ]);
        $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(13.2);
        //worksheet color
        $event->sheet->getDelegate()->getTabColor()->setRGB('FF0000');

        //set style of each project
        $totalNumber=[];
        $totalAmount = [];
        $totalLicenseFee = [];
        $check = 0;
        $start = 3;
        for ($i = 1; $i <= count($this->data['data']); $i++) {
          $end = $start + 7;
          for ($start; $start <= $end; $start++) {
            $check++;
            if ($check == 1) {
              $event->sheet->styleCells("A$start:E$start", [
                'font' => array(
                  'name' => 'Times New Roman',
                  'size' => "18",
                ),
              ]);
              $event->sheet->getDelegate()->getRowDimension($start)->setRowHeight(30);
            } elseif ($check == 2) {
              $event->sheet->getDelegate()->getStyle("A$start:E$start")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFC000');

              $event->sheet->styleCells("A$start:E$start", [
                'font' => array(
                  'name' => 'Tahoma',
                  'size' => "11",
                ),
              ]);
              // set bolder
              $event->sheet->getStyle("A$start:E$start")->applyFromArray([
                'borders' => [
                  'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  ],
                ],
              ]);
              $event->sheet->getDelegate()->getRowDimension($start)->setRowHeight(24);
            } elseif ($check == 3 || $check == 4 ||$check == 5) {

              $event->sheet->styleCells("A$start:E$start", [
                'font' => array(
                  'size' => "11",
                ),
              ]);
              $event->sheet->getDelegate()->getRowDimension($start)->setRowHeight(24);
              $event->sheet->getDelegate()->getStyle("B$start")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');
              $event->sheet->getDelegate()->getCell("D$start")->setValue("=B$start*C$start");

              $event->sheet->getDelegate()->getStyle("D$start")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');
              $event->sheet->getDelegate()->getStyle("C$start")->getNumberFormat()
                ->setFormatCode("0%");

                 // set bolder
              $event->sheet->getStyle("A$start:E$start")->applyFromArray([
                'borders' => [
                  'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  ],
                ],
              ]);
            } elseif ($check == 6) {

              $event->sheet->getDelegate()->getStyle("A$start:E$start")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('E7E6E6');
              $event->sheet->getDelegate()->getStyle("B$start")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');
              $startCalculate = $start - 3;
              $endCalculate = $start - 1;
              $event->sheet->getDelegate()->getCell("B$start")->setValue("=Sum(B$startCalculate:B$endCalculate)");
              array_push($totalAmount, "B$start");

              $event->sheet->getDelegate()->getStyle("D$start")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');
              $startCalculate = $start - 3;
              $endCalculate = $start - 1;
              $event->sheet->getDelegate()->getCell("D$start")->setValue("=Sum(D$startCalculate:D$endCalculate)");
              $event->sheet->getDelegate()->getRowDimension($start)->setRowHeight(24);
              array_push($totalLicenseFee, "D$start");
               // set bolder
               $event->sheet->getStyle("A$start:E$start")->applyFromArray([
                'borders' => [
                  'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  ],
                ],
              ]);
            } elseif ($check == 7) {

              $event->sheet->getDelegate()->getRowDimension($start)->setRowHeight(12);
            } elseif ($check == 8) {

              $event->sheet->getDelegate()->getRowDimension($start)->setRowHeight(12);
            }
          }
          $check = 0;
          $start = 0;
          $start += $end + 1;
          array_push($totalNumber,"($i)");
        }
        // set style calculate total
        $totalRow = 3 + (8 * count($this->data['data']));
        $event->sheet->getDelegate()->getRowDimension($totalRow)->setRowHeight(30);
        $event->sheet->getDelegate()->getStyle("A$totalRow:E$totalRow")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('E7E6E6');
        $totalAmount = implode("+", $totalAmount);
        $totalLicenseFee = implode("+", $totalLicenseFee);

        $event->sheet->getDelegate()->getCell("B$totalRow")->setValue("=$totalAmount");
        $event->sheet->getDelegate()->getCell("D$totalRow")->setValue("=$totalLicenseFee");

        // set bolder
        $event->sheet->getStyle("A$totalRow:E$totalRow")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);
        $totalNumber = implode("+",$totalNumber);

        $event->sheet->getDelegate()->getCell("A$totalRow")->setValue("Total  $totalNumber");

      }
    ];
  }

  public function title(): string
  {
    return $this->data['year']?$this->data['year']:now()->year.'Subtotal-Licience';
  }

}