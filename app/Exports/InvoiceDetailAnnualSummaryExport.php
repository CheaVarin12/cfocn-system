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


class InvoiceDetailAnnualSummaryExport implements FromView, WithEvents
{
    public $data;
    public $shortYear;
    public $totalAmountOfInvoiceDetail;
    public function __construct($data)
    {
      $this->data = $data['data'];
      $this->shortYear = $data['shortYear'];
      $this->totalAmountOfInvoiceDetail = $data['totalAmountOfInvoiceDetail'];

    }
  
    public function view(): view
    {
      return view("admin::pages.report.summary-annual.invoice-detail.excel", [
        'data'      => $this->data,
        'shortYear' => $this->shortYear,
        'totalAmountOfInvoiceDetail' => $this->totalAmountOfInvoiceDetail,
      ]);
    }

    public function registerEvents(): array
    {
      return [
        AfterSheet::class => function (AfterSheet $event) {
  
          $getDelegate = $event->sheet->getDelegate();
  
          $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T','U'];
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
  
          $getDelegate->getStyle('A1:U1')
            ->getFont()
            ->setBold(true);
          $getDelegate->getStyle(1)->getFont()->setSize(16);
  
          // height header
          $getDelegate->getRowDimension('1')->setRowHeight(24);
          $getDelegate->getRowDimension('2')->setRowHeight(20);
          $getDelegate->getRowDimension('3')->setRowHeight(20);
  
          $getDelegate->getStyle('A2:U2')
            ->getFont()
            ->setBold(true);
          $getDelegate->getStyle('A3:U3')
            ->getFont()
            ->setBold(true);
  
          //set width column
          for ($i = 0; $i < count($columns); $i++) {
            if ($i == 0) {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(50);
            } else {
              $getDelegate->getColumnDimension($columns[$i])->setWidth(11);
            }
          }

          //set bolder
          $end_row = count($this->data)+4;
          $event->sheet->getStyle("A2:U$end_row")->applyFromArray([
            'borders' => [
              'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              ],
            ],
          ]);
        }
  
      ];
    }
}
