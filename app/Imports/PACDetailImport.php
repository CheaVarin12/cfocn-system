<?php

namespace App\Imports;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Service;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;

class PACDetailImport implements ToCollection, WithGroupedHeadingRow
{
    use Importable;

    public $message = null;
    public $arrColumnNames = ['pac_id', 'service_id', 'description', 'qty', 'uom', 'unit_price', 'amount'];
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            foreach ($this->arrColumnNames as $column) {
                if (!array_key_exists($column, $rows->toArray()[0])) {
                    $this->message = 'invalid_column';
                    return;
                }
            }
            if ($row['pac_id'] != '') {
                PurchaseDetail::create([
                    'purchase_id' => $row['pac_id'],
                    'service_id' => $row['service_id'],
                    'name' => Service::find($row['service_id'])?->name,
                    'des' => $row['description'],
                    'qty' => $row['qty'],
                    'price' => $row['unit_price'],
                    'uom' => $row['uom'],
                    'amount' => $row['amount'],
                ]);
                
                $pac = Purchase::where('id', $row['pac_id'])->first();
                if ($pac) {
                    $pac->update(['total_price' => $rows->sum('unit_price'), 'total_qty' => $rows->sum('qty')]);
                }
            }
        }
    }
}
