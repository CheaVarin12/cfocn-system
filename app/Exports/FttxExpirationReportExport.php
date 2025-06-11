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


class FttxExpirationReportExport implements FromView, WithEvents
{
    public $data;
    public $totalAllAmountByMonth;
    public $columns;
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->totalAllAmountByMonth = $data['totalAllAmountByMonth'];
        $this->columns = $data['columns'];
    }

    public function view(): view
    {
        return view("admin::pages.fttx.expiration-report.excel", [
            'data' => $this->data,
            'totalAllAmountByMonth' => $this->totalAllAmountByMonth,
            'columns' => $this->columns,
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

                $getDelegate->getStyle('A1:O3')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(35);
                $lastColumn = 2 + count($this->columns); //
                //set width column
                $getDelegate->getDefaultColumnDimension()->setWidth(14);
                $getDelegate->getColumnDimension('B')->setWidth(40);
                $getDelegate->getColumnDimension('A')->setWidth(6);
                $end_row = count($this->data) + 4;
                foreach ($this->data as $item) {
                    $end_row += count($item['isp']);
                }

                //set bolder
                $event->sheet->getStyle("A2:{$this->getColumnNameByIndex($lastColumn)}$end_row")->applyFromArray([
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
