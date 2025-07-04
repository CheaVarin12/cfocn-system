<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\FttxExpirationReportExport;
use App\Models\Customer;
use App\Models\Fttx;
use App\Models\FttxDetail;
use App\Models\FttxPosSpeed;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class FttxExpirationReportController extends Controller
{
    protected $layout = 'admin::pages.fttx.expiration-report.';

    public function __construct()
    {
        $this->middleware('permission:fttx-expiration-report-view', ['only' => ['index']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/FttxExpirationReportController > index | admin: " . json_encode($req->all()));
        try {
            $today = Carbon::now()->format('d-M-Y');
            $posSpeedId = $req->pos_speed_id ? explode(",", $req->pos_speed_id) : null;
            $customerId = $req->customer_id ?? null;
            $fullMonthKeys = $this->getFttxDeadlineMonths($customerId, $posSpeedId);

            $data['columns'] = $fullMonthKeys;
            $templateTotalPerMonth = collect(array_fill_keys($fullMonthKeys, 0))->put('Total', 0);
            $reportData = [];

            // Get all FTTX entries matching filter
            $fttxQuery = Fttx::with(['customer'])
                ->where('deadline', '<', Carbon::today())
                ->where('status', '!=', 3)
                ->where(function ($query) {
                    $query->where('check_status', '!=', 'inactive')->orWhereNull('check_status');
                });

            if ($customerId) {
                $fttxQuery->where('customer_id', $customerId);
            }
            
            if ($posSpeedId) {
                $fttxQuery->whereIn('pos_speed_id', $posSpeedId);
            }

            $fttxEntries = $fttxQuery->get();

            // Get unique customers from FTTX
            $uniqueCustomers = $fttxEntries->pluck('customer')->filter()->unique('id');

            foreach ($uniqueCustomers as $customer) {
                $total = $this->getAmountNeedToPay($customer->id, null, $fullMonthKeys);

                // Update grand total per month
                foreach ($templateTotalPerMonth->keys() as $key) {
                    $templateTotalPerMonth[$key] += $total[$key] ?? 0;
                }

                $reportData[] = [
                    'id' => $customer->id,
                    'name_en' => $customer->name_en,
                    'name_kh' => $customer->name_kh,
                    'total' => $total,
                ];
            }

            $data['data'] = $reportData;
            $data['totalAllAmountByMonth'] = $templateTotalPerMonth;
            $data['posSpeeds'] = FttxPosSpeed::where('status', 1)->get();
            $data['customer'] = $customerId ? Customer::find($customerId) : '';

            if ($req->check == "export") {
                return Excel::download(new FttxExpirationReportExport($data), 'Expiration_income_report_' . $today . '.xlsx');
            }

            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            dd($error);
            Log::error("Error: Admin/FttxExpirationReportController > index | message: " . $error->getMessage());
        }
    }


    public function getAmountNeedToPay($customerId, $posSpeedId, $column)
    {
        try {
            $allMonths = collect(array_fill_keys($column, 0));

            $fttxDetail = Fttx::where('customer_id', $customerId)
                ->when($posSpeedId, function ($q) use ($posSpeedId) {
                    $q->whereIn('pos_speed_id', $posSpeedId);  
                })
                ->where('deadline', '<', Carbon::today())
                ->where('status', '!=', 3)
                ->where(function ($query) {
                    $query->where('check_status', '!=', 'inactive')
                        ->orWhereNull('check_status');
                })
                ->select('id', 'deadline', 'rental_price', 'ppcc', 'rental_pole')
                ->get();

            $now = Carbon::now()->startOfMonth();

            foreach ($fttxDetail as $item) {
                if (!$item->deadline || !$item->rental_price) continue;

                $start = Carbon::parse($item->deadline)->startOfMonth();

                while ($start <= $now) {
                    $monthKey = $start->format('M-Y');

                    if ($allMonths->has($monthKey)) {
                        $allMonths[$monthKey] += (float) ($item->rental_price + $item->ppcc + $item->rental_pole);
                    }

                    $start->addMonth();
                }
            }

            $allMonths->put('Total', $allMonths->sum());

            return $allMonths;
        } catch (Exception $error) {
            Log::error("Error: Admin/FttxExpirationReportController > getAmountNeedToPay | message: " . $error->getMessage());
        }
    }

    function getFttxDeadlineMonths($customerId = null, $posSpeedId = null)
    {
        $earliest = Fttx::where('deadline', '<', Carbon::today())
            ->when($customerId, function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->when($posSpeedId, function ($q) use ($posSpeedId) {
                $q->whereIn('pos_speed_id', $posSpeedId);
            })
            ->where('status', '!=', 3)
            ->where(function ($query) {
                $query->where('check_status', '!=', 'inactive')
                    ->orWhereNull('check_status');
            })
            ->orderBy('deadline', 'asc')
            ->first();

        $start = $earliest
            ? Carbon::parse($earliest->deadline)->startOfMonth()
            : Carbon::now()->startOfMonth();

        $end = Carbon::now()->startOfMonth();
        $months = [];

        while ($start <= $end) {
            $months[] = $start->format('M-Y');
            $start->addMonth();
        }

        return $months;
    }

    public function getDetail(Request $request)
    {
        $posSpeedId = $request->input('pos_speed_id');
        $customerId = $request->input('customer_id');

        $data['data'] = Fttx::with('posSpeed')
            ->select('id', 'customer_id', 'work_order_isp', 'work_order_cfocn', 'status', 'name', 'pos_speed_id', 'completed_time', 'deadline', 'rental_price', 'ppcc', 'rental_pole')
            ->when($posSpeedId, fn($q) => $q->where('pos_speed_id', $posSpeedId))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->where('deadline', '<', Carbon::today())
            ->where('status', '!=', 3)
            ->where(function ($query) {
                $query->where('check_status', '!=', 'inactive')
                    ->orWhereNull('check_status');
            })
            ->get()
            ->map(function ($item) {
                // Basic total
                $item->total = floatval($item->rental_price) + floatval($item->ppcc) + floatval($item->rental_pole);

                // Count months between deadline and now (inclusive)
                $start = Carbon::parse($item->deadline)->startOfMonth();
                $end = Carbon::now()->startOfMonth();
                $months = $start->diffInMonths($end) + 1; // Include the deadline month

                // Grand total
                $item->grand_total = $item->total * $months;

                // Human-readable status
                foreach (config('dummy.fttx_status') as $status) {
                    if ($status['key'] == $item->status) {
                        $item->status_text = $status['text'];
                    }
                }

                // Optional: number of expired months
                $item->expired_months = $months;

                return $item;
            });

        return response()->json($data);
    }
}
