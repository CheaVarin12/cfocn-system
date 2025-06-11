<?php


namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});
class revenueItemListReportExport implements FromView, WithEvents, WithTitle
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;

    }
    public function view(): view
    {
        return view("admin::pages.report.revenue.excel.index", [
            'data' => $this->data,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                
                $invoiceInproject = [];
                $invoiceCreditNoteInProject=[];
                foreach ($this->data['creditNote'] as $invoice) {
                    if ($invoice?->purchase?->project_id == $this->data['project']->id) {
                        array_push($invoiceCreditNoteInProject, $invoice);
                    }
                }
                $invoices = array_merge($this->data['lease'], $this->data['sale'], $this->data['service'],$invoiceCreditNoteInProject);
                foreach ($invoices as $invoice) {
                    if ($invoice->purchase?->project_id == $this->data['project']->id) {
                        array_push($invoiceInproject, $invoice);
                    }
                }
                $columns = ['A', 'B', 'C', 'D'];
                //print fit column
                $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
                $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
                // Zoom Scale
                $event->sheet->getDelegate()->getSheetView()->setZoomScale(100);

                // set wrap text
                $event->sheet->getDelegate()->getStyle("A2:D3")->getAlignment()->setWrapText(true);

                // set width column
                for ($i = 0; $i < count($columns); $i++) {
                    if ($i == 0) {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(4.14);
                    } elseif ($i == 1) {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(31.81);
                    } elseif ($i == 2) {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(22.81);
                    } else {
                        $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(45.92);
                    }
                }

                // get layout counts (add 1 to rows for heading row)
                $row_count = count($invoiceInproject) + 4;

                // set dropdown column
                $drop_column = 'B';
                // set dropdown options
                if ($this->data['project']->id == 2) {
                    $options = [
                        'Submarine Cable Charges',
                        'Other Charges',
                        'Credit Note',
                        'Debit Note'
                    ];
                } else {
                    $options = [
                        'Optical Cable Networks Charges',
                        'Other Charges',
                        'Credit Note',
                        'Debit Note'
                    ];
                }
                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$drop_column}4")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

                // clone validation to remaining rows
                for ($i = 4; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }

                // set height row
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(25);
                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(15);
                $event->sheet->getDelegate()->getRowDimension('3')->setRowHeight(23.4);
                for ($i = 4; $i <= count($invoiceInproject); $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(13.8);
                }

                //set worksheet color
                $event->sheet->getDelegate()->getTabColor()->setRGB('A9D08E');

                //set font size
    
                $event->sheet->styleCells("A1:D1", [
                    'font' => array(
                        'size' => 20,
                    ),
                ]);

                $event->sheet->styleCells("A4:D9", [
                    'font' => array(
                        'size' => 10,
                    ),
                ]);
                $event->sheet->styleCells("A2:D2", [
                    'font' => array(
                        'size' => 10,
                    ),
                ]);
                $event->sheet->styleCells("A4:D4", [
                    'font' => array(
                        'size' => 9,
                    ),
                ]);

                $event->sheet->styleCells("A3:D3", [
                    'font' => array(
                        'size' => 8,
                    ),
                ]);

                //background color
                $event->sheet->getDelegate()->getStyle("A2:D3")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('D9D9D9');

                $event->sheet->getDelegate()->getStyle("A4:D4")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('EDEDED');

                //set text color
                $event->sheet->getDelegate()->getStyle('A4:D4')
                    ->getFont()
                    ->getColor()
                    ->setARGB('808080');
                $event->sheet->getDelegate()->getStyle('B3')
                    ->getFont()
                    ->getColor()
                    ->setARGB('C00000');

                // set bolder 
                $endRow = count($invoiceInproject) + 5;
                $event->sheet->getStyle("A4:D$endRow")->applyFromArray([
                    'borders' => [
                        'horizontal' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);
                $newEndRow = $endRow - 1;
                $event->sheet->getStyle("A3:D$newEndRow")->applyFromArray([
                    'borders' => [
                        'vertical' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $event->sheet->getStyle("A3:A$newEndRow")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $event->sheet->getStyle("D3:D$newEndRow")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $event->sheet->getStyle("A2:D3")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $event->sheet->getStyle("B3")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                        ],
                    ],
                ]);
                $event->sheet->getStyle("B2")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                        ],
                    ],
                ]);
            }
        ];
    }
    public function title(): string
    {
        return $this->data['project']->name . '_item list';
    }
}