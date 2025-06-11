<?php

namespace App\Exports;

use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class TemplateFttxUpload implements FromView, ShouldAutoSize, WithEvents
{

    public $data;

    public function __construct($data)
    {
        $this->data = $data;

    }
    public function view(): view
    {
        return view("admin::pages.fttx.fttx.template-excel-upload", [
            'data' => $this->data,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $getDelegate = $event->sheet->getDelegate();
    
                // Zoom Scale
                $getDelegate->getSheetView()->setZoomScale(100);
    
                // Set row height
                $getDelegate->getRowDimension('1')->setRowHeight(38);
    
                // Set filter on row 1
                $getDelegate->setAutoFilter('A1:' . $getDelegate->getHighestColumn() . '1');
    
                // Freeze the first row
                $getDelegate->freezePane('A2'); 

                $getDelegate->getStyle('P2')
                ->getNumberFormat()
                ->setFormatCode('dd-mmm-yy');
                   // Optionally, set a value in A2 (if needed)
                   $getDelegate->setCellValue('P2', now()->toDateString());
            },
        ];
    }
}
