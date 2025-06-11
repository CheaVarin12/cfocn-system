<?php

namespace App\Exports;

use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});
class CustomerDMCReportExport implements FromView, WithEvents
{
  public $pac;
  public $projectInExport;
  public $projects;
  public function __construct($data)
  {
    $this->pac = $data['data'];
    $this->projects = $data['projects'];
    $this->projectInExport = $data['projectInExport'];
  }

  public function view(): view
  {
    return view("admin::pages.report.customer_dmc.excel", [
      'pac' => $this->pac,
      'projectInExport' => $this->projectInExport,
      'projects' => $this->projects,
    ]);
  }

  public function registerEvents(): array
  {
      return [
        AfterSheet::class => function (AfterSheet $event) {

          $getDelegate = $event->sheet->getDelegate();

          $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M','N','O','P'];
          //print fit column
          $getDelegate->getPageSetup()->setFitToWidth(1);
          $getDelegate->getPageSetup()->setFitToHeight(0);
          // Zoom Scale
          $getDelegate->getSheetView()->setZoomScale(100);

          //Text style
          $alignmentAllCenter = [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
          ];


          //text wrap
          for ($i = 1; $i <= 2 + count($this->pac); $i++) {
            for ($j = 0; $j < count($columns); $j++) {
              $col = "$columns[$j]$i";
              $getDelegate->getStyle($col)->getAlignment()->setWrapText(true);
            }
          }

          //global font size
          for ($i = 1; $i <= 2 + count($this->pac); $i++) {
            if ($i == 1) {
              $getDelegate->getStyle($i)->getFont()->setSize(16);
            } else {
              $getDelegate->getStyle($i)->getFont()->setSize(11);
            }
          }

          $getDelegate->getStyle('A1:P1')
            ->getFont()
            ->setBold(true);

          // height header
          $getDelegate->getRowDimension('1')->setRowHeight(24);
          $getDelegate->getRowDimension('2')->setRowHeight(29);

          $getDelegate->getStyle('A2:P2')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('E2EFDA');

          //set width column
          for ($i = 0; $i < count($columns); $i++) {
            if ($i == 0 || $i == 1 || $i == 3 || $i == 4) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(15.81);
            } elseif ($i == 2) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(17.14);
            } elseif ($i == 5) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(25.81);
            } elseif ($i == 6) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(28.7);
            } elseif ($i == 7) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(35.59);
            } elseif ($i == 8) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(16.37);
            } elseif ($i == 9) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(17.25);
            } elseif ($i == 10) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(15.92);
            } else {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(28.92);
            }
          }

          //set bolder
          $end_row = count($this->pac) + 2;
          $event->sheet->getStyle("A2:P$end_row")->applyFromArray([
            'borders' => [
              'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              ],
            ],
          ]);

          $workSheet = $getDelegate;
          $workSheet->freezePane('A3'); // freezing here
          // set filter
          $getDelegate->setAutoFilter('A2:' . $getDelegate->getHighestColumn() . '2');

          $font = array('name' => 'Kh Siemreap', 'bold' => false, 'size' => 11);
          $i = 2;
          foreach ($this->pac as $item => $index) {
            $getDelegate->getStyle('H' . $i)->applyFromArray(['font' => $font]);
            $i++;
          }
        }
      ];
  }
}