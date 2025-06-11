<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReceivePaymentController extends Controller
{
    protected $layout = 'admin::pages.report.receive_payment.';

    public function __construct()
    {
        $this->middleware('permission:report-receive-payment-view', ['only' => ['index']]);
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
        $data = $this->getDataFilter($reqField);
        $data['from_date'] = $from_date;
        $data['to_date']   = $to_date;
        $data['projects'] = Project::where('status', 1)->get();

        return view($this->layout . 'index', $data);
    }
    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }

    public function getDataFilter($req)
    {
        $data["receipt"] = $this->queryDataReceipt($req);
        $data["creditNotes"] = $this->queryCreditNote($req);
        $getData = $this->margeData((object) $data);
        $objectItem = [
            'price_dollar' => 0,
            'price_khmer' => 0,
            'rate_dollar' => 0,
            'rate_khmer' => 0
        ];
        $total_grand = (object)[...$objectItem];
        $total_credit_note_grand = (object)[...$objectItem];


        if (count($data) > 0) {
            foreach ($getData as $item) {
                $item->type_invoice = isset($item->credit_note_number) && $item->credit_note_number ? "credit_note" : "receipt";
                if ($item->type_invoice == "credit_note") {
                    $total_credit_note_grand->price_dollar += $item->total_grand;
                } else if ($item->type_invoice == "receipt") {
                    $total_grand->price_dollar += ($item?->total_grand ?? 0);
                }
            }

            $total_grand->price_dollar = $total_grand->price_dollar - $total_credit_note_grand->price_dollar;
        }

        return [
            'data' => $getData,
            'paidAmount' => $total_grand->price_dollar,
            'totalCreditNoteGrand' => $total_credit_note_grand,
            'totalGrand' => $total_grand
        ];
    }

    public function queryDataReceipt($req)
    { {
            $queryFilter = Receipt::with([
                "customer" => function ($q) {
                    $q->select("id", "name_en", "name_kh", "address_en", "address_kh");
                },
                "invoices" => function ($q) {
                    $q->select("id", "invoice_number", "po_id");
                    $q->with(["purchase" => function ($q) {
                        $q->select("id", "type_id");
                        $q->with(["type" => function ($q) {
                            $q->select("id", "code", "name");
                        }]);
                    }]);
                },
            ])->where(function ($query) use ($req) {
                if (isset($req?->search) && $req?->search) {
                    $query->where('receipt_number', 'like', '%' . $req->search . '%');
                    $query->orWhereHas('customer', function ($q) use ($req) {
                        $q->where('customer_code', 'like', '%' . $req->search . '%');
                        $q->orWhere('name_en', 'like', '%' . $req->search . '%');
                        $q->orWhere('name_kh', 'like', '%' . $req->search . '%');
                    });
                    $query->orWhereHas('invoices', function ($q) use ($req) {
                        $q->where('invoice_number', 'like', '%' . $req->search . '%');
                    });
                }
                if ($req->from_date && $req->to_date) {
                    $query->whereBetween(DB::raw('date(issue_date)'), [$req->from_date, $req->to_date]);
                }
                if (request('search_project')) {
                    $query->whereHas('invoices', function ($q) {
                        $q->whereHas(
                            'purchase',
                            function ($q) {
                                $q->where('project_id', request('search_project'));
                            }
                        );
                    });
                }
            })->orderBy('issue_date', 'asc')->get();
            return $queryFilter;
        }
    }
    public function queryCreditNote($req)
    {
        return CreditNote::with([
            "creditNoteDetail",
            "customer",
            "invoices",
            "purchase" => function ($q) {
                $q->with(["customer", "project"]);
            }
        ])->where(function ($query) use ($req) {
            if (isset($req?->search) && $req?->search) {
                $query->where('invoice_number', 'like', '%' . $req->search . '%');
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
                    'purchase',
                    function ($q) use ($req) {
                        $q->where('project_id', $req->search_project);
                    }
                );
            }
        })->orderBy('issue_date', 'asc')->get();
    }

    public function margeData($data)
    {
        $dataResult = [...$data->receipt, ...$data->creditNotes];
        usort($dataResult, function ($a, $b) {
            return strcasecmp($a->issue_date, $b->issue_date);
        });
        return $dataResult;
    }

    public function queryData($req)
    {
        $queryFilter = Receipt::with([
            "customer" => function ($q) {
                $q->select("id", "name_en", "name_kh", "address_en", "address_kh");
            },
            "invoices" => function ($q) {
                $q->select("id", "invoice_number", "po_id");
                $q->with(["purchase" => function ($q) {
                    $q->select("id", "type_id");
                    $q->with(["type" => function ($q) {
                        $q->select("id", "code", "name");
                    }]);
                }]);
            },
        ])->where(function ($query) use ($req) {
            if (isset($req?->search) && $req?->search) {
                $query->where('receipt_number', 'like', '%' . $req->search . '%');
                $query->orWhereHas('customer', function ($q) use ($req) {
                    $q->where('customer_code', 'like', '%' . $req->search . '%');
                    $q->orWhere('name_en', 'like', '%' . $req->search . '%');
                    $q->orWhere('name_kh', 'like', '%' . $req->search . '%');
                });
                $query->orWhereHas('invoices', function ($q) use ($req) {
                    $q->where('invoice_number', 'like', '%' . $req->search . '%');
                });
            }
            // if ($req->from_date && $req->to_date) {
            //     $query->whereDate('issue_date', '>=', $req->from_date);
            //     $query->whereDate('issue_date', '<=', $req->to_date);
            //     $query->whereBetween(DB::raw('date(issue_date)'), [$req->from_date, $req->to_date]);
            // }
            if (request('search_project')) {
                $query->whereHas('invoices', function ($q) {
                    $q->whereHas(
                        'purchase',
                        function ($q) {
                            $q->where('project_id', request('search_project'));
                        }
                    );
                });
            }
        })->orderBy('issue_date', 'asc')->get();

        $data = [
            'data' => $queryFilter,
            'paidAmount' => 0
        ];

        if (isset($queryFilter) && count($queryFilter) > 0) {
            foreach ($queryFilter as $item) {
                $data['paidAmount'] += ($item->paid_amount ?? 0);
            }
        }
        $queryFilter = $data;
        return $queryFilter;
    }
}
