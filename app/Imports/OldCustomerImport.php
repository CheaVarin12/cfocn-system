<?php

namespace App\Imports;

use App\Models\OldCustomerInfo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;

class OldCustomerImport implements ToCollection, WithGroupedHeadingRow
{
    use Importable;

    public $message;
    public $arrColumNames = [
        'register_date', 'customer_code', 'customer_name', 'po_number', 'pac_number', 'customer_address',
        'service_type', 'description', 'type', 'qty_cores', 'length', 'status', 'inactive_date',
    ];
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            foreach ($this->arrColumNames as $column) {
                if (!array_key_exists($column, $rows->toArray()[0])) {
                    $this->message = 'invalid_column';
                    return;
                }
            }
            if ($row['register_date'] != '') {
                OldCustomerInfo::create([
                    'register_date' => $row['register_date'] ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['register_date']))->format('Y-m-d') : null,
                    'customer_code' => $row['customer_code'],
                    'customer_name' => $row['customer_name'],
                    'customer_address' => $row['customer_address'],
                    'po_number' => $row['po_number'],
                    'pac_number' => $row['pac_number'],
                    'service_type' => $row['service_type'],
                    'description' => $row['description'],
                    'type' => $row['type'],
                    'qty_cores' => $row['qty_cores'],
                    'length' => $row['length'],
                    'status' => $row['status'] == 'Active' ? 1 : 2,
                    'inactive_date' => $row['inactive_date'] ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['inactive_date']))->format('Y-m-d') : null,
                    'user_id'   => Auth::user()->id
                ]);
            }
        }
    }
}
