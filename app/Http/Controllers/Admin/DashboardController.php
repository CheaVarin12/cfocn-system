<?php

namespace App\Http\Controllers\Admin;


use App\Services\QueryService;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Project;
use App\Models\Service;
use App\Models\Type;
use App\Models\Purchase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $queryService = null;
    protected $layout = 'admin::pages.purchase.';
    public function __construct(QueryService $qService)
    {
        $this->queryService = $qService;
        $this->middleware('permission:dashboard-view', ['only' => ['index']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/DashboardController > index | admin: ");
        $this->getExchangeRate();
        $fromDate = $req->from_date ? $req->from_date : Carbon::now()->firstOfMonth()->toDate()->format('Y-m-d');
        $toDate =  $req->to_date ? $req->to_date : Carbon::now()->lastOfMonth()->toDate()->format('Y-m-d');

        $invoiceData = $this->filter(Invoice::class, $fromDate, $toDate);
        $receiptData = $this->filter(Receipt::class, $fromDate, $toDate);


        $data["dashboard"] = [
            (object) [
                'name' => 'All Customers',
                'icon' => 'bx-user bx-tada-hover',
                'value' => $this->filterCount(Customer::class, $fromDate, $toDate, 'status', null),
                'custom_class' => 'bg-primary',
                'link' => route('admin-customer-list'),
            ],
            (object) [
                'name' => 'Active Customers',
                'icon' => 'bx-user-check bx-tada-hover',
                'value' => $this->filterCount(Customer::class, $fromDate, $toDate, 'status', 1),
                'custom_class' => 'bg-all-booking',
                'link' => route('admin-user-list'),
            ],
            (object) [
                'name' => 'Inactive Customers',
                'icon' => 'bx-user-x bx-tada-hover',
                'value' => $this->filterCount(Customer::class, $fromDate, $toDate, 'status', 2),
                'custom_class' => 'bg-dark-light',
                'link' => route('admin-user-list'),
            ],
            (object) [
                'name' => 'Invoices',
                'icon' => 'bx-file-blank bx-tada-hover',
                'value' => $invoiceData->dataCount,
                'custom_class' => 'bg-pending-booking',
                'link' => route('admin-customer-list'),
            ],
            (object) [
                'name' => 'Receipts',
                'icon' => 'bx-receipt bx-tada-hover',
                'value' => $receiptData->dataCount,
                'custom_class' => 'bg-completed-booking',
                'link' => route('admin-user-list'),
            ],
            (object) [
                'name' => 'Receipt Amounts',
                'icon' => 'bx-dollar-circle bx-tada-hover',
                'type_value' => 'dollar',
                'value' => $receiptData->totalGrand,
                'custom_class' => 'bg-amount',
                'link' => route('admin-user-list'),
            ],
            (object) [
                'name' => 'Fiber Optic Project',
                'icon' => 'bxl-product-hunt bx-tada-hover',
                'value' => $this->filterInvoiceProject(Invoice::class, $fromDate, $toDate, 1)->dataCount,
                'custom_class' => 'bg-cancel-booking',
                'link' => '#',
            ],
            (object) [
                'name' => 'Submarine Project',
                'icon' => 'bxl-product-hunt bx-tada-hover',
                'value' => $this->filterInvoiceProject(Invoice::class, $fromDate, $toDate, 2)->dataCount,
                'custom_class' => 'bg-customer',
                'link' => '#',
            ],
            (object) [
                'name' => 'Underground Project',
                'icon' => 'bxl-product-hunt bx-tada-hover',
                'value' => $this->filterInvoiceProject(Invoice::class, $fromDate, $toDate, 4)->dataCount,
                'custom_class' => 'bg-standard',
                'link' => route('admin-user-list'),
            ],
        ];
        $data['firstMonthDay'] = $fromDate;
        $data['lastMonthDay'] =  $toDate;

        return view("admin::pages.dashboard", $data);
    }
    private function filterCount($model, $first, $last, $typeStatus, $status)
    {
        return $model::where(function ($q) use ($first, $last, $typeStatus, $status) {
            if ($first && $last) {
                $q->whereDate('created_at', '>=', $first);
                $q->whereDate('created_at', '<=', $last);
            }
            if ($status) {
                $q->where($typeStatus, $status);
            }
        })->count();
    }

    private function filter($model, $first, $last)
    {
        return $model::select(
            DB::raw('sum(total_grand) as totalGrand'),
            DB::raw('count(total_grand) as dataCount'),
        )->where(function ($q) use ($first, $last) {
            if ($first && $last) {
                $q->whereDate('issue_date', '>=', $first);
                $q->whereDate('issue_date', '<=', $last);
            }
        })->first();
    }

    public function filterInvoiceProject($model, $first, $last, $project_id)
    {
        return $model::select(
            DB::raw('sum(total_grand) as totalGrand'),
            DB::raw('count(total_grand) as dataCount'),
        )->where(function ($q) use ($first, $last, $project_id) {
            if ($first && $last) {
                $q->whereDate('issue_date', '>=', $first);
                $q->whereDate('issue_date', '<=', $last);
            }
            $q->whereHas(
                'purchase',
                function ($q) use ($project_id) {
                    $q->where('project_id', $project_id);
                }
            );
        })->first();
    }

    public function countUser()
    {
        return User::where('type', 'admin')->whereNot('role', 'super_admin')->count();
    }
}
