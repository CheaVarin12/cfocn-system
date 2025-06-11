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
class CustomerExport implements FromView, WithEvents,WithTitle
{
  use Exportable;
  public $customers;
  public function __construct($data)
  {
    $this->customers = $data['customers'];
  }

  public function view(): view
  {
    return view("admin::pages.customer.excel", [
      'customers' => $this->customers,
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'j'];
        //print fit column
        $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
        $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
        // Zoom Scale
        $event->sheet->getDelegate()->getSheetView()->setZoomScale(100);
        //text wrap
        for ($i = 1; $i <= 2 + count($this->customers); $i++) {
          for ($j = 0; $j < count($columns); $j++) {
            $col = "$columns[$j]$i";
            $event->sheet->getDelegate()->getStyle($col)->getAlignment()->setWrapText(true);
          }
        }

        //globle font size
        for ($i = 1; $i <= 2 + count($this->customers); $i++) {
          if ($i == 1) {
            $event->sheet->getDelegate()->getStyle($i)->getFont()->setSize(16);
          } else {
            $event->sheet->getDelegate()->getStyle($i)->getFont()->setSize(11);
          }
        }

        $event->sheet->getDelegate()->getStyle('A1:J1')
          ->getFont()
          ->setBold(true);
        // height header
        $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(21.6);
        $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(29.14);
        // set background color header
        $event->sheet->getDelegate()->getStyle('A1:J1')
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('FFFF00');
        $event->sheet->getDelegate()->getStyle('A2:J2')
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('D9E1F2');

        //set width column
  
        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 0) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(4);
          } elseif ($i == 1 || $i == 2 || $i == 5) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(13);
          } elseif ($i == 6 || $i == 7) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(18);
          } elseif ($i == 8 || $i == 9) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(50);
          } else {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(20);
          }
        }
        //set font family
        for ($j = 3; $j < 3 + count($this->customers); $j++) {
          for ($i = 0; $i < count($columns); $i++) {
            if ($i == 4 || $i == 9) {
              $event->sheet->styleCells($columns[$i] . $j, [
                'font' => array(
                  'name' => 'Battambang',
                  'size' => '9',
                ),
              ]);
            }
          }
        }
        //set bolder
        $end_row = count($this->customers) + 2;
        $event->sheet->getStyle("A1:J$end_row")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        //   // set filter
        $event->sheet->getDelegate()->setAutoFilter('B2:' . $event->sheet->getDelegate()->getHighestColumn() . '2');
      }
    ];
  }
  public function title(): string
  {
      return 'Customer-list';
  }
  
}