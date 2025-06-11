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
class SaleJournalReportExport implements FromView, WithEvents
{
  public $invoice;
  public $projectsInExport;
  public $rate;
  public $date;
  public function __construct($data)
  {
    $this->invoice = $data['data'];
    $this->projectsInExport = $data['projectsInExport'];
    $this->rate = $data['rate'];
    $this->date = $data['date'];
  }

  public function view(): view
  {
    return view("admin::pages.report.sale-journal-report-excel", [
      'invoice' => $this->invoice,
      'projectsInExport' => $this->projectsInExport,
      'rate'=> $this->rate,
      'date'=>$this->date,
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
        $event->sheet->getDelegate()->getSheetView()->setZoomScale(85);
        //print fit column
        $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
        $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);

        //set width column
        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 0) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(15);
          } elseif ($i == 1) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(16);
          } elseif ($i == 2) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(30);
          } elseif ($i == 3) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(19.11);
          } elseif ($i == 4) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(33.6);
          } elseif ($i == 5) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(17.31);
          } elseif ($i == 6) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(13.91);
          } elseif ($i == 7 || $i == 17) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(16.31);
          } elseif ($i == 8) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(11.81);
          } elseif ($i == 9) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(14.41);
          } elseif ($i == 10 || $i == 12) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(17.31);
          } elseif ($i == 11) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(12.91);
          } elseif ($i == 13 || $i == 14 || $i == 15) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(15.31);
          } elseif ($i == 16) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(14.91);
          } elseif ($i == 18) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(15);
          } else {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(13);
          }

        }
        $event->sheet->setBreak('A22', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //set height head
        $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(35);
        $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(31.1);
        $event->sheet->getDelegate()->getRowDimension('3')->setRowHeight(26.3);
        $event->sheet->getDelegate()->getRowDimension('4')->setRowHeight(21);
        $event->sheet->getDelegate()->getRowDimension('5')->setRowHeight(21);
        $event->sheet->getDelegate()->getRowDimension('6')->setRowHeight(43.5);
        $event->sheet->getDelegate()->getRowDimension('7')->setRowHeight(25.5);
        $event->sheet->getDelegate()->getRowDimension('8')->setRowHeight(43.5);
        $event->sheet->getDelegate()->getRowDimension('9')->setRowHeight(18.8);

        $lastrow = 10 + count($this->invoice);
        $event->sheet->getDelegate()->getRowDimension("$lastrow")->setRowHeight(35.3);

        // Freezing row column
        $event->sheet->getDelegate()->freezePane('C10');

        // set font and font size to head
        $event->sheet->styleCells("D1", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "14",
          ),
        ]);
        $event->sheet->styleCells("A2", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("D2", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("A3", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("D3", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("A4", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("D4", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("A5", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("N5", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "11",
          ),
        ]);

        //set font and font size head
  
        $event->sheet->styleCells("A6:T8", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "10",
          ),
        ]);

        //set font size head
  
        $event->sheet->styleCells("A7", [
          'font' => array(
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("B7", [
          'font' => array(
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("C7", [
          'font' => array(
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("A6", [
          'font' => array(
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("E7", [
          'font' => array(
            'size' => "12",
          ),
        ]);

        $event->sheet->styleCells("A9:T9", [
          'font' => array(
            'name' => 'Times New Roman',
            'size' => "10",
          ),
        ]);

        // set font size and fone family list
  
        for ($i = 10; $i <= count($this->invoice) + 9; $i++) {
          for ($j = 0; $j < count($columns); $j++) {
            $col = "$columns[$j]$i";
            if ($j == 0 || $j == 1 || $j == 2 || $j == 3) {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => 'Calibri',
                  'size' => "12",
                ),
              ]);
            } elseif ($j == 4) {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => 'Khmer OS',
                  'size' => "12",
                ),
              ]);
            } else {
              $event->sheet->styleCells($col, [
                'font' => array(
                  'name' => 'Times New Roman',
                  'size' => "12",
                ),
              ]);
            }
          }
        }
        $event->sheet->styleCells("A$lastrow:F$lastrow", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "11",
          ),
        ]);
        $event->sheet->styleCells("G$lastrow:T$lastrow", [
          'font' => array(
            'name' => 'Times New Roman',
            'size' => "11",
          ),
        ]);

        // background gree
        $event->sheet->getDelegate()->getStyle('A6:T9')
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('92D050');

        $event->sheet->getDelegate()->getStyle("G$lastrow:T$lastrow")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('92D050');

        // setWrapText
        $event->sheet->getDelegate()->getStyle("A2:C2")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("R6")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("S6")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("C7")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("D7")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("E7")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("F7")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("G7")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("I7")->getAlignment()->setWrapText(true);
        $event->sheet->getDelegate()->getStyle("A9:T9")->getAlignment()->setWrapText(true);

        // set filter
        $event->sheet->getDelegate()->setAutoFilter('A8:' . $event->sheet->getDelegate()->getHighestColumn() . '8');


        // set bolder
        $event->sheet->getStyle("A6:T$lastrow")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        //border head none
        $a1 = [
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              'color' => ['argb' => 'FFFFFF'],
            ]
          ]
        ];
        $event->sheet->getDelegate()->getStyle("A1:M5")->applyFromArray($a1);
        //calculate money by row
        for ($i = 10; $i <= count($this->invoice) + 9; $i++) {
          for ($j = 0; $j < count($columns); $j++) {
            $col = "$columns[$j]$i";
            $totalPrice = $this->invoice[$i - 10]->total_price;
            if ($j == 6) {
              $event->sheet->getDelegate()->getCell("$col")->setValue("");
              $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                ->setFormatCode('"$"* #,##0.00');
            } elseif ($j == 7) {
              $event->sheet->getDelegate()->getCell("$col")->setValue("");
              $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                ->setFormatCode('"R"* #,##0');
            } elseif ($j == 8) {
              $event->sheet->getDelegate()->getCell("$col")->setValue("");
              $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                ->setFormatCode('"$"* #,##0.00');
            }

            $check_rate_first = 0;
            $check_rate_seconde = 0;
            foreach ($this->invoice[$i - 10]->invoiceDetail as $item) {
              if ($item->rate_first) {
                $check_rate_first += 1;
              }
              if ($item->rate_second) {
                $check_rate_seconde += 1;
              }
            }

            if ($check_rate_first != 0 || $check_rate_seconde != 0) {
              if ($j == 9) {
                $event->sheet->getDelegate()->getCell("$col")->setValue($totalPrice);
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"$"* #,##0.00');
              } elseif ($j == 10) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=$totalPrice*T$i");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"R"* #,##0');
              } elseif ($j == 11) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=J$i*10%");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"$"* #,##0.00');
              } elseif ($j == 12) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=L$i*T$i");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"R"* #,##0');
              }
              if ($j == 17) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=SUM(J$i+L$i)");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"$"* #,##0.00');
              } elseif ($j == 18) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=SUM(K$i+M$i)");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"R"* #,##0');
              }
            } else {
              if ($j == 13) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=$totalPrice/1.1");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"$"* #,##0.00');
              } elseif ($j == 14) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=N$i*T$i");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"R"* #,##0');
              } elseif ($j == 15) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=$totalPrice/1.1*10%");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"$"* #,##0.00');
              } elseif ($j == 16) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=P$i*T$i");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"R"* #,##0');
              }
              if ($j == 17) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=SUM(N$i+P$i)");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"$"* #,##0.00');
              } elseif ($j == 18) {
                $event->sheet->getDelegate()->getCell("$col")->setValue("=SUM(O$i+Q$i)");
                $event->sheet->getDelegate()->getStyle("$col")->getNumberFormat()
                  ->setFormatCode('"R"* #,##0');
              }
            }

          }
        }

        // calculate sum money 
        $lastCalculate = $lastrow - 1;
        $event->sheet->getDelegate()->getCell("G$lastrow")->setValue("=ROUND(SUM(G10:G$lastCalculate),2)");
        $event->sheet->getDelegate()->getStyle("G$lastrow")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $event->sheet->getDelegate()->getCell("H$lastrow")->setValue("=SUM(H10:H$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("H$lastrow")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');

        $event->sheet->getDelegate()->getCell("I$lastrow")->setValue("=ROUND(SUM(I10:I$lastCalculate),2)");
        $event->sheet->getDelegate()->getStyle("I$lastrow")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $event->sheet->getDelegate()->getCell("J$lastrow")->setValue("=SUM(J10:J$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("J$lastrow")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $event->sheet->getDelegate()->getCell("K$lastrow")->setValue("=SUM(K10:K$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("K$lastrow")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');

        $event->sheet->getDelegate()->getCell("L$lastrow")->setValue("=SUM(L10:L$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("L$lastrow")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $event->sheet->getDelegate()->getCell("M$lastrow")->setValue("=SUM(M10:M$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("M$lastrow")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');

        $event->sheet->getDelegate()->getCell("N$lastrow")->setValue("=SUM(N10:N$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("N$lastrow")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $event->sheet->getDelegate()->getCell("O$lastrow")->setValue("=SUM(O10:O$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("O$lastrow")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');

        $event->sheet->getDelegate()->getCell("P$lastrow")->setValue("=SUM(P10:P$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("P$lastrow")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $event->sheet->getDelegate()->getCell("Q$lastrow")->setValue("=SUM(Q10:Q$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("Q$lastrow")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');

        $event->sheet->getDelegate()->getCell("R$lastrow")->setValue("=SUM(R10:R$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("R$lastrow")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $event->sheet->getDelegate()->getCell("S$lastrow")->setValue("=SUM(S10:S$lastCalculate)");
        $event->sheet->getDelegate()->getStyle("S$lastrow")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');
      }
    ];
  }
}