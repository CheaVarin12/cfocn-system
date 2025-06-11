<?php

namespace App\Imports;

use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;

class PACImport implements ToCollection, WithGroupedHeadingRow
{
    use Importable;

    public $message = null;
    public $arrColumnNames = [
        'pac_number', 'po_lo_number', 'customer_id', 'project_id', 'service_type_id', 'issue_date',
        'type', 'qty_cores', 'length', 'status',
    ];
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
            if ($row['pac_number'] != '') {
                Purchase::create([
                    'pac_number'    => $row['pac_number'],
                    'po_number'     => $row['po_lo_number'],
                    'customer_id'   => $row['customer_id'],
                    'project_id'    => $row['project_id'],
                    'type_id'       => $row['service_type_id'],
                    'issue_date'    => $row['issue_date'] ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['issue_date']))->format('Y-m-d') : null,
                    'pac_type'      => $row['type'],
                    'cores'         => $row['qty_cores'],
                    'length'        => $row['length'],
                    'status'        => $row['status'] == 'Active' ? 1 : 2,
                    'user_id'       => Auth::id(),
                ]);
            }
        }
    }
}
