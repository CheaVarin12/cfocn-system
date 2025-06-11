<?php

namespace App\Exports;

use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Carbon\Carbon;

\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class InfraInvoiceExport implements FromView, WithEvents
{
    use Exportable;
    public $data;
    public $totalPrice;
    public $totalVat;
    public $totalGrand;
    public $date;
    
    public function __construct($data)
    {
        $currentDate = Carbon::now();
        $this->data = $data['data'];
        $this->totalPrice = $data['totalPrice'];
        $this->totalVat = $data['totalVat'];
        $this->totalGrand = $data['totalGrand'];
        $this->date = $data['date'];
    }

    public function view(): view
    {
        return view("admin::pages.report.summary_invoice.infra.excel", [
            'data' => $this->data,
            'totalPrice' => $this->totalPrice,
            'totalVat' => $this->totalVat,
            'totalGrand' => $this->totalGrand,
            'date' => $this->date,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
                $event->sheet->getDelegate()->getSheetView()->setZoomScale(100);
                //print fit column
                $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
                $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);

                //height 
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(31);
                for ($i = 2; $i <= count($this->data) + 2; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(29);
                    // font size
                    $event->sheet->getDelegate()->getStyle($i)->getFont()->setSize(10);
                }

                // width
                // set widht column
                for ($i = 0; $i < count($columns); $i++) {
                    if ($i == 0) {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(3.7);
                    } elseif ($i == 1 || $i == 2 || $i == 3 || $i == 4 || $i == 6) {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(14.11);
                    } elseif ($i == 5) {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(40);
                    } else {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(16);
                    }
                }

                // font size
                $event->sheet->getDelegate()->getStyle('1')->getFont()->setSize(16);
                $event->sheet->getDelegate()->getStyle('2')->getFont()->setSize(11);

                // text bold
                $event->sheet->getDelegate()->getStyle('A1:K2')
                    ->getFont()
                    ->setBold(true);

                $lastRow = count($this->data) + 3;
                //set border 
                $event->sheet->getStyle("A2:K$lastRow")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                //set format currency
                for ($i = 3; $i <= count($this->data) + 2; $i++) {
                    $event->sheet->getDelegate()->getStyle("H$i")->getNumberFormat()->setFormatCode('"$"* #,##0.00;"$"* -#,##0.00');
                    $event->sheet->getDelegate()->getStyle("I$i")->getNumberFormat()->setFormatCode('"$"* #,##0.00;"$"* -#,##0.00');
                    $event->sheet->getDelegate()->getStyle("J$i")->getNumberFormat()->setFormatCode('"$"* #,##0.00;"$"* -#,##0.00');
                }
                $event->sheet->getDelegate()->getStyle("H$i")->getNumberFormat()->setFormatCode('"$"* #,##0.00;[Red]"$"* -#,##0.00');
                $event->sheet->getDelegate()->getStyle("I$i")->getNumberFormat()->setFormatCode('"$"* #,##0.00;[Red]"$"* -#,##0.00');
                $event->sheet->getDelegate()->getStyle("J$i")->getNumberFormat()->setFormatCode('"$"* #,##0.00;[Red]"$"* -#,##0.00');
            }
        ];
    }
}
