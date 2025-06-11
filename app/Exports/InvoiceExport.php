<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class InvoiceExport implements FromView 
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    

    public function view():view
    {
        
        return view("admin::export.excel_invoice",[
            'invoice' =>  $this->data
        ]);
    }
}
