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


class FttxReportExport implements FromView, WithEvents
{
  public $data;
  public $totalAllAmountByMonth;
  public $from_date;
  public $to_date;
  public $fttx_status;
  public function __construct($data)
  {
    $this->data = $data['data'];
    $this->totalAllAmountByMonth = $data['totalAllAmountByMonth'];
    $this->from_date = $data['from_date'];
    $this->to_date = $data['to_date'];
    $this->fttx_status = $data['fttx_status'];
  }

  public function view(): view
  {
    return view("admin::pages.fttx.report.excel", [
      'data' => $this->data,
      'totalAllAmountByMonth' => $this->totalAllAmountByMonth,
      'from_date' => $this->from_date,
      'to_date' => $this->to_date,
      'fttx_status' => $this->fttx_status,
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {

        $getDelegate = $event->sheet->getDelegate();
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
        //print fit column
        $getDelegate->getPageSetup()->setFitToWidth(1);
        $getDelegate->getPageSetup()->setFitToHeight(0);
        // Zoom Scale
        $getDelegate->getSheetView()->setZoomScale(100);

        $getDelegate->setCellValue(
          'A2',
          "Statistical functions, Total: " . count($this->data) . " types \n统计功能，总共 " . count($this->data) . " 个类型"
        );
        $getDelegate->getStyle('A1:O3')->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(35);

        //set width column
        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 1) {
            $getDelegate->getColumnDimension($columns[$i])->setWidth(34);
          } else {
            $getDelegate->getColumnDimension($columns[$i])->setWidth(14);
          }
        }
        $end_row = count($this->data)+4;
        foreach ($this->data as $item) {
            $end_row += count($item['isp']);
        }
        //set bolder
        $event->sheet->getStyle("A2:O$end_row")->applyFromArray([
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
