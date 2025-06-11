<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FttxReportExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Fttx;
use App\Models\FttxDetail;
use App\Models\FttxPosSpeed;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class FttxReportController extends Controller
{
    protected $layout = 'admin::pages.fttx.report.';

    public function __construct()
    {
        $this->middleware('permission:fttx-annual-report-view', ['only' => ['index']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/FttxReportController > index | admin: " . $req);
        try {
            $today = Carbon::now()->format('d-M-Y');
            $startDate = Carbon::now();
            $currentYear = $startDate->year;
            $fromDate = $req->from_date
                ? $req->from_date
                : Carbon::create($currentYear, 1, 1)->format('Y-m-d');

            $toDate = $req->to_date
                ? $req->to_date
                : Carbon::create($currentYear, 12, 31)->format('Y-m-d');

            $fttxStatus = $req->fttx_status ? explode(",", $req->fttx_status) : [2];
            $posSpeedId = $req->pos_speed_id ? explode(",", $req->pos_speed_id) : '';
            $customerId = $req->customer_id ?? '';
            $posSpeeds = FttxPosSpeed::when($posSpeedId, function ($q) use ($posSpeedId) {
                $q->whereIn('id', $posSpeedId);
            })->where('status', 1)->get();
            $reportData = [];
            foreach ($posSpeeds as $posSpeed) {
                $totalAmountEachPosSpeedByMonth = collect([
                    'Jan' => 0,
                    'Feb' => 0,
                    'Mar' => 0,
                    'Apr' => 0,
                    'May' => 0,
                    'Jun' => 0,
                    'Jul' => 0,
                    'Aug' => 0,
                    'Sep' => 0,
                    'Oct' => 0,
                    'Nov' => 0,
                    'Dec' => 0,
                    'Total' => 0,
                ]);
                $posSpeedName = $posSpeed->split_pos;
                $reportData[$posSpeedName]['isp'] = [];
                $reportData[$posSpeedName]['total_amount'] = [];
                foreach ($posSpeed->fttx as $fttx) {
                    if ($fttx->status && in_array($fttx->status, $fttxStatus)) {
                        if ($fttx->customer) {
                            $reportData[$posSpeedName]['isp'][] = [
                                'id' => $fttx->customer->id,
                                'name_en' => $fttx->customer->name_en,
                                'name_kh' => $fttx->customer->name_kh,
                                'pos_speed_id' => $posSpeed->id,
                            ];
                        }
                    }
                }

                $uniqueCustomers = [];
                foreach ($reportData[$posSpeedName]['isp'] as $isp) {
                    $uniqueCustomers[$isp['id']] = $isp;
                }
                if ($customerId) {
                    $filteredCustomers = array_filter($uniqueCustomers, function ($customer) use ($customerId) {
                        return $customer['id'] == $customerId;
                    });
                    $reportData[$posSpeedName]['isp'] = array_values($filteredCustomers);
                } else {
                    $reportData[$posSpeedName]['isp'] = array_values($uniqueCustomers);
                }

                // Add the new column
                foreach ($reportData[$posSpeedName]['isp'] as &$item) {
                    $item['total'] = $this->amountIspByYear($item['id'], $fromDate, $toDate, $fttxStatus, $item['pos_speed_id']);
                    foreach ($totalAmountEachPosSpeedByMonth as $key => $value) {
                        $totalAmountEachPosSpeedByMonth[$key] += $item['total'][$key];
                    }
                }
                unset($item);
                $reportData[$posSpeedName]['total_amount'] = $totalAmountEachPosSpeedByMonth;
            }

            $data['data'] = $reportData;
            $totalAllAmountByMonth  = collect([
                'Jan' => 0,
                'Feb' => 0,
                'Mar' => 0,
                'Apr' => 0,
                'May' => 0,
                'Jun' => 0,
                'Jul' => 0,
                'Aug' => 0,
                'Sep' => 0,
                'Oct' => 0,
                'Nov' => 0,
                'Dec' => 0,
                'Total' => 0,
            ]);

            foreach ($data['data'] as $item) {
                foreach ($item['total_amount'] as $key => $value) {
                    $totalAllAmountByMonth[$key] += $value;
                }
            }
            $data['totalAllAmountByMonth'] = $totalAllAmountByMonth;
            $data['posSpeeds'] = FttxPosSpeed::where('status', 1)->get();
            $data['from_date'] = $fromDate;
            $data['to_date'] = $toDate;
            $data['fttx_status'] = $fttxStatus;
            if ($customerId) {
                $data['customer'] = Customer::where('id', $customerId)->first();
            } else {
                $data['customer'] = '';
            }

            if ($req->check == "export") {
                return Excel::download(new FttxReportExport($data), 'fttx_report' . $today . '.xlsx');
            }
            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/FttxReportController > index | message: " . $error->getMessage());
        }
    }

    public function amountIspByYear($customerId, $fromDate, $toDate, $fttxStatus, $posSpeedId)
    {
        $allMonths = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ]);

        $fttxDetail = FttxDetail::where('customer_id', $customerId)
            ->when($fttxStatus, function ($q) use ($fttxStatus, $posSpeedId) {
                $q->whereHas('fttx', function ($qq) use ($fttxStatus, $posSpeedId) {
                    $qq->where('pos_speed_id', $posSpeedId);
                    $qq->whereIn('status', $fttxStatus);
                });
            })
            ->whereBetween('date', [$fromDate, $toDate])
            ->selectRaw('MONTH(date) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->get();
        $monthlyTotals = $fttxDetail->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $result = $allMonths->merge($monthlyTotals);

        $totalSum = $result->sum();
        $result->put('Total', $totalSum);
        return $result;
    }


    public function getDetail(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $posSpeedId = $request->input('pos_speed_id');
        $customerId = $request->input('customer_id');
        $fttxStatus = $request->input('fttx_status');
        $fttxStatusArray = $fttxStatus ? explode(",", $fttxStatus) : '';

        $data['data'] = Fttx::with([
            'fttxDetail' => function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [$fromDate, $toDate]);
            }
        ], 'posSpeed')
            ->select('id', 'customer_id', 'work_order_isp', 'work_order_cfocn', 'status', 'name', 'pos_speed_id', 'completed_time', 'deadline')
            ->when($posSpeedId, fn($q) => $q->where('pos_speed_id', $posSpeedId))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($fttxStatusArray && is_array($fttxStatusArray) && count($fttxStatusArray) > 0, function ($q) use ($fttxStatusArray) {
                $q->whereIn('status', $fttxStatusArray);
            })
            ->get();
        foreach ($data['data'] as $value) {
            foreach (config('dummy.fttx_status') as $status) {
                if ($status['key'] == $value->status) {
                    $value->status_text = $status['text'];
                }
            }
            $value->pos_speed_text = $value?->posSpeed ? $value?->posSpeed?->split_pos : '';
            $getByMonths = collect([
                'Jan' => 0,
                'Feb' => 0,
                'Mar' => 0,
                'Apr' => 0,
                'May' => 0,
                'Jun' => 0,
                'Jul' => 0,
                'Aug' => 0,
                'Sep' => 0,
                'Oct' => 0,
                'Nov' => 0,
                'Dec' => 0,
                'Total' => 0,
            ]);

            if ($value->fttxDetail->isNotEmpty()) {
                foreach ($value->fttxDetail as $detail) {
                    $month = date('M', strtotime($detail->date));
                    $getByMonths[$month] += $detail->total_amount;
                }
            }
            $getByMonths['Total'] = $getByMonths->sum();

            $value->amountByMonth = $getByMonths;
        }

        return response()->json($data);
    }

    public function areArraysEqual($array1, $array2)
    {
        return empty(array_diff($array1, $array2)) && empty(array_diff($array2, $array1));
    }

    public function checkBetweenTowDate($completedTimeParam)
    {
        $completedTime = Carbon::parse($completedTimeParam);
        $oneMonthLater = $completedTime->copy()->addMonth();
        if (Carbon::today()->between($completedTime, $oneMonthLater)) {
            return true;
        } else {
            return false;
        }
    }
}
