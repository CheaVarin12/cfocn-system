<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerInfoController extends Controller
{
    protected $layout = 'admin::pages.report.customer_info.';

    public function __construct()
    {
        $this->middleware('permission:report-cfocn-customer-view', ['only' => ['index']]);
    }

    public function index(Request $req)
    {

        $startDate = Carbon::now();
        $dateTime = $startDate->toDate()->format('YmdHis');

        $from_date = $req->from_date ? $req->from_date : $startDate->firstOfMonth()->toDate()->format('Y-m-d');
        $to_date =  $req->to_date ? $req->to_date : $startDate->lastOfMonth()->toDate()->format('Y-m-d');
        $reqField = (object) $req->all();
        $reqField->from_date = $from_date;
        $reqField->to_date = $to_date;

        $data['data'] = $this->queryData($reqField);
        $data['from_date'] = $from_date;
        $data['to_date']   = $to_date;
        // dd($data);
        //$nameExport = $dateTime . ' ' . 'customer_info.xlsx';
        $nameExport = "customer_info_" . $from_date . "_" . $to_date . '.xlsx';
        if ($req->check == "export") {
            // return Excel::download(new CustomerReportExport($data), $nameExport);
        }
        return view($this->layout . 'index', $data);
    }
    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }
    public function queryData($req)
    {
        $queryFilter = Customer::where(function ($query) use ($req) {
            if (isset($req?->search) && $req?->search) {
                $query->where('customer_code', 'like', '%' . $req->search . '%');
                $query->orWhere('name_en', 'like', '%' . $req->search . '%');
                $query->orWhere('name_kh', 'like', '%' . $req->search . '%');
            }
            if ($req->from_date && $req->to_date) {
                $query->whereDate('register_date', '>=', $req->from_date);
                $query->whereDate('register_date', '<=', $req->to_date);
            }
        })->orderBy('id', 'asc')->get();



        return $queryFilter;
    }
}
