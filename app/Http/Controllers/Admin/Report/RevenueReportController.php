<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\TotalARDetailReportExport;
use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\LicenseFee;
use App\Models\Project;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\revenueReportExport;

class RevenueReportController extends Controller
{
    protected $layout = 'admin::pages.report.revenue.';
    public function __construct()
    {
        $this->middleware('permission:report-revenue-view', ['only' => ['index']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/RevenueReportController > index | admin: ");
        try {
            $startDate = Carbon::now();
            $from_date = $req->from_date ? $req->from_date : $startDate->firstOfMonth()->toDate()->format('Y-m-d');
            $to_date =  $req->to_date ? $req->to_date : $startDate->lastOfMonth()->toDate()->format('Y-m-d');
            $reqField = (object) $req->all();
            $reqField->from_date = $from_date;
            $reqField->to_date = $to_date;

            $data['data'] = $this->queryData($reqField);
            $data['from_date'] = $from_date;
            $data['to_date'] = $to_date;
            return view($this->layout . 'index', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/RevenueReportController > index | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    function fetchData(Request $req)
    {
        $data = $this->queryData($req);
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function queryData($req)
    {
        $data['year'] = isset($req->year) && $req->year ? $req->year : now()->year;
        $data = Invoice::with([
            'purchase' => function ($q) use ($req) {
                $q->with(["project" => function ($q) use ($req) {
                    $q->with(["licenseFee" => function ($q) use ($req) {
                        $q->where('status', 1);
                    }]);
                }]);
            }
        ])->where(function ($q) use ($req, $data) {
            if ($req->from_date && $req->to_date) {

                $q->whereDate('issue_date', '>=', $req->from_date);
                $q->whereDate('issue_date', '<=', $req->to_date);
            }
            $q->whereHas('purchase', function ($q) use ($req) {
                if (isset($req->project_id) && $req->project_id) {
                    $q->where('project_id', $req->project_id);
                }
            });
        })->orderBy('id', 'desc')->get();

        return $this->formatDataByProject($data);
    }
    public function formatDataByProject($data)
    {
        $dataItemProject = [];

        $getData = $this->groupByProjectWithPurchaseType($data);

        foreach ($getData as $index => $val) {
            foreach ($val as $indexVal1 => $val1) {
                $licenseFeePercentage = $val1[0]?->purchase?->project?->licenseFee?->percentage ?? 0;
                $ProjectItem = $val1[0]?->purchase?->project ?? null;
                $amount = 0;
                $licenseFeeAmount = 0;
                foreach ($val1 as $val2) {
                    $amount += $val2->total_grand;
                }
                $licenseFeeAmount = $amount * ($licenseFeePercentage / 100);

                array_push($dataItemProject, (object)[
                    "project_parent_id" => $index,
                    "ProjectItem"   => $ProjectItem,
                    "project_child_id"    => $indexVal1,
                    "name_project_child" => null,
                    "amount" => $amount,
                    "licenseFeePercentage"    => $licenseFeePercentage,
                    "licenseFeeAmount"    => $licenseFeeAmount,
                ]);
            }
        }
        //shopSort
        usort($dataItemProject, function ($a, $b) {
            return strcasecmp($a->project_parent_id, $b->project_parent_id);
        });
        $dataItemProject = $this->groupByProject($dataItemProject);

        return $dataItemProject;
    }
    public function groupByProject($data)
    {
        $itemProject = collect([
            "1" => (object)[
                "project_parent_id" => null,
                "project_child_id"    => 1,
                "name_project_child" => "Lease Income",
                "amount" => 0,
                "licenseFeePercentage"    => 0,
                "licenseFeeAmount"    => 0,
            ],
            "2" => (object)[
                "project_parent_id" => null,
                "project_child_id"    => 2,
                "name_project_child" => "Sale Income",
                "amount" => 0,
                "licenseFeePercentage"    => 0,
                "licenseFeeAmount"    => 0,
            ],
            "3" => (object)[
                "project_parent_id" => null,
                "project_child_id"    => 3,
                "name_project_child" => "Service Income",
                "amount" => 0,
                "licenseFeePercentage"    => 0,
                "licenseFeeAmount"    => 0,
            ]
        ]);
        $getData = [];
        foreach ($data as $index => $item) {
            if (isset($item->project_parent_id)) {
                $getData[$item->project_parent_id][] = $item;

                //shopSort
                usort($getData[$item->project_parent_id], function ($a, $b) {
                    return strcasecmp($a->project_child_id, $b->project_child_id);
                });
            }
        }
        $formatGetData = [];
        foreach ($getData as $index => $val) {
            $formatGetData[$index]['dataDB'] = $val;
            $formatGetData[$index]['dataDefault'] = array(...$itemProject);
        }

        return array(...$formatGetData);
    }
    public function groupByProjectWithPurchaseType($data)
    {
        $getData = [];
        foreach ($data as $index => $item) {
            if (isset($item->purchase->project_id)) {
                if ($item->purchase->type_id == 1) {
                    $getData[$item->purchase->project_id][$item->purchase->type_id][] = $item;
                } else if ($item->purchase->type_id == 2) {
                    $getData[$item->purchase->project_id][$item->purchase->type_id][] = $item;
                } else
                    $getData[$item->purchase->project_id][3][] = $item;
            }
        }
        return $getData;
    }
    public function excel(Request $req)
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
