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

class InvoiceReceiptAnnualSummaryExport implements FromView, WithEvents
{
    public $data;
    public $shortYear;
    public $totalAllInvoiceByMonth;
    public $totalAllReceiptByMonth;
    public function __construct($data)
    {
      $this->totalAllInvoiceByMonth = $data['totalAllInvoiceByMonth'];
      $this->totalAllReceiptByMonth = $data['totalAllReceiptByMonth'];
      $this->data = $data['data'];
      $this->shortYear = $data['shortYear'];
    }
  
    public function view(): view
    {
      return view("admin::pages.report.summary-annual.invoice-receipt.excel", [
        'totalAllInvoiceByMonth' => $this->totalAllInvoiceByMonth,
        'totalAllReceiptByMonth' => $this->totalAllReceiptByMonth,
        'data' => $this->data,
        'shortYear' => $this->shortYear,
      ]);
    }
  
    public function registerEvents(): array
    {
      return [
        AfterSheet::class => function (AfterSheet $event) {
  
          $getDelegate = $event->sheet->getDelegate();
  
          $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
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
  
          $getDelegate->getStyle('A1:AA1')
            ->getFont()
            ->setBold(true);
          $getDelegate->getStyle(1)->getFont()->setSize(16);
  
          // height header
          $getDelegate->getRowDimension('1')->setRowHeight(24);
          $getDelegate->getRowDimension('2')->setRowHeight(20);
          $getDelegate->getRowDimension('3')->setRowHeight(20);
  
          $getDelegate->getStyle('A2:AA2')
            ->getFont()
            ->setBold(true);
          $getDelegate->getStyle('A3:AA3')
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
  
          $totalServiceType = count($this->data);
          $totalCustomer = 0;
          foreach ($this->data as $item) {
            $totalCustomer += count($item['customer']);
          }
          //set bolder
          $end_row = $totalServiceType+$totalCustomer+4;
          $event->sheet->getStyle("A2:AA$end_row")->applyFromArray([
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
