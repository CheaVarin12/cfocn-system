<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Project;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\revenueReportExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CreditNote;
use Exception;
use App\Models\LicenseFee;


class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:report-customer-view', ['only' => ['indexCustomer']]);
        $this->middleware('permission:report-income-view', ['only' => ['indexIncome']]);
        $this->middleware('permission:report-sale-journal-view', ['only' => ['indexSaleReport']]);
    }
    protected $layout = 'admin::pages.report.';
    
    public function indexRevenue(Request $req)
    {

        Log::info("Start: Admin/ReportController > indexRevenue | admin: " . $req);
        try {
            $data['year'] = $req->year ? $req->year : now()->year;
            $data['lease'] = [];
            $data['sale'] = [];
            $data['service'] = [];
            $data['creditNote'] = CreditNote::where(DB::raw('YEAR(issue_date)'), '=', $data['year'])->where('status', 1)->get();
            $data['licenseFee'] = LicenseFee::where([
                'year' => $data['year'],
                'status' => 1,
            ])->get();
            $invoices = Invoice::where(function ($q) use ($req, $data) {
                //$q->where(DB::raw('YEAR(issue_date)'), '=', $data['year']);
                if ($req->form_date && $req->to_date) {
                    $q->whereDate('issue_date', '>=', $req->form_date);
                    $q->whereDate('issue_date', '<=', $req->to_date);
                }
            })->get();
            foreach ($invoices as $invoice) {
                if ($invoice->purchase->type_id == 1) {
                    array_push($data['lease'], $invoice);
                } elseif ($invoice->purchase->type_id == 2) {
                    array_push($data['sale'], $invoice);
                } else {
                    array_push($data['service'], $invoice);
                }
            }

            $query = Project::where('status', 1);
            $search = $req->search ? $req->search : '';
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })->orderBy('name', 'asc')->get();
            return Excel::download(new revenueReportExport($data), 'DMC_CFOCN_Revenue.xlsx');
        } catch (Exception $error) {
            Log::error("Error: Admin/ReportController > indexRevenue | message: " . $error->getMessage());
        }
    }

}
