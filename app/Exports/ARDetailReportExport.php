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
class ARDetailReportExport implements FromView, WithEvents, WithTitle
{
    public $data;
    public $id;

    public $customerName;
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->id = $data['id'];
        $this->customerName = $data['customerName'];
    }

    public function view(): view
    {
        return view("admin::pages.ar_acging.detail-excel", [
            'data' => $this->data,
            'id' => $this->id,
            'customerName' => $this->customerName,
        ]);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
                $workSheet = $event->sheet->getDelegate();
                $workSheet->getSheetView()->setZoomScale(90);
                //print fit column
                $workSheet->getPageSetup()->setFitToWidth(1);
                $workSheet->getPageSetup()->setFitToHeight(0);

                //hide row
                for ($i = 1; $i <= 7; $i++) {
                    $workSheet->getRowDimension($i)->setVisible(false);
                }
                // set WrapText
                $workSheet->getStyle("A9:k9")->getAlignment()->setWrapText(true);

                //set width column
                for ($i = 0; $i < count($columns); $i++) {
                    if ($i == 0) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(4.71);
                    } elseif ($i == 1) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(22.71);
                    } elseif ($i == 2) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(6.41);
                    } elseif ($i == 3) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(15);
                    } elseif ($i == 4) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(12.71);
                    } elseif ($i == 5) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(10.91);
                    } elseif ($i == 6) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(10.41);
                    } elseif ($i == 7 || $i == 8 || $i == 9) {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(17);
                    } else {
                        $workSheet->getColumnDimension($columns[$i])->setWidth(35);
                    }
                }
            //    set height
                $workSheet->getRowDimension('8')->setRowHeight(29.4);
                $workSheet->getRowDimension('9')->setRowHeight(27.6);
                
                // set font 
                // font head
                $event->sheet->styleCells("B8:J9", [
                    'font' => array(
                      'name' => '宋体',
                      'size' => "11",
                    ),
                  ]);
                

                $startRow = 9;
                $rowProject = 0;
                $rowPac = 0;
                $rowHeight = 0;
                $startRowCalculate = 10;
                $endRowCalculate = 0;
                $rowCalculateTotalLenght=[];
                $rowCalculateTotalCoreKm=[];
                $rowCalculateTotalAmount=[];
                $rowCalculateTotalPaidAmount=[];
                $rowCalculateTotalRemainingAmount=[];

                foreach ($this->data as $index => $project) {
                    $rowProject++;
                    foreach ($project->purchase->where('customer_id', $this->id)->where('status', 1) as $key => $item) {
                        $rowPac++;
                    }
                    
                    $rowHeight = $rowProject + $rowPac + $startRow;
                    $workSheet->getRowDimension($rowHeight)->setRowHeight(26.3);
                    $workSheet->getStyle("A$rowHeight:K$rowHeight")
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('BFBFBF');
                        
                        $workSheet->getStyle("A$rowHeight:K$rowHeight")
                        ->getFont()
                        ->setBold(true);

                        $event->sheet->styleCells("A$rowHeight:B$rowHeight", [
                            'font' => array(
                              'name' => '宋体',
                              'size' => "11",
                            ),
                          ]);

                    $endRowCalculate = $rowHeight - 1;
                    //set bolder list
                    $endrowbolder=$rowHeight+1;
                    $event->sheet->getStyle("A$startRowCalculate:K$endrowbolder")->applyFromArray([
                        'borders' => [
                          'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                          ],
                        ],
                      ]);
               
                    
                    //calculate
                    for ($i = $startRowCalculate; $i <= $startRowCalculate + count(range($startRowCalculate, $endRowCalculate)); $i++) {
                         //core/km
                        $workSheet->getCell("G$i")->setValue("=C$i*F$i");
                        // set formart money
                        $workSheet->getStyle("D$i")->getNumberFormat()
                            ->setFormatCode('""* #,##0.00');
                        $workSheet->getStyle("H$i")->getNumberFormat()
                            ->setFormatCode('""* #,##0.00');
                        $workSheet->getStyle("I$i")->getNumberFormat()
                            ->setFormatCode('""* #,##0.00');
                        $workSheet->getStyle("J$i")->getNumberFormat()
                            ->setFormatCode('""* #,##0.00');
                        //text wrap list
                        $workSheet->getStyle("B$i")->getAlignment()->setWrapText(true);
                        $workSheet->getStyle("E$i")->getAlignment()->setWrapText(true);
                        $workSheet->getStyle("K$i")->getAlignment()->setWrapText(true);
                    }
                    array_push($rowCalculateTotalLenght,"F$rowHeight");
                    array_push($rowCalculateTotalCoreKm,"G$rowHeight");
                    array_push($rowCalculateTotalAmount,"H$rowHeight");
                    array_push($rowCalculateTotalPaidAmount,"I$rowHeight");
                    array_push($rowCalculateTotalRemainingAmount,"J$rowHeight");

                    $workSheet->getCell("F$rowHeight")->setValue("=SUM(F$startRowCalculate:F$endRowCalculate)");
                    $workSheet->getCell("G$rowHeight")->setValue("=SUM(G$startRowCalculate:G$endRowCalculate)");
                    $workSheet->getCell("H$rowHeight")->setValue("=SUM(H$startRowCalculate:H$endRowCalculate)");
                    $workSheet->getStyle("H$rowHeight")->getNumberFormat()
                    ->setFormatCode('""* #,##0.00');
                    $workSheet->getCell("I$rowHeight")->setValue("=SUM(I$startRowCalculate:I$endRowCalculate)");
                    $workSheet->getStyle("I$rowHeight")->getNumberFormat()
                    ->setFormatCode('""* #,##0.00');
                    $workSheet->getCell("J$rowHeight")->setValue("=SUM(J$startRowCalculate:J$endRowCalculate)");
                    $workSheet->getStyle("J$rowHeight")->getNumberFormat()
                    ->setFormatCode('""* #,##0.00');
                    $startRowCalculate = $rowHeight + 1;
                    $rowHeight -= $startRow;
                  
                }
                // last row  total
                $workSheet->getRowDimension($endrowbolder)->setRowHeight(28.5);
                $workSheet->getStyle("A$endrowbolder:K$endrowbolder")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('BFBFBF');
                $workSheet->getStyle("A$endrowbolder:K$endrowbolder")
                ->getFont()
                ->setBold(true);
              
                $totalLength = implode("+",$rowCalculateTotalLenght);
                $totalCoreKm= implode("+",$rowCalculateTotalCoreKm);
                $totalAmount= implode("+",$rowCalculateTotalAmount);
                $totalPaidAmount= implode("+",$rowCalculateTotalPaidAmount);
                $totalRemainingAmount= implode("+",$rowCalculateTotalRemainingAmount);
               
                $workSheet->getCell("F$endrowbolder")->setValue("=ROUND($totalLength,2)");
                $workSheet->getCell("G$endrowbolder")->setValue("=ROUND($totalCoreKm,2)");
                $workSheet->getCell("H$endrowbolder")->setValue("=ROUND($totalAmount,2)");
                $workSheet->getStyle("H$endrowbolder")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');
                $workSheet->getCell("I$endrowbolder")->setValue("=ROUND($totalPaidAmount,2)");
                $workSheet->getStyle("I$endrowbolder")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');
                $workSheet->getCell("J$endrowbolder")->setValue("=ROUND($totalRemainingAmount,2)");
                $workSheet->getStyle("J$endrowbolder")->getNumberFormat()
                ->setFormatCode('""* #,##0.00');

                //  background head
                $workSheet->getStyle("A8:K9")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('BFBFBF');

                      //set bolder
            $event->sheet->getStyle("A8:K9")->applyFromArray([
                'borders' => [
                  'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  ],
                ],
              ]);
            }
        ];
    }
    public function title(): string
    {
        return $this->customerName;
    }
}