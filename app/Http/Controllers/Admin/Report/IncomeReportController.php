<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\IncomeReportExport;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class IncomeReportController extends Controller
{
    protected $layout = 'admin::pages.report.income.';
    public function __construct()
    {
        $this->middleware('permission:report-income-view', ['only' => ['index']]);
    }
    public function index(Request $req)
    {
        $startDate = Carbon::now();
        $from_date = $req->from_date ? $req->from_date : $startDate->firstOfMonth()->toDate()->format('Y-m-d');
        $to_date =  $req->to_date ? $req->to_date : $startDate->lastOfMonth()->toDate()->format('Y-m-d');
        $reqField = (object) $req->all();
        $reqField->search = $req->search;
        $reqField->from_date = $from_date;
        $reqField->to_date = $to_date;
        $data = $this->queryData($reqField);
        $data['projects'] = Project::where('status', 1)->get();
        if ($req->check == "export") {
            $nameExport = "report_income_" . $from_date . "_" . $to_date . '.xlsx';
            return Excel::download(new IncomeReportExport($data), $nameExport);
        }
        return view($this->layout . 'index', $data);
    }
    public function queryData($req)
    {
        $query = Receipt::query();
        $queryFilter = $query->where(function ($query) use ($req) {
            if ($req->from_date && $req->to_date) {
                $query->whereDate('created_at', '>=', $req->from_date);
                $query->whereDate('created_at', '<=', $req->to_date);
            }
            if ($req->search) {
                $query->whereHas(
                    'customer',
                    function ($q) use ($req) {
                        $q->where('customer_code', 'like', '%' . $req->search . '%');
                        $q->orWhere('name_en', 'like', '%' . $req->search . '%');
                        $q->orWhere('name_kh', 'like', '%' . $req->search . '%');
                    }
                );
            }
        })->get();
        $item = [
            "from_date" => $req->from_date,
            "to_date"   => $req->to_date,
            "data"  => $queryFilter,
        ];
        return $item;
    }
}
