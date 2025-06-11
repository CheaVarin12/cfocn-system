<?php

namespace App\Exports;

use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class receiptExport implements FromView, WithEvents
{
  use Exportable;
  public $receipt;
  public $invoice;
  public $invoice_detail;
  public function __construct($data)
  {
    $this->invoice = $data['invoice'];
    $this->invoice_detail = $data['invoice_detail'];
    $this->receipt = $data['receipt'];
  }
  public function view(): view
  {
    return view('admin::pages.receipt.excel-receipt-export', [
      'invoice' => $this->invoice,
      'invoice_detail' => $this->invoice_detail,
      'receipt' => $this->receipt,
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {

        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        //print fit column
        $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
        $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
        // Zoom Scale
        $event->sheet->getDelegate()->getSheetView()->setZoomScale(115);
        //text wrap 
        for ($i = 20; $i <= 19 + count($this->invoice_detail); $i++) {
          $event->sheet->getDelegate()->getStyle($i)->getAlignment()->setWrapText(true);
        }


        //head
        $heads = [
          ['height' => '', 'size' => '', 'bold' => ''],
          ['height' => 15.6, 'size' => 12, 'bold' => true],
          ['height' => 15, 'size' => 10, 'bold' => false],
          ['height' => 15, 'size' => 10, 'bold' => false],
          ['height' => 15.6, 'size' => 10, 'bold' => false],
          ['height' => 15, 'size' => 12, 'bold' => false],
        ];

        for ($i = 5; $i <= 9; $i++) {
          $row = "A$i:H$i";
          $event->sheet->styleCells($row, [
            'font' => array(
              'name' => 'Arial',
              'size' => $heads[$i - 4]['size'],
              'bold' => $heads[$i - 4]['bold']
            ),
          ]);
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight($heads[$i - 4]['height']);
        }
        // set line
        $event->sheet->getDelegate()->getStyle('A8:H8')->applyFromArray([
          'borders' => [
            'bottom' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
            ]
          ]
        ]);

        //set width of column
        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 0) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(3.40);
          } elseif ($i == 1) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(21.14);
          } elseif ($i == 2) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(12.14);
          } elseif ($i == 3) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(21.37);
          } elseif ($i == 4) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(7.37);
          } elseif ($i == 5) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(6.54);
          } elseif ($i == 6) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(11.03);
          } else {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(17.37);
          }
        }

        for ($i = 6; $i <= 8; $i++) {
          $row = "A$i:H$i";
          $event->sheet->getDelegate()->getStyle($row)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFFFF');
        }
        //O F F I C I A L    R E C E I P T
        $alignmentAllCenter = [
          'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
          'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ];
        $event->sheet->styleCells('A10:H10', [
          'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'size' => 20
          ),
          'alignment' => $alignmentAllCenter
        ]);
        $event->sheet->styleCells('A11:H11', [
          'font' => array(
            'name' => 'Arial',
            'bold' => false,
            'size' => 20
          ),
          'alignment' => $alignmentAllCenter
        ]);
        ///  
        $event->sheet->styleCells('A12', [
          'font' => array(
            'name' => 'Times New Roman',
            'bold' => true,
            'size' => 10
          ),
        ]);

        $event->sheet->styleCells('G12:H12', [
          'font' => array(
            'name' => 'Arial',
            'size' => 10
          ),
        ]);

        //
        $event->sheet->styleCells('A13', [
          'font' => array(
            'name' => 'Times New Roman',
            'size' => 9
          ),
        ]);

        $event->sheet->styleCells('G13:H13', [
          'font' => array(
            'name' => 'Calibri',
            'size' => 11
          ),
        ]);
        //
        $event->sheet->styleCells('A14', [
          'font' => array(
            'name' => 'Times New Roman',
            'size' => 9
          ),
        ]);

        $event->sheet->styleCells('G14:H14', [
          'font' => array(
            'name' => 'Arial',
            'size' => 10
          ),
        ]);
        //
        $event->sheet->styleCells('A15', [
          'font' => array(
            'name' => 'Times New Roman',
            'size' => 9
          ),
        ]);

        for ($i = 12; $i <= 18; $i++) {
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(15);
        }

        //head

        $event->sheet->styleCells('A19:H19', [
          'font' => array(
            'name' => 'Arial',
            'size' => 10,
            'bold' => true,
          ),
          'alignment' => $alignmentAllCenter
        ]);
        $event->sheet->getDelegate()->getStyle('A19:H19')
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('BFBFBF');
        $event->sheet->getDelegate()->getRowDimension(19)->setRowHeight(13.2);

        //list

        //set font family
        for ($j = 20; $j <= 19 + count($this->invoice_detail); $j++) {
          for ($i = 0; $i < count($columns); $i++) {
            if ($i == 0 || $i == 1 || $i == 2) {
              $event->sheet->styleCells($columns[$i] . $j, [
                'font' => array(
                  'name' => 'Arial',
                  'size' => '9',
                ),
              ]);
            } else {
              $event->sheet->styleCells($columns[$i] . $j, [
                'font' => array(
                  'name' => 'Arial',
                  'size' => '10',
                ),
              ]);
            }
          }
          $event->sheet->getDelegate()->getRowDimension($j)->setRowHeight(42);
        }


        //set font family
        for ($j = 20 + count($this->invoice_detail); $j <= 23 + count($this->invoice_detail); $j++) {
          for ($i = 0; $i < count($columns); $i++) {
            if ($i == 4 || $i == 5) {
              $event->sheet->styleCells($columns[$i] . $j, [
                'font' => array(
                  'name' => 'Arial',
                  'size' => '10',
                  'bold' => true,
                ),
              ]);

              $event->sheet->getDelegate()->getStyle($columns[$i] . $j)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('BFBFBF');
            } else {
              $event->sheet->styleCells($columns[$i] . $j, [
                'font' => array(
                  'name' => 'Arial',
                  'size' => '10',
                  'bold' => true,
                ),
              ]);
            }
          }
          $event->sheet->getDelegate()->getRowDimension($j)->setRowHeight(15);
        }

        $row_note = 25 + count($this->invoice_detail);
        $event->sheet->getDelegate()->getStyle("A$row_note")->getAlignment()->setWrapText(true);

        $row_re = 28 + count($this->invoice_detail);

        $event->sheet->styleCells("A$row_re:H$row_re", [
          'font' => array(
            'name' => 'Times New Roman',
            'bold' => true,
            'size' => 12
          ),
          'alignment' => $alignmentAllCenter
        ]);

        //set bolder
        $end_row = count($this->invoice_detail) + 23;
        $event->sheet->getStyle("A19:H$end_row")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        //none bolder

        for ($i = 20 + count($this->invoice_detail); $i <= 22 + count($this->invoice_detail); $i++) {
          $event->sheet->getStyle("A$i:E$i")->applyFromArray([
            'borders' => [
              'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
              ],
            ],
          ]);
        }
        $row = 23 + count($this->invoice_detail);
        $event->sheet->getStyle("A$row:E$row")->applyFromArray([
          'borders' => [
            'top' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
            ],
          ],
        ]);
        $row = 20 + count($this->invoice_detail);
        $endrow = 23 + count($this->invoice_detail);
        $event->sheet->getDelegate()->getStyle("A$row:E$endrow")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('FFFFFF');

        $event->sheet->getStyle("A19:H$end_row")->applyFromArray([
          'borders' => [
            'outline' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        $lastRow = count($this->invoice_detail) + 19;
        $khmerOsBattambang = array(
          'name' => 'Khmer OS Battambang',
          'size' => "10",
        );
        $event->sheet->styleCells("B20:D$lastRow", ['font' => [...$khmerOsBattambang]]);
      }
    ];
  }
}
