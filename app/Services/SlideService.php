<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Slide;
use Illuminate\Support\Facades\Auth;

class SlideService
{
    public function create($request)
    {
        $item = $request->all();
       // $item["name"] = $request->name;
        Slide::create($item);
    }
    public function update($data, $request)
    {
        $item = $request->all();
        //$item["name"] = json_encode($request->name);
        $data->update($item);
    }
    public function sumLevel()
    {
        $item = Slide::orderBy('ordering', 'desc')->first();
        return isset($item->ordering) ? (int)$item->ordering + 1 : 1;
    }
    public function invoiceAuto(){
        // $invoice_number = "N-01";
        // $invoice_last = DB::table('invoices')->orderBy('id', 'DESC')->first();
        // if($invoice_last){
        //     $dataInvoice = explode( '-', $invoice_last->invoice_number );
        //     $number = (int) $dataInvoice[1];
        //     $invoice_number = "N-0" . ($number + 1);
        // }
    }
}
