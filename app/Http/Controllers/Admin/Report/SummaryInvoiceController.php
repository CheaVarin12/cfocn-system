<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InfraInvoiceExport;
use App\Exports\SubmarineInvoiceExport;
use App\Models\CreditNote;
use App\Models\HistoryDmcSendFile;
use App\Models\WorkOrderCreditNote;
use App\Models\WorkOrderInvoice;
use App\Services\FTPConnectionService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SummaryInvoiceController extends Controller
{
    protected $layoutInfra = 'admin::pages.report.summary_invoice.infra.';
    protected $layoutSubmarine =  'admin::pages.report.summary_invoice.submarine.';
    private $serverConnection = null;
    private $message = null;
    
    public function __construct(FTPConnectionService $ser)
    {
        $this->serverConnection = $ser;
        $this->middleware('permission:infra-view', ['only' => ['index']]);
        $this->middleware('permission:submarine-view', ['only' => ['index']]);
    }

    public function index(Request $req, $type)
    {
        $startDate = Carbon::now();
        $dateTime = $startDate->toDate()->format('YmdHis');

        $from_date = $req->from_date ? $req->from_date : $startDate->firstOfMonth()->toDate()->format('Y-m-d');
        $to_date =  $req->to_date ? $req->to_date : $startDate->lastOfMonth()->toDate()->format('Y-m-d');
        $reqField = (object) $req->all();
        $data['server_connection_status'] = 'unable_connection';
        $reqField->from_date = $from_date;
        $reqField->to_date = $to_date;
        $data = $this->getDataFilter($reqField, $type);

        $data['from_date'] = $from_date;
        $data['to_date']   = $to_date;
        $data['year'] = $req->from_date ? Carbon::parse($req->from_date)->format('Y') : $startDate->format('Y');
        $data['month'] = $req->from_date ? Carbon::parse($req->from_date)->format('m') : $startDate->format('m');
        $data['date'] = $data['year'] . $data['month'];
       
        if ($type == 'infra') {
            $nameExport = $this->formatDate($from_date, 'Y') . $this->formatDate($from_date, 'm') . ' '. 'Infra_Invoice_Summary.xlsx';
        } else {
            $nameExport = $this->formatDate($from_date, 'Y') . $this->formatDate($from_date, 'm') . ' '. 'Submarine_Invoice_Summary.xlsx';
        }

        if ($req->check == "export") {
            if ($type == 'infra') {
                return Excel::download(new InfraInvoiceExport($data), $nameExport);
            } elseif ($type == 'submarine') {
                return Excel::download(new SubmarineInvoiceExport($data), $nameExport);
            }
        }

        if ($req->check == "submitDMC") {
            $res = $this->serverConnection->ServerLogin();
            DB::beginTransaction();
            try {
                if ($res == "login_success") {
                    $startDate = $req->from_date;
                    $year = $this->formatDate($startDate, 'Y');
                    $month = $this->formatDate($startDate, 'm');
                    $day = $this->formatDate($startDate, 'd');

                    $datePathUpload = $year . '/' . $month;
                    if ($type == 'infra') {
                        $pathFile = 'infra_invoice_summary/' . $datePathUpload . '/' . $nameExport;
                        Excel::store(new InfraInvoiceExport($data), $pathFile);
                    } else {
                        $pathFile = 'submarine_invoice_summary/' . $datePathUpload . '/' . $nameExport;
                        Excel::store(new SubmarineInvoiceExport($data), $pathFile);
                    }

                    //getUrl
                    $file =  public_path('uploads/' . $pathFile);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);

                    $docItem = [
                        'invoice_id' => null,
                        'year' => $year,
                        'month' => $month,
                        'day' => $day,
                        'file_name' => $nameExport,
                        'file_path' => '/' . $pathFile,
                        'file_type' => $type == 'infra' ? 'infra_invoice_summary' : 'submarine_invoice_summary',
                        'extension_type' => $extension,
                        'from_date' => $req->from_date,
                        'to_date'   => $req->to_date,
                        'user_id'   => Auth::user()->id
                    ];

                    $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
                    if ($type == 'infra') {
                        $invoicePathSubmitDMCByTypeProject = $configDMCPath->infra_invoice_summary;
                    } else {
                        $invoicePathSubmitDMCByTypeProject = $configDMCPath->submarine_invoice_summary;
                    }

                    //submitDMC
                    $this->serverConnection->dmcFile($invoicePathSubmitDMCByTypeProject, $file, $nameExport, function ($result, $err) use ($docItem, $res) {
                        if ($result == true) {
                            HistoryDmcSendFile::create($docItem);
                            $this->message = 'success';
                        } else {
                            $this->message = 'unsuccess';
                        }
                    });
                    Session::flash('success', 'Submit to dmc success');
                    DB::commit();
                    return response()->json([
                        'data' => null,
                        'message' => $this->message,
                        'connection_status' => $res
                    ]);
                } else {
                    return response()->json([
                        'data' => null,
                        'message' => 'unsuccess',
                        'connection_status' => $res
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'data' => null,
                    'err'  => $e->getMessage(),
                    'message' => 'unsuccess',
                    'connection_status' => $res
                ]);
            }
        }

        if ($type == 'infra') {
            return view($this->layoutInfra . 'index', $data);
        } elseif ($type == 'submarine') {
            return view($this->layoutSubmarine . 'index', $data);
        }
    }

    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }

    public function getDataFilter($req, $type)
    {
        $data["invoice"] = $this->queryDataInvoice($req, $type);
        $data["voidInvoice"] = $this->queryVoidInvoice($req, $type);
        $data["ftthInvoice"] = $this->queryFTTHInvoice($req, $type);
        $data["ftthVoidInvoice"] = $this->queryFTTHVoidInvoice($req, $type);
        $data['creditNote'] = $this->queryCreditNote($req, $type);
        $data['ftthCreditNote'] = $this->queryFTTHCreditNote($req, $type);

        $getData = $this->margeData((object) $data);
        $objectItem = [
            'total_price' => 0,
            'total_vat' => 0,
            'total_grand' => 0,
        ];

        $total_price = (object)[...$objectItem];
        $total_void_invoice_price = (object)[...$objectItem];

        $total_vat = (object)[...$objectItem];
        $total_void_invoice_vat = (object)[...$objectItem];

        $total_grand = (object)[...$objectItem];
        $total_void_invoice_grand = (object)[...$objectItem];
        if (count($data) > 0) {
            foreach ($getData as $item) {
                $item->type_invoice = $item->deleted_at ? "void_invoice" : ($item->credit_note_number ? "credit_note" : "invoice");

                if ($item->type_invoice == "void_invoice") {
                    $item->total_price = 0;
                    $item->vat = 0;
                    $item->total_grand = 0;
                    $total_void_invoice_price->total_price  += $item->total_price;
                    $total_void_invoice_vat->total_vat += $item->vat;
                    $total_void_invoice_grand->total_grand += $item->total_grand;
                } else if ($item->type_invoice == "invoice") {
                    $total_price->total_price  += $item->total_price;
                    $total_vat->total_vat += $item->vat;
                    $total_grand->total_grand += $item->total_grand;
                } else if ($item->type_invoice == "credit_note") {
                    $item->total_price = -1 * abs($item->total_price);
                    $item->vat = -1 * abs($item->vat);
                    $item->total_grand = -1 * abs($item->total_grand);

                    $total_price->total_price  += $item->total_price;
                    $total_vat->total_vat += $item->vat;
                    $total_grand->total_grand += $item->total_grand;
                }
            }

            $total_price->total_price = $total_price->total_price - $total_void_invoice_price->total_price;
            $total_vat->total_vat = $total_vat->total_vat - $total_void_invoice_vat->total_vat;
            $total_grand->total_grand = $total_grand->total_grand - $total_void_invoice_grand->total_grand;
        }

        return [
            'data' => $getData,
            'totalPrice' =>  $total_price->total_price,
            'totalVat' => $total_vat->total_vat,
            'totalGrand' =>  $total_grand->total_grand,
        ];
    }

    public function queryDataInvoice($req, $type)
    {
        $queryFilter = Invoice::with([
            "invoiceDetail",
            "customer" => function ($q) {
                $q->select("id", "name_en", "name_kh", "address_en", "address_kh");
            },
            "purchase" => function ($q) {
                $q->select("id", "project_id", "type_id");
                $q->with(["type" => function ($q) {
                    $q->select("id", "code", "name");
                }]);
            },
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
                $query->whereBetween(DB::raw('date(issue_date)'), [$req->from_date, $req->to_date]);
            }
        })->whereHas('purchase', function ($query) use ($type) {
            if ($type == 'infra') {
                $query->where('project_id', '!=', 2);
            } else if ($type == 'submarine') {
                $query->where('project_id', 2);
            }
        })
            ->orderBy('issue_date', 'asc')->get();
        return $queryFilter;
    }
    public function queryVoidInvoice($req, $type)
    {
        return Invoice::with([
            "invoiceDetail",
            "customer",
            "purchase" => function ($q) {
                $q->with(["customer", "project", "type"]);
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
        })->whereHas('purchase', function ($query) use ($type) {
            if ($type == 'infra') {
                $query->where('project_id', '!=', 2);
            } else if ($type == 'submarine') {
                $query->where('project_id', 2);
            }
        })->onlyTrashed()->orderBy('issue_date', 'asc')->get();
    }

    public function queryFTTHInvoice($req, $type)
    {
        return WorkOrderInvoice::with([
            "order.type",
            "invoiceDetail",
            "customer",
        ])
            ->where(function ($query) use ($req) {
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
            })
            ->whereHas('order', function ($query) use ($type) {
                if ($type == 'infra') {
                    $query->where('project_id', '!=', 2);
                } else if ($type == 'submarine') {
                    $query->where('project_id', 2);
                }
            })
            ->orderBy('issue_date', 'asc')->get();
    }

    public function queryFTTHVoidInvoice($req, $type)
    {
        return WorkOrderInvoice::with([
            "order.type",
            "invoiceDetail",
            "customer",
        ])
            ->where(function ($query) use ($req) {
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
            })
            ->whereHas('order', function ($query) use ($type) {
                if ($type == 'infra') {
                    $query->where('project_id', '!=', 2);
                } else if ($type == 'submarine') {
                    $query->where('project_id', 2);
                }
            })->onlyTrashed()
            ->orderBy('issue_date', 'asc')->get();
    }

    public function queryCreditNote($req, $type)
    {
        return CreditNote::with(['creditNoteDetail', 'invoiceDetail', 'customer', 'purchase.type'])
            ->where(function ($query) use ($req) {
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
            })->whereHas('purchase', function ($query) use ($type) {
                if ($type == 'infra') {
                    $query->where('project_id', '!=', 2);
                } else if ($type == 'submarine') {
                    $query->where('project_id', 2);
                }
            })
            ->orderBy('issue_date', 'asc')
            ->get();
    }

    public function queryFTTHCreditNote($req, $type)
    {
        return WorkOrderCreditNote::with(['creditNoteDetails', 'invoiceDetail', 'customer', 'order.type'])
            ->where(function ($query) use ($req) {
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
            })->whereHas('order', function ($query) use ($type) {
                if ($type == 'infra') {
                    $query->where('project_id', '!=', 2);
                } else if ($type == 'submarine') {
                    $query->where('project_id', 2);
                }
            })
            ->orderBy('issue_date', 'asc')
            ->get();
    }

    public function margeData($data)
    {
        $dataResult = [...$data->invoice, ...$data->voidInvoice, ...$data->ftthInvoice, ...$data->ftthVoidInvoice, ...$data->creditNote, ...$data->ftthCreditNote];
        usort($dataResult, function ($a, $b) {
            return strcasecmp($a->issue_date, $b->issue_date);
        });
        return $dataResult;
    }
}
