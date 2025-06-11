<?php

namespace App\Exports;

use Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

\PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());
Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});


class InvoiceDetailOneRateExport implements FromView, WithEvents
{
  use Exportable;
  public $invoice;
  public $purchase_detail;
  public $invoice_detail;
  public $customer;
  public $contact;
  public $rate;
  public $day_in_month;
  public $numberToWords;
  public function __construct($data)
  {
    $this->invoice = $data['invoice'];
    $this->purchase_detail = $data['purchase_detail'];
    $this->invoice_detail = $data['invoice_detail'];
    $this->customer = $data['customer'];
    $this->contact = $data['contact'];
    $this->day_in_month = $data['day_in_month'];
    $this->rate = $data['rate'];
    $this->numberToWords = $data['numberToWords'];
  }
  public function view(): view
  {

    return view("admin::pages.invoice.invoice-one-rate-excel", [
      'invoice' => $this->invoice,
      'purchase_detail' => $this->purchase_detail,
      'invoice_detail' => $this->invoice_detail,
      'customer' => $this->customer,
      'contact' => $this->contact,
      'rate' => $this->rate,
      'numberToWords' => $this->numberToWords,
    ]);
  }
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $columns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        $event->sheet->getDelegate()->getColumnDimension('A')->setVisible(false);
        $event->sheet->getDelegate()->getColumnDimension('J')->setVisible(false);
        $event->sheet->getDelegate()->getSheetView()->setZoomScale(140);
        //print fit column
        $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
        $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
        //text wrap
        for ($i = 1; $i <= 30 + count($this->invoice_detail); $i++) {
          for ($j = 0; $j < count($columns); $j++) {
            $col = "$columns[$j]$i";
            $event->sheet->getDelegate()->getStyle($col)->getAlignment()->setWrapText(true);
          }
        }
        //head
        $heads = [
          ['height' => '', 'font' => '', 'size' => '', 'bold' => ''],
          ['height' => 37.5, 'font' => 'Khmer OS Siemreap', 'size' => 13, 'bold' => true],
          ['height' => 13.2, 'font' => 'Times New Roman', 'size' => 10, 'bold' => true],
          ['height' => 15.9, 'font' => 'Khmer OS', 'size' => 8, 'bold' => false],
          ['height' => 15.9, 'font' => 'Khmer OS', 'size' => 8, 'bold' => false],
          ['height' => 13.2, 'font' => 'Calibri', 'size' => 7, 'bold' => false],
          ['height' => 15.9, 'font' => 'Khmer OS', 'size' => 8, 'bold' => false],
          ['height' => 13.2, 'font' => 'Calibri', 'size' => 7, 'bold' => false],
          ['height' => 18.8, 'font' => 'Khmer OS Battambang', 'size' => 10, 'bold' => true],
          ['height' => 13.8, 'font' => 'Times New Roman', 'size' => 8, 'bold' => true],
        ];

        for ($i = 1; $i <= 9; $i++) {
          $row = "B$i:I$i";
          $event->sheet->styleCells($row, [
            'font' => array(
              'name' => $heads[$i]['font'],
              'size' => $heads[$i]['size'],
              'bold' => $heads[$i]['bold']
            ),
          ]);
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight($heads[$i]['height']);
          $event->sheet->getDelegate()->getStyle($row)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // head in table
  
        $head_in_tbl_ls = [
          ['height' => '', 'font' => '', 'size' => ''],
          ['height' => 15, 'font' => 'Khmer OS'],
          ['height' => 12.9, 'font' => 'Calibri'],
          ['height' => 12.9, 'font' => 'Khmer OS'],
          ['height' => 15.8, 'font' => 'Calibri'],
          ['height' => 14.1, 'font' => 'Khmer OS'],
          ['height' => 15, 'font' => 'Calibri'],
          ['height' => 15.9, 'font' => 'Khmer OS'],
        ];

        for ($i = 10; $i <= 16; $i++) {
          $row = "B$i:D$i";
          $event->sheet->styleCells($row, [
            'font' => array(
              'name' => $head_in_tbl_ls[$i - 9]['font'],
              'size' => 7,
            ),
          ]);
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight($head_in_tbl_ls[$i - 9]['height']);
        }


        $head_in_tbl_rs = [
          ['height' => '', 'font' => '', 'bold' => ''],
          ['height' => 15, 'font' => 'Khmer OS', 'bold' => false],
          ['height' => 12.9, 'font' => 'Khmer OS', 'bold' => false],
          ['height' => 12.9, 'font' => 'Khmer OS', 'bold' => false],
          ['height' => 15.8, 'font' => 'Khmer OS', 'bold' => true],
          ['height' => 14.1, 'font' => 'Calibri', 'bold' => true],
          ['height' => 15, 'font' => 'Calibri', 'bold' => true],
          ['height' => 15.9, 'font' => 'Calibri', 'bold' => true],
        ];

        for ($i = 10; $i <= 16; $i++) {
          $row = "E$i:I$i";
          $event->sheet->styleCells($row, [
            'font' => array(
              'name' => $head_in_tbl_rs[$i - 9]['font'],
              'size' => 7,
              'bold' => $head_in_tbl_rs[$i - 9]['bold'],
            ),
          ]);
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight($head_in_tbl_rs[$i - 9]['height']);
        }

        //head list
        $event->sheet->styleCells("B17:I17", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "7",
            'bold' => true
          ),
        ]);
        $event->sheet->getDelegate()->getRowDimension('17')->setRowHeight(16.5);

        $event->sheet->styleCells("B18:I18", [
          'font' => array(
            'name' => 'Calibri',
            'size' => "8",
            'bold' => true
          ),
        ]);
        $event->sheet->getDelegate()->getRowDimension('18')->setRowHeight(13.8);

        $event->sheet->getDelegate()->getStyle('B17:I18')
          ->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('c4d69b');


        //list
  
        $event->sheet->styleCells("B19:I19", [
          'font' => array(
            'name' => 'Calibri',
            'size' => "8",
            'bold' => true
          ),
        ]);
        $event->sheet->getDelegate()->getRowDimension('19')->setRowHeight(13.2);

        $number_of_p_detail = $this->invoice_detail->count();
        for ($i = 19; $i <= 19 + $number_of_p_detail; $i++) {
          for ($j = 0; $j < count($columns); $j++) {
            if ($j == 1 || $j == 2) {
              $event->sheet->styleCells($columns[$j] . $i, [
                'font' => array(
                  'name' => 'Khmer OS Siemreap',
                  'size' => "8",
                ),
              ]);
            } else {
              $event->sheet->styleCells($columns[$j] . $i, [
                'font' => array(
                  'name' => 'Calibri',
                  'size' => "8",
                ),
              ]);
            }

          }
        }

        for ($i = 0; $i < count($columns); $i++) {
          if ($i == 0) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(3.70);
          } elseif ($i == 1) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(18.81);
          } elseif ($i == 2) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(33.03);
          } elseif ($i == 3) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(11.84);
          } elseif ($i == 4) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(11.25);
          } elseif ($i == 5) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(8.7);
          } elseif ($i == 6) {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(11.03);
          } else {
            $event->sheet->getDelegate()->getColumnDimension($columns[$i])->setWidth(14);
          }
        }

        for ($i = 20 + count($this->invoice_detail); $i <= 23 + count($this->invoice_detail); $i++) {
          $event->sheet->styleCells("B$i:I$i", [
            'font' => array(
              'name' => 'Calibri',
              'size' => "8",
            ),
          ]);
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(13.2);
        }

        //result
        for ($i = 24 + count($this->invoice_detail); $i <= 26 + count($this->invoice_detail); $i++) {
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(29.3);
        }

        $row_re = 24 + count($this->invoice_detail);
        $event->sheet->styleCells("B$row_re:D$row_re", [
          'font' => array(
            'name' => 'Calibri',
            'size' => "8",
            'bold' => true
          ),
        ]);
        $event->sheet->styleCells("F$row_re:G$row_re", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "6",
            'bold' => true
          ),
        ]);
        $event->sheet->styleCells("H$row_re:I$row_re", [
          'font' => array(
            'name' => 'Calibri',
            'size' => "9",
            'bold' => true
          ),
        ]);
        $event->sheet->getDelegate()->getRowDimension($row_re)->setRowHeight(29.3);

        $row_re = $row_re + 1;
        $event->sheet->styleCells("B$row_re:E$row_re", [
          'font' => array(
            'name' => 'Calibri',
            'size' => "8",
            'bold' => true
          ),
        ]);
        $event->sheet->styleCells("F$row_re:G$row_re", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "6",
            'bold' => true
          ),
        ]);
        $event->sheet->styleCells("H$row_re:I$row_re", [
          'font' => array(
            'name' => 'Calibri',
            'size' => "9",
            'bold' => true
          ),
        ]);
        $event->sheet->getDelegate()->getRowDimension($row_re)->setRowHeight(29.3);

        $row_re = $row_re + 1;
        $event->sheet->styleCells("B$row_re:E$row_re", [
          'font' => array(
            'name' => 'Khmer OS Siemreap',
            'size' => "8",
            'bold' => true
          ),
        ]);
        $event->sheet->styleCells("F$row_re:G$row_re", [
          'font' => array(
            'name' => 'Khmer OS',
            'size' => "6",
            'bold' => true
          ),
        ]);
        $event->sheet->styleCells("H$row_re:I$row_re", [
          'font' => array(
            'name' => 'Calibri',
            'size' => "9",
            'bold' => true
          ),
        ]);
        $event->sheet->getDelegate()->getRowDimension($row_re)->setRowHeight(29.3);

        ///footer
        $row_foot = 27 + count($this->invoice_detail);
        for ($i; $i <= $row_foot + 4; $i++) {
          $event->sheet->styleCells("B$i:I$i", [
            'font' => array(
              'name' => 'Times New Roman',
              'size' => "7",
            ),
          ]);
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(12);
        }

        $row_foot += 4;
        $event->sheet->getDelegate()->getRowDimension($row_foot)->setRowHeight(51);

        $foot2 = [
          ['height' => '', 'font' => '', 'size' => ''],
          ['height' => 18, 'font' => 'Khmer OS', 'size' => 8],
          ['height' => 18, 'font' => 'Calibri', 'size' => 7],
        ];
        $row_foot += 1;
        for ($i = $row_foot; $i <= $row_foot + 2; $i++) {
          $row = "B$i:I$i";
          $event->sheet->styleCells($row, [
            'font' => array(
              'name' => $head_in_tbl_ls[$i - ($row_foot - 1)]['font'],
              'size' => 7,
            ),
          ]);
          $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight($head_in_tbl_ls[$i - ($row_foot - 1)]['height']);
        }


        $day_in_month = $this->day_in_month;
        for ($i = 20; $i <= 19 + count($this->invoice_detail); $i++) {
          for ($j = 0; $j < count($columns); $j++) {
            if ($j == 5 || $j == 7) {
              // set format money of project detail
              $event->sheet->getDelegate()->getStyle($columns[$j] . $i)->getNumberFormat()
                ->setFormatCode('"$"* #,##0.00');
            }
            if ($j == 7) {
                $charge_number = $this->invoice->charge_number;
                if($this->invoice->charge_type == 'day') {
                  $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i/$day_in_month*$charge_number,2)");
                } elseif ($this->invoice->charge_type == 'month') {
                  if($this->invoice){
                    if($this->invoice->InvoiceDetail[$i-20]->rate_first){
                      $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i*H$i/12*$charge_number,2)");
                     }elseif($this->invoice->InvoiceDetail[$i-20]->rate_second){
                      $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i*H$i/12*$charge_number,2)");
                     }
                     else{
                      $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i/12*$charge_number,2)");
                     }
                  }
                } elseif($this->invoice->charge_type == 'quarter'){
                  if($this->invoice->InvoiceDetail[$i-20]->rate_first){
                    $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i*H$i/$charge_number,2)");
                  }
                  elseif($this->invoice->InvoiceDetail[$i-20]->rate_second){
                    $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i*H$i/$charge_number,2)");
                  }
                  else{
                    $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i/$charge_number,2)");
                  }
                }else{
                  $event->sheet->getDelegate()->getCell($columns[$j] . $i)->setValue("=ROUND(E$i*G$i*$charge_number,2)");
                }
            }
          }
        }
      
        $row_total = 24 + $this->invoice_detail->count();
        $end = 19 + $this->invoice_detail->count();
        $event->sheet->getDelegate()->getCell("H$row_total")->setValue("=SUM(I20:I$end)");
        $event->sheet->getDelegate()->getStyle("H$row_total")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $row_vat = 25 + $this->invoice_detail->count();
        $event->sheet->getDelegate()->getCell("H$row_vat")->setValue("=ROUND(H$row_total*10%,2)");
        $event->sheet->getDelegate()->getStyle("H$row_vat")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        $row_grand = 26 + $this->invoice_detail->count();
        $event->sheet->getDelegate()->getCell("H$row_grand")->setValue("=ROUND(H$row_total+H$row_vat,2)");
        $event->sheet->getDelegate()->getStyle("H$row_grand")->getNumberFormat()
          ->setFormatCode('"$"* #,##0.00');

        //khmer
        $rate = $this->rate->rate;
        $event->sheet->getDelegate()->getCell("I$row_grand")->setValue("=ROUND(H$row_grand*$rate,2)");
        $event->sheet->getDelegate()->getStyle("I$row_grand")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');

        $event->sheet->getDelegate()->getCell("I$row_total")->setValue("=ROUND(I$row_grand/1.1,2)");
        $event->sheet->getDelegate()->getStyle("I$row_total")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');

        $event->sheet->getDelegate()->getCell("I$row_vat")->setValue("=ROUND(I$row_grand-I$row_total,2)");
        $event->sheet->getDelegate()->getStyle("I$row_vat")->getNumberFormat()
          ->setFormatCode('"R"* #,##0');


        //bolder
  
        //bolder all
        $end = 26 + count($this->invoice_detail);
        $event->sheet->getStyle("B10:I$end")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
          ],
        ]);

        //outline
        $end = 26 + count($this->invoice_detail);
        $event->sheet->getStyle("B10:I$end")->applyFromArray([
          'borders' => [
            'outline' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
          ],
        ]);

        //head in table list
        $event->sheet->getStyle("B17:I18")->applyFromArray([
          'borders' => [
            'outline' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
          ],
        ]);

        $event->sheet->getStyle("B17:I18")->applyFromArray([
          'borders' => [
            'vertical' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
          ],
        ]);

        $event->sheet->getStyle("B17:I18")->applyFromArray([
          'borders' => [
            'horizontal' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
            ],
          ],
        ]);

        //bolder result
  
        $event->sheet->getStyle("B17:I18")->applyFromArray([
          'borders' => [
            'outline' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
          ],
        ]);
        $start_r = 24 + count($this->invoice_detail);
        $end_r = $start_r + 2;
        $event->sheet->getStyle("B$start_r:I$end_r")->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
          ],
        ]);

        $event->sheet->getStyle("B$start_r:E$end_r")->applyFromArray([
          'borders' => [
            'horizontal' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
            ],
          ],
        ]);
      }
    ];
  }
}