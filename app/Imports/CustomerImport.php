<?php

namespace App\Imports;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;

class CustomerImport implements ToCollection, WithGroupedHeadingRow
{
    use Importable;

    public $message;
    public $arrColumnNames = [
        'register_date', 'customer_code', 'customer_name_en', 'customer_name_kh', 'phone', 'email',
        'vat_tin', 'address_en', 'address_kh'
    ];
    /**
    * @param Collection $collection
    */
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            foreach ($this->arrColumnNames as $column) {
                if (!array_key_exists($column, $rows->toArray()[0])) {
                    $this->message = 'invalid_column';
                    return;
                }
            }
            if ($row['register_date'] != '') {
                Customer::create([
                    'register_date' => $row['register_date'] ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['register_date']))->format('Y-m-d') : null,
                    'customer_code' => $row['customer_code'],
                    'name_en' => $row['customer_name_en'],
                    'name_kh' => $row['customer_name_kh'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'vat_tin' => $row['vat_tin'],
                    'address_en' => $row['address_en'],
                    'address_kh' => $row['address_kh'],
                    'status' => $row['status'] == 'Active' ? 1 : 2,
                    'user_id'   => Auth::user()->id
                ]);
            }
        }
    }
}
