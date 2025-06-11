<?php

namespace App\Exports\report;

use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Fill;

\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class SaleJournalExport implements FromView, WithEvents
{
  public $invoice;
  public $projectsInExport;
  public $rate;
  public $date;
  public $totalSaleTax;
  public $totalSaleNotTax;
  public $totalSaleUser;
  public $total_grand;

  public function __construct($data)
  {
    $this->invoice = $data['data'];
    $this->projectsInExport = $data['projectInExport'];
    $this->rate = $data['rate'];
    $this->date = (object) $data['date'];
    $this->totalSaleTax = $data['totalSaleTax'];
    $this->totalSaleNotTax = $data['totalSaleNotTax'];
    $this->totalSaleUser = $data['totalSaleUser'];
    $this->total_grand = $data['total_grand'];
  }

  public function view(): view
  {
    return view("admin::pages.report.sale-journal.excel", [
      'invoice' => $this->invoice,
      'projectInExport' => $this->projectsInExport,
      'rate' => $this->rate,
      'date' => $this->date,
      'totalSaleTax' => $this->totalSaleTax,
      'totalSaleNotTax' => $this->totalSaleNotTax,
      'totalSaleUser' => $this->totalSaleUser,
      'total_grand' => $this->total_grand,
    ]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet = $event->sheet;
        $getDelegate = $event->sheet->getDelegate();

        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
        $getDelegate->getSheetView()->setZoomScale(85);
        //print fit column
        $getDelegate->getPageSetup()->setFitToWidth(1);
        $getDelegate->getPageSetup()->setFitToHeight(0);

        //set width column
        for ($i = 0; $i < count($columns); $i++) {
          $getDelegate->getColumnDimension($columns[$i])->setWidth(15);
          if ($i == 2 || $i == 4) {
            $getDelegate->getColumnDimension($columns[$i])->setWidth(33.6);
          }
          if ($i == 3) {
            $getDelegate->getColumnDimension($columns[$i])->setWidth(19.11);
          }
        }
        $event->sheet->setBreak('A22', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

        //set height head
        $getDelegate->getRowDimension('1')->setRowHeight(35);
        $getDelegate->getRowDimension('2')->setRowHeight(31.1);
        $getDelegate->getRowDimension('3')->setRowHeight(26.3);
        $getDelegate->getRowDimension('4')->setRowHeight(21);
        $getDelegate->getRowDimension('5')->setRowHeight(21);
        $getDelegate->getRowDimension('6')->setRowHeight(43.5);
        $getDelegate->getRowDimension('7')->setRowHeight(25.5);
        $getDelegate->getRowDimension('8')->setRowHeight(43.5);
        $getDelegate->getRowDimension('9')->setRowHeight(18.8);

        $lastRow = 10 + count($this->invoice);
        $getDelegate->getRowDimension("$lastRow")->setRowHeight(35.3);

        // Freezing row column
        $getDelegate->freezePane('C10');

        $khmerOs = array(
          'name' => 'Khmer OS',
          'size' => "12",
        );

        $font12 = array('size' => "12");

        // set font and font size to head
        $sheet->styleCells("D1", ['font' => [...$khmerOs, 'size' => '14']]);

        $sheet->styleCells("A2", ['font' => $khmerOs]);
        $sheet->styleCells("D2", ['font' => $khmerOs]);
        $sheet->styleCells("A3", ['font' => $khmerOs]);
        $sheet->styleCells("D3", ['font' => $khmerOs]);
        $sheet->styleCells("A4", ['font' => $khmerOs]);
        $sheet->styleCells("D4", ['font' => $khmerOs]);
        $sheet->styleCells("A5", ['font' => $khmerOs]);
        $sheet->styleCells("N5", ['font' => $khmerOs]);

        //set font and font size head
        $sheet->styleCells("A6:T8", ['font' => [...$khmerOs, 'size' => 10]]);

        //set font size head
        $sheet->styleCells("A7", ['font' => $font12]);
        $sheet->styleCells("B7", ['font' => $font12]);
        $sheet->styleCells("C7", ['font' => $font12]);
        $sheet->styleCells("A6", ['font' => $font12]);
        $sheet->styleCells("E7", ['font' => $font12]);

        $sheet->styleCells("A9:T9", [
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
              $event->sheet->styleCells($col, ['font' => [...$khmerOs, 'name' => 'Calibri']]);
            } elseif ($j == 4) {
              $event->sheet->styleCells($col, ['font' => $khmerOs]);
            } else {
              $sheet->styleCells($col, ['font' => [...$khmerOs, 'name' => 'Times New Roman']]);
            }
          }
        }

        $sheet->styleCells("A$lastRow:F$lastRow", ['font' => [...$khmerOs, 'size' => '11']]);
        $sheet->styleCells("G$lastRow:T$lastRow", ['font' => ['name' => 'Times New Roman', 'size' => '11']]);

        // background gree
        $getDelegate->getStyle('A6:T9')
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('92D050');

        foreach ($this->invoice as $indexVal => $val) {
          $index = 10 + $indexVal;
          if ($val->type_invoice == "credit_note") {
            $sheet->styleCells("A$index:T$index", [
              'font' => array(
                // 'color' => ['argb' => 'eee6e6']
                'color' => ['argb' => 'f93154']
              ),
              // 'fill' => [
              //   'fillType'   => Fill::FILL_SOLID,
              //   'startColor' => ['argb' => 'c85858'],
              // ],
            ]);
          }
        }

        $getDelegate->getStyle("G$lastRow:T$lastRow")
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('92D050');


        // setWrapText
        for ($i = 1; $i <= 9; $i++) {
          if ($i == 2 || $i >= 6) {
            $getDelegate->getStyle("A$i:T$i")->getAlignment()->setWrapText(true);
          }
        }

        // set filter
        $getDelegate->setAutoFilter('A8:' . $getDelegate->getHighestColumn() . '8');


        // set bolder
        $event->sheet->getStyle("A6:T$lastRow")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        //border head none
        $borderSet = [
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              'color' => ['argb' => 'FFFFFF'],
            ]
          ]
        ];
        $getDelegate->getStyle("A1:M5")->applyFromArray($borderSet);


        //Text style
        $alignmentAllCenter = [
          'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
          'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ];
        $verticalCenter = [
          'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ];

        foreach ($columns as $colI => $colItem) {
          $countInvoice = count($this->invoice) + 1;
          $sheet->styleCells($colItem . 1 . ':' . $colItem . $countInvoice, ['alignment' => $verticalCenter]);
          $sheet->styleCells($colItem . 6 . ':' . $colItem . 9, ['alignment' => $alignmentAllCenter]);

          if ($colI >= 6 && $colI <= 18) {
            $operatorCal = $colI % 2;
            $type = '"$"* #,##0.00';
            if ($operatorCal == 0) {
              $type = '"R"* #,##0';
            }
            $this->formatCurrency($getDelegate, $colItem, $type);
          }
          if ($colI == 19) {
            $this->formatCurrency($getDelegate, $colItem, '"R"* #,##0');
          }
          if ($colI == 6) {
            $this->formatCurrency($getDelegate, $colItem, '"$"* #,##0.00');
          }
          if ($colI == 7) {
            $this->formatCurrency($getDelegate, $colItem, '"R"* #,##0');
          }
        }
        foreach ($this->invoice as $i => $iv) {
          $index = $i + 10;

          $colUs = ['J', 'L', 'N', 'P', 'R'];
          $culKh = ['K', 'M', 'O', 'Q', 'S'];
          if ($iv->type_invoice == "credit_note") {
           
            foreach ($colUs as $us) {
              $this->formatCurrency($getDelegate, $us.$index, '"$"* -#,##0.00');
            }
            foreach ($culKh as $kh) {
              $this->formatCurrency($getDelegate, $kh.$index, '"R"* -#.##0');
            }
          }
        }
        $sheet->styleCells("G$lastRow:T$lastRow", ['alignment' => $alignmentAllCenter]);
      }
    ];
  }
  public function formatCurrency($getDelegate, $colItem, $type)
  {
    return $getDelegate->getStyle($colItem)->getNumberFormat()->setFormatCode($type);
  }
}
