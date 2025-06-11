<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\TotalARDetailReportExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ARAcgingReportController extends Controller
{

    protected $layout = 'admin::pages.report.ar_acging.';
    public function __construct()
    {
        $this->middleware('permission:report-ar-acging-view', ['only' => ['index']]);
    }
    public function index(Request $req)
    {
        $startDate = Carbon::now();
        $from_date = $req->from_date ? $req->from_date : $startDate->firstOfMonth()->toDate()->format('Y-m-d');
        $to_date =  $req->to_date ? $req->to_date : $startDate->lastOfMonth()->toDate()->format('Y-m-d');
        $reqField = (object) $req->all();
        $reqField->from_date = $from_date;
        $reqField->to_date = $to_date;
        $data = $this->queryData($reqField);
        if ($req->check == "export") {
            $nameExport = "ar_report_" . $from_date . "_" . $to_date . '.xlsx';
            return Excel::download(new TotalARDetailReportExport($data), $nameExport);
        }
        return view($this->layout . 'index', $data);
    }
    public function queryData($req)
    {
        $totalReceipt = 0;
        $totalAmount = 0;
        $paidAmount = 0;
        $totalLength = 0;
        $totalCoreKm = 0;
        $totalRemainingAmount = 0;


        $queryData = Customer::where('status', 1);
        $queryFilter = $queryData->where(function ($query) use ($req) {
            if (isset($req?->search) && $req?->search) {
                $query->where('name_en', 'like', '%' . $req->search . '%');
                $query->OrWhere('customer_code', 'like', '%' . $req->search . '%');
            }
            if ($req->from_date && $req->to_date) {
                $query->whereDate('register_date', '>=', $req->from_date);
                $query->whereDate('register_date', '<=', $req->to_date);
            }
        })->orderBy('id', 'asc')->get();
        // ->limit(5)
        if (count($queryFilter) > 0) {
            foreach ($queryFilter as $index => $item) {
                $item->purchase_totalLength = 0;
                $item->purchase_core = 0;
                $item->purchase_totalCoreKm = 0;
                $item->purchase_totalPrice = 0;
                $item->purchase_paidAmount = 0;

                //invoice
                $item->invoice_totalAmount = 0;
                $item->invoice_receipt_paid_amount = 0;

                if (isset($item->purchase)) {
                    foreach ($item->purchase as $val) {
                        $item->purchase_totalLength += $val->length ?? 0;
                        $item->purchase_totalCoreKm += ($val->length ?? 0) * ($val->cores ?? 0);

                        $item->purchase_core += $val->cores ?? 0;
                        $item->purchase_totalPrice += $val->total_price ?? 0;

                        $totalLength += $item->purchase_totalLength;
                        $totalCoreKm += $item->purchase_totalCoreKm;
                    }
                }
                if (isset($item->invoice)) {
                    foreach ($item->invoice as $val) {
                        $item->invoice_totalAmount += $val->total_grand;
                        $totalAmount += $val->total_grand;
                        if (isset($val->receipt) && count($val->receipt) > 0) {
                            foreach ($val->receipt as $rep) {
                                $item->invoice_receipt_paid_amount += $rep->paid_amount;
                                $paidAmount += $rep->paid_amount;
                            }
                        }
                    }
                }
                $totalRemainingAmount += $totalAmount - $paidAmount;
            }
        }
        $item = [
            "from_date" => $req->from_date,
            "to_date"   => $req->to_date,
            "data"  => $queryFilter,
            "totalReceipt" => $totalReceipt,
            "totalAmount" => $totalAmount,
            "paidAmount" => $paidAmount,
            "totalLength" => $totalLength,
            "totalCoreKm" => $totalCoreKm,
            'totalRemainingAmount' => $totalRemainingAmount
        ];
        return $item;
    }
}
