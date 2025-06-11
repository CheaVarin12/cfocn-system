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
class TotalARDetailReportExport implements FromView, WithEvents, WithTitle
{
  public $data;


  public function __construct($data)
  {
    $this->data = $data;
  }
  public function view(): view
  {
    return view("admin::pages.report.ar_acging.excel", $this->data);
  }
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

        $worksheet = $event->sheet->getDelegate();

        $worksheet->getSheetView()->setZoomScale(100);
        //print fit column
        $worksheet->getPageSetup()->setFitToWidth(1);
        $worksheet->getPageSetup()->setFitToHeight(0);

        //height row
        $worksheet->getRowDimension('2')->setVisible(false);
        $worksheet->getRowDimension('3')->setVisible(false);
        $worksheet->getRowDimension('4')->setVisible(false);
        $worksheet->getRowDimension('5')->setVisible(false);


        //set width column
        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 0) {
            $worksheet->getColumnDimension($columns[$i])->setWidth(6);
          } elseif ($i == 1) {
            $worksheet->getColumnDimension($columns[$i])->setWidth(18);
          } elseif ($i == 2) {
            $worksheet->getColumnDimension($columns[$i])->setWidth(11);
          } elseif ($i == 3 || $i == 5) {
            $worksheet->getColumnDimension($columns[$i])->setWidth(15);
          } elseif ($i == 4) {
            $worksheet->getColumnDimension($columns[$i])->setWidth(7);
          } elseif ($i == 6 || $i == 7) {
            $worksheet->getColumnDimension($columns[$i])->setWidth(12);
          } elseif ($i == 8 || $i == 9 || $i == 10) {
            $worksheet->getColumnDimension($columns[$i])->setWidth(17);
          } else {
            $worksheet->getColumnDimension($columns[$i])->setWidth(35);
          }
        }

        //set height 
        $worksheet->getRowDimension('1')->setRowHeight(15.6);
        $worksheet->getRowDimension('6')->setRowHeight(36.8);
        $worksheet->getRowDimension('7')->setRowHeight(9);
        $worksheet->getRowDimension('8')->setRowHeight(46.5);

        // Freezing row column
        $worksheet->freezePane('C10');
        // set filter
        $worksheet->setAutoFilter('A9:' . $worksheet->getHighestColumn() . '9');

        //text wrap
        $worksheet->getStyle("A6:F6")->getAlignment()->setWrapText(true);
        $worksheet->getStyle("A9:L9")->getAlignment()->setWrapText(true);

        //font 

        $event->sheet->styleCells("A6", [
          'font' => array(
            'name' => '宋体',
            'size' => "12",
            'bold' => true,
          ),
        ]);
        $event->sheet->styleCells("A8", [
          'font' => array(
            'name' => '宋体',
            'size' => "10",
          ),
        ]);
        $event->sheet->styleCells("B8", [
          'font' => array(
            'name' => '宋体',
            'size' => "10",
          ),
        ]);
        $event->sheet->styleCells("B9:K9", [
          'font' => array(
            'name' => '宋体',
            'size' => "10",
          ),
        ]);
        $event->sheet->styleCells("L9", [
          'font' => array(
            'size' => "10",
          ),
        ]);

        // set font size and fone family list

        for ($i = 10; $i <= count($this->data['data']) + 9; $i++) {
          for ($j = 0; $j < count($columns); $j++) {
            $col = "$columns[$j]$i";
            if ($j == 1) {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => 'Calibri',
                  'size' => "10",
                ),
              ]);
              $worksheet->getStyle("$col")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('92D050');
            } elseif ($j == 0 || $j == 6 || $j == 7 || $j == 11) {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => 'Calibri',
                  'size' => "10",
                ),
              ]);
            } elseif ($j == 8 || $j == 9 || $j == 10) {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => 'Calibri',
                  'size' => "10",
                ),
              ]);
              $worksheet->getStyle("$col")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');
            } elseif ($j == 3) {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => 'Times New Roman',
                  'size' => "13",
                ),
              ]);
            } else {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => '宋体',
                  'size' => "10",
                ),
              ]);
            }
          }
          $event->sheet->styleCells(
            'C' . $i . ':C' . count($this->data['data']) + 9,
            [
              'font' => array(
                'name' => 'Kh Siemreap',
                'size' => "10",
              ),
            ]
          );
          $worksheet->getStyle("C$i:C$i")->getAlignment()->setWrapText(true);
        }

        foreach ($columns as $col) {
          $worksheet->getColumnDimension($col)->setWidth(17);
          if ($col == 'C' || $col == 'M') {
            $worksheet->getColumnDimension($col)->setWidth(37);
          }
        }

        //set background color
        $worksheet->getStyle("A8:M9")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('C0C0C0');

        $lastRow = count($this->data['data']) + 10;

        $worksheet->getStyle("A$lastRow:M$lastRow")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('C0C0C0');

        // set bolder 
        $event->sheet->getStyle("A8:M$lastRow")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        //border head none
        $a1 = [
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              'color' => ['argb' => 'FFFFFF'],
            ]
          ]
        ];
        $worksheet->getStyle("A1:M1")->applyFromArray($a1);
        $worksheet->getStyle("A6:G6")->applyFromArray($a1);
        $worksheet->getStyle("A7:G7")->applyFromArray($a1);


        $endRowList = $lastRow - 1;



        //caculate 
        // $worksheet->getCell("G$lastRow")->setValue("=SUM(G10:G$endRowList)");
        // $worksheet->getStyle("G$lastRow")->getNumberFormat()
        //   ->setFormatCode('""* #,##0.00');
        // $worksheet->getCell("H$lastRow")->setValue("=SUM(H10:H$endRowList)");
        // $worksheet->getStyle("H$lastRow")->getNumberFormat()
        //   ->setFormatCode('""* #,##0.00');
        // $worksheet->getCell("I$lastRow")->setValue("=SUM(I10:I$endRowList)");
        // $worksheet->getStyle("I$lastRow")->getNumberFormat()
        //   ->setFormatCode('""* #,##0.00');
        // $worksheet->getCell("J$lastRow")->setValue("=SUM(J10:J$endRowList)");
        // $worksheet->getStyle("J$lastRow")->getNumberFormat()
        //   ->setFormatCode('""* #,##0.00');
        // $worksheet->getCell("K$lastRow")->setValue("=SUM(K10:K$endRowList)");
        // $worksheet->getStyle("K$lastRow")->getNumberFormat()
        //   ->setFormatCode('""* #,##0.00');
      }

    ];
  }
  public function title(): string
  {
    return 'Total';
  }
}
