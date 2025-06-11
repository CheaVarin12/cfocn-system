<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\report\SaleJournalExport;
use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use Illuminate\Http\Request;
use KhmerDateTime\KhmerDateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\WorkOrderCreditNote;
use App\Models\WorkOrderInvoice;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class SaleJournalReportController extends Controller
{
    protected $layout = 'admin::pages.report.sale-journal.';
    public function __construct()
    {
        $this->middleware('permission:report-sale-journal-view', ['only' => ['index']]);
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
        // return $data;
        $data['projects'] = Project::where('status', 1)->get();
        $data['rate'] = DB::table('rates')->first();
        $data['date'] = $this->getDate($from_date, $to_date);
        if ($req->check == "export") {
            $nameExport = "sale_journal_report_" . $from_date . "_" . $to_date . '.xlsx';
            return Excel::download(new SaleJournalExport($data), $nameExport);
        }
        return view($this->layout . 'index', $data);
    }
    public function getDate($from_date, $to_date)
    {
        $dateTimeFromDate = $this->formatDate($from_date);
        $dateTimeToDate = $this->formatDate($to_date);
        $data['form_date_label'] = (object)[
            'day'   => $dateTimeFromDate->day(),
            'month' => $dateTimeFromDate->fullMonth(),
            'year'  => $dateTimeFromDate->year()
        ];
        $data['to_date_label'] = (object)[
            'day'   => $dateTimeToDate->day(),
            'month' => $dateTimeToDate->fullMonth(),
            'year'  => $dateTimeToDate->year()
        ];
        return $data;
    }
    public function queryData($req)
    {
        $objectItem = [
            'price_dollar' => 0,
            'price_khmer' => 0,
            'rate_dollar' => 0,
            'rate_khmer' => 0
        ];
        $totalSaleTax = (object)[...$objectItem];
        $totalSaleUser = (object)[...$objectItem];
        $totalSaleNotTax = (object)[...$objectItem];
        $total_grand = (object)[...$objectItem];
        $total_credit_note_grand = (object)[...$objectItem];

        $project = isset($req->search_project) && $req?->search_project ? Project::find($req->search_project) : null;
        $dataRateCheckDebug = [];

        $data = $this->margeData((object) $this->getDataFilter($req));

        if (count($data) > 0) {
            foreach ($data as $item) {
                $item->customer_type = $item?->customer?->type ?? 1; // 1 is Taxable 2 is Not taxed 3 is for user
                $item->customer_vat_tin = $item?->customer?->vat_tin != 0 && $item?->customer?->vat_tin ? true : false;
                $item->type_invoice = isset($item->credit_note_number) && $item->credit_note_number ? "credit_note" : "invoice";
                $item->ftth_invoice = isset($item->order) && $item->order ? "ftth_invoice" : "invoice";
                $item->total_rate = $item->vat;

                $item->total_grand_kh = round($item->total_grand * $item->exchange_rate, 2);
                $item->total_price_kh = round($item->total_grand_kh / 1.1, 2);
                $item->total_rate_kh = $item->total_grand_kh - $item->total_price_kh;

                if (round($item->total_price * (10 / 100), 2) != $item->vat) {
                    $item->total_rate_kh = $item->vat * $item->exchange_rate;
                    $item->total_price_kh = $item->total_price * $item->exchange_rate;
                }

                if ($item->customer_type == 2) {
                    $item->total_grand = $item->total_price;
                    $item->vat = 0;
                    $item->total_grand_kh = round($item->total_grand * $item->exchange_rate, 2);
                    $item->total_price_kh = $item->total_grand_kh;
                    $item->total_rate_kh = 0;
                }

                if ($item->customer_type == 1) {
                    if ($item->type_invoice == "credit_note") {
                        $totalSaleTax->price_dollar -= $item->total_price;
                        $totalSaleTax->price_khmer -= $item->total_price_kh;
                        $totalSaleTax->rate_dollar -= $item->total_rate;
                        $totalSaleTax->rate_khmer -= $item->total_rate_kh;
                    } else {
                        $totalSaleTax->price_dollar += $item->total_price;
                        $totalSaleTax->price_khmer += $item->total_price_kh;
                        $totalSaleTax->rate_dollar += $item->total_rate;
                        $totalSaleTax->rate_khmer += $item->total_rate_kh;
                    }
                    $item->location_display = 'sale_tax';
                } elseif ($item->customer_type == 2) {
                    if ($item->type_invoice == "credit_note") {
                        $totalSaleNotTax->price_dollar -= $item->total_price;
                        $totalSaleNotTax->price_khmer -= $item->total_price_kh;
                        $totalSaleNotTax->rate_dollar -= $item->total_rate;
                        $totalSaleNotTax->rate_khmer -= $item->total_rate_kh;
                    } else {
                        $totalSaleNotTax->price_dollar += $item->total_price;
                        $totalSaleNotTax->price_khmer += $item->total_price_kh;
                        $totalSaleNotTax->rate_dollar += $item->total_rate;
                        $totalSaleNotTax->rate_khmer += $item->total_rate_kh;
                    }
                    $item->location_display = 'sale_not_tax';
                } else {
                    if ($item->type_invoice == "credit_note") {
                        $totalSaleUser->price_dollar -= $item->total_price;
                        $totalSaleUser->price_khmer -= $item->total_price_kh;
                        $totalSaleUser->rate_dollar -= $item->total_rate;
                        $totalSaleUser->rate_khmer -= $item->total_rate_kh;
                    } else {
                        $totalSaleUser->price_dollar += $item->total_price;
                        $totalSaleUser->price_khmer += $item->total_price_kh;
                        $totalSaleUser->rate_dollar += $item->total_rate;
                        $totalSaleUser->rate_khmer += $item->total_rate_kh;
                    }
                    $item->location_display = 'sale_user';
                }

                if (!$item->customer_type && $item->customer_vat_tin == false) {
                    if ($item->type_invoice == "credit_note") {
                        $totalSaleUser->price_dollar -= $item->total_price;
                        $totalSaleUser->price_khmer -= $item->total_price_kh;
                        $totalSaleUser->rate_dollar -= $item->total_rate;
                        $totalSaleUser->rate_khmer -= $item->total_rate_kh;
                    } else {
                        $totalSaleUser->price_dollar += $item->total_price;
                        $totalSaleUser->price_khmer += $item->total_price_kh;
                        $totalSaleUser->rate_dollar += $item->total_rate;
                        $totalSaleUser->rate_khmer += $item->total_rate_kh;
                    }
                    $item->location_display = 'sale_user';
                }

                if ($item->type_invoice == "credit_note") {
                    $total_credit_note_grand->price_dollar += $item->total_grand;
                    $total_credit_note_grand->price_khmer += $item->total_grand_kh;
                } else if ($item->type_invoice == "invoice") {
                    $total_grand->price_dollar += $item->total_grand;
                    $total_grand->price_khmer += $item->total_grand_kh;
                }
            }
            $total_grand->price_dollar = $total_grand->price_dollar - $total_credit_note_grand->price_dollar;
            $total_grand->price_khmer = $total_grand->price_khmer - $total_credit_note_grand->price_khmer;
        }

        $item = [
            "from_date" => $req->from_date,
            "to_date"   => $req->to_date,
            "data"  => $data,
            "projectData" => $project,
            "dataRateCheckDebug" => $dataRateCheckDebug,
            "projectInExport" =>  $this->dataGroupBy($data),
            "totalSaleTax" => $totalSaleTax,
            "totalSaleNotTax" => $totalSaleNotTax,
            "totalSaleUser" => $totalSaleUser,
            "total_grand" => $total_grand
        ];
        return $item;
    }
    public function getDataFilter($req)
    {
        $data["invoices"] = $this->filterData(Invoice::class, $req, ["invoiceDetail", "customer", "purchase.customer", "purchase.project"], "purchase");
        $data["creditNotes"] = $this->filterData(CreditNote::class, $req, ["creditNoteDetail", "customer", "purchase.customer", "purchase.project"], "purchase", "credit_note");
        $data['ftthInvoices'] = $this->filterData(WorkOrderInvoice::class, $req, ["invoiceDetail", "customer", "order.customer", "order.project"], "order");
        $data['ftthCreditNotes'] = $this->filterData(WorkOrderCreditNote::class, $req, ["creditNoteDetails", "customer", "order.customer", "order.project"], "order", "credit_note");
        return $data;
    }

    public function filterData($model, $req, $dataDetail, $relation = null, $invoiceType = null)
    {
        return $model::with(
            // [
            //     $dataDetail,
            //     "customer",
            //     "purchase" => function ($q) {
            //         $q->with(["customer", "project"]);
            //     },
            //     "order" => function($q) {
            //         $q->with(["customer", "project"]);
            //     }
            // ]
            $dataDetail
        )->where(function ($query) use ($req, $dataDetail, $relation, $invoiceType) {
            if (isset($req?->search) && $req?->search) {
                if ($invoiceType == 'credit_note') {
                    $query->where('credit_note_number', 'like', '%' . $req->search . '%');
                } else {
                    $query->where('invoice_number', 'like', '%' . $req->search . '%');
                }
                $query->orWhereHas('customer', function ($q) use ($req) {
                    $q->where('customer_code', 'like', '%' . $req->search . '%');
                    $q->orWhere('name_en', 'like', '%' . $req->search . '%');
                    $q->orWhere('name_kh', 'like', '%' . $req->search . '%');
                });
            }
            if ($req->from_date && $req->to_date) {
                $query->whereDate('issue_date', '>=', $req->from_date);
                $query->whereDate('issue_date', '<=', $req->to_date);
            }
            if (isset($req->search_project) && $req?->search_project) {
                $query->whereHas(
                    $relation,
                    function ($q) use ($req) {
                        $q->where('project_id', $req->search_project);
                    }
                );
            }
        })->orderBy('issue_date', 'asc')->get();
    }

    public function margeData($data)
    {
        $dataResult = [...$data->invoices, ...$data->ftthInvoices, ...$data->creditNotes, ...$data->ftthCreditNotes];
        //sort(invoice_number)
        usort($dataResult, function ($a, $b) {
            return strcasecmp($a->invoice_number, $b->invoice_number);
        });
        return $dataResult;
    }
    public function dataGroupBy($data)
    {
        $dataItem = [];
        if (count($data) > 0) {
            foreach ($data as $index => $item) {
                if ($item->ftth_invoice == "invoice") {
                    if (count($dataItem) > 0) {
                        if (!isset($dataItem[$item->purchase->project_id])) {
                            $dataItem[$item->purchase->project_id] = Project::find($item->purchase->project_id);
                        }
                    } else {
                        $dataItem[$item->purchase->project_id] = Project::find($item->purchase->project_id);
                    }
                } else {
                    if (count($dataItem) > 0) {
                        if (!isset($dataItem[$item->order->project_id])) {
                            $dataItem[$item->order->project_id] = Project::find($item->order->project_id);
                        }
                    } else {
                        $dataItem[$item->order->project_id] = Project::find($item->order->project_id);
                    }
                }
            }
        }
        return $dataItem;
    }

    public function formatDate($date)
    {
        return $date ?  KhmerDateTime::parse($date) : null;
    }
}
