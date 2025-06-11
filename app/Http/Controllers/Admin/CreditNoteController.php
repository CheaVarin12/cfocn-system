<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreditNoteRequest;
use App\Http\Requests\Admin\DMCSubmitRequest;
use App\Http\Requests\Admin\InvoiceRequest;
use App\Mail\InvoiceDocSend;
use App\Models\BankAccount;
use App\Models\ChildCreditNote;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\CreditNote;
use App\Models\CreditNoteDetail;
use App\Models\Customer;
use App\Models\HistoryDmcSendFile;
use App\Models\LogoControl;
use App\Models\Project;
use App\Models\Purchase;
use App\Models\SendMail;
use App\Models\UploadFile;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Services\FTPConnectionService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\File;

class CreditNoteController extends Controller
{
    protected $layout = 'admin::pages.credit_note.';
    private $disk = null;
    private $sftp = null;
    private $serverConnection = null;
    private $message = null;
    public function __construct(FTPConnectionService $ser)
    {
        $this->middleware('permission:credit-note-view', ['only' => ['index']]);
        $this->middleware('permission:credit-note-create', ['only' => ['create', 'onSave']]);
        $this->middleware('permission:credit-note-update', ['only' => ['onUpdateStatus', 'onSave']]);
        $this->middleware('permission:credit-note-detail', ['only' => ['viewDetail']]);
        $this->middleware('permission:credit-note-dmc-submit', ['only' => ['DMCSubmit']]);
        $this->disk = Storage::disk('ftp');
        $this->serverConnection = $ser;
    }
    public function index(Request $request)
    {
        Log::info("Start: Admin/CreditNoteController > index | admin: ");
        try {
            $data['status'] = $request->status;
            $search = $request->search ? $request->search : '';
            if (!$request->status) {
                return redirect()->route('admin-credit-note-list', 1);
            }
            if ($request->status != 'trash') {
                $query = CreditNote::where('status', $request->status);
            } else {
                $query = CreditNote::onlyTrashed();
            }
            $fromDate = $request->from_date ? $request->from_date : '';
            $toDate = $request->to_date ? $request->to_date : '';
            $data['projects'] = Project::where('status', 1)->get();
            $data['data'] = $query->with(['creditNoteDetail', 'invoices'])->where(function ($q) use ($search, $fromDate, $toDate) {
                if (request('search')) {
                    $q->where('credit_note_number', 'like', '%' . $search . '%');
                    $q->orWhere('invoice_number', 'like', '%' . $search . '%');
                    $q->orWhereHas('customer', function ($q) {
                        $q->where('name_en', 'like', '%' . request('search') . '%');
                        $q->orWhere('name_kh', 'like', '%' . request('search') . '%');
                    });
                }
                if ($fromDate && $toDate) {
                    $q->whereDate('issue_date', '>=', $fromDate);
                    $q->whereDate('issue_date', '<=', $toDate);
                }
                if (request('search_project')) {
                    $q->whereHas(
                        'purchase',
                        function ($q) {
                            $q->where('project_id', request('search_project'));
                        }
                    );
                }
            })->orderBy('id', 'desc')->paginate(25);
            $data['logoControl'] = LogoControl::first();
            $contract_number = [];
            $contract_number_item = null;
            foreach ($data['data'] as $key => $value) {
                if ($value->invoices && $value->invoices->multiple_po_id && count(json_decode($value->invoices->multiple_po_id)) > 0) {
                    foreach (json_decode($value->invoices->multiple_po_id) as $item) {
                        $contract_number_item = Purchase::find($item)->contract_number;
                        if ($contract_number_item) {
                            array_push($contract_number, Purchase::find($item)->contract_number);
                        }
                    }
                    if (count($contract_number) > 0) {
                        $value->contract_number = implode(",", $contract_number);
                    } else {
                        $value->contract_number = '';
                    }
                    $value->pacs = Purchase::select('id', 'pac_number', 'po_number')
                        ->whereIn('id', json_decode($value->invoices->multiple_po_id))
                        ->get();
                    $value->check_multiple_pac = true;
                } else {
                    $value->contract_number = $value?->purchase?->contract_number;
                    $value->pacs = Purchase::select('id', 'pac_number', 'po_number')
                        ->where('id', $value->po_id)
                        ->get();
                    $value->check_multiple_pac = false;
                }
                $contract_number = [];
            }
            return view($this->layout . 'index', $data);
        } catch (Exception $e) {
            Log::error("Error: Admin/CreditNoteController > index | message: " . $e->getMessage());
        }
    }
    public function pickUpInvoice($invoice_number = null)
    {
        Log::info("Start: Admin/CreditNoteController > pickUpInvoice | admin:");
        try {
            $data = $this->queryPickUpInvoice($invoice_number);
            $contract_number = [];
            $contract_number_item = null;
            if ($data->invoices &&  $data->invoices->multiple_po_id && count(json_decode($data->invoices->multiple_po_id)) > 0) {
                foreach (json_decode($data->invoices->multiple_po_id) as $item) {
                    $contract_number_item = Purchase::find($item)->contract_number;
                    if ($contract_number_item) {
                        array_push($contract_number, Purchase::find($item)->contract_number);
                    }
                }
                if (count($contract_number) > 0) {
                    $data->contract_number = implode(",", $contract_number);
                } else {
                    $data->contract_number = '';
                }
            } else {
                $data->contract_number = $data?->purchase?->contract_number;
            }
            if ($data->multiple_po_id) {
                $data['pacs'] = Purchase::select('id', 'pac_number', 'po_number')
                    ->whereIn('id', json_decode($data->multiple_po_id))
                    ->get();
                $data['check_multiple_pac'] = true;
            } else {
                $data['pacs'] = Purchase::select('id', 'pac_number', 'po_number')
                    ->where('id', $data->po_id)
                    ->get();
                $data['check_multiple_pac'] = false;
            }
            return response()->json(['data' => $data, 'message' => true]);
        } catch (Exception $error) {
            Log::error("Error: Admin/CreditNoteController > pickUpInvoice | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function pickUpCreditNote($invoice_number = null)
    {
        Log::info("Start: Admin/CreditNoteController > pickUpCreditNote | admin:");
        try {
            $data = CreditNote::with(['purchase.project', 'customer', 'creditNoteDetail.service', 'invoices'])
                ->where('credit_note_number',  $invoice_number)->first();
            if (isset($data) && $data) {
                $data->total_grand_kh = 0;
                $data->total_price_kh = 0;
                $data->vat_kh = 0;
                $data->contact = Contact::first();
                $data->check_rate_first = 0;
                $data->check_rate_seconde = 0;
                $data->purchase_type = $data?->purchase?->type_id == 2 ? true : false;
                $rateData = DB::table('rates')->first();
                $data->exchangeRateDefault = $rateData;
                //calKhmer
                $data->total_grand_kh = $data->total_grand * ($data->exchange_rate ?? $rateData->rate);
                $data->total_price_kh = $data->total_grand_kh / 1.1;
                $data->vat_kh = $data->total_grand_kh - $data->total_price_kh;

                if (isset($data->creditNoteDetail) && count($data->creditNoteDetail) > 0) {
                    foreach ($data->creditNoteDetail as $item) {
                        if ($item->rate_first) {
                            $data->check_rate_first += 1;
                        }
                        if ($item->rate_second) {
                            $data->check_rate_seconde += 1;
                        }
                    }
                    $data->invoice_detail = $data->creditNoteDetail;
                }
            }
            return response()->json(['data' => $data, 'message' => true]);
        } catch (Exception $error) {
            Log::error("Error: Admin/CreditNoteController > pickUpCreditNote | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function create($id)
    {
        Log::info("Start: Admin/CreditNoteController > create | admin:");
        try {
            $data['invoice'] = Invoice::where('id', $id)->withTrashed()->first();
            $data['services'] = Service::where("status", 1)->get();
            $data['rate'] = DB::table('rates')->first();
            $data['total_grand_kh'] = $data['invoice']->total_grand * $data['rate']->rate;
            $data['total_price_kh'] = $data['total_grand_kh'] / 1.1;
            $data['vat_kh'] = $data['total_grand_kh'] - $data['total_price_kh'];
            if ($data['invoice']->purchase->type_id == 2) {
                return view($this->layout . 'create-sale', $data);
            } else {
                return view($this->layout . 'create', $data);
            }
        } catch (Exception $error) {
            Log::error("Error: Admin/CreditNoteController > create | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
    public function onSave(CreditNoteRequest $req)
    {
        Log::info("Start: Admin/InvoiceController > credit note on save | admin: " . $req);
        $purchaseDetails = isset($req->purchase_details) && $req->purchase_details ? json_decode($req->purchase_details) : [];
        $childCreditNote = isset($req->child_credit_note) && $req->child_credit_note ? json_decode($req->child_credit_note) : [];
        $itemCreditNote = $req->all();
        $itemCreditNote['user_id'] = Auth::user()->id;
        $dataCustomer = $req->data_customer ? $req->data_customer : $this->dataCustomerEncode($req->customer_id);
        $itemCreditNote['data_customer'] = $dataCustomer;
        DB::beginTransaction();
        try {
            $status = "credit note create success.";
            if (!$req->id) {
                $data = CreditNote::create($itemCreditNote);
            } else {
                $data = CreditNote::find($req->id);
                $data->update($itemCreditNote);
            }
            foreach ($purchaseDetails as $item) {
                $detail = [
                    'purchase_id' => $item->purchase_id->value,
                    'credit_note_id' => $data->id,
                    'service_id' => $item->service_id->value,
                    'des' => $item->des->value,
                    'qty' => $item->qty->value,
                    'price' => $item->price->value,
                    'uom' => $item->uom->value,
                    'rate_first' => $item?->rate_first->value ? $item?->rate_first->value : null,
                    'rate_second' => $item?->rate_second->value ? $item?->rate_second->value : null,
                    'amount' => $item->amount->value ?? 0,
                ];
                if (isset($item?->credit_note_id) && $item?->credit_note_id) {
                    CreditNoteDetail::find($item->credit_note_id)->update($detail);
                } else {
                    CreditNoteDetail::create($detail);
                }
            }

            if ($req->check_multiple_pac) {
                //delete child credit note
                ChildCreditNote::where('credit_noted_id', $req->id)->delete();
                foreach ($childCreditNote as $item) {
                    $childCreditNotes = [
                        'credit_noted_id' => $data->id,
                        'purchase_id' => $item->purchase_id,
                        'total_qty' => $item->total_qty,
                        'vat' => $item->vat,
                        'sub_total' => $item->sub_total,
                        'grand_total' => $item->grand_total,
                        'issue_date' => $req->issue_date,
                    ];
                    ChildCreditNote::create($childCreditNotes);
                }
            }

            //delete
            $this->deleteCreditNoteDetail($req);
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success', 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'credit note create unsuccess!');
            Log::error("Error: Admin/InvoiceController > credit note on save | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }
    public function dataCustomerEncode($customerId)
    {
        $data = Customer::find($customerId);
        return json_encode($data);
    }
    public function deleteCreditNoteDetail($req)
    {
        //delete
        if (count($req->deleteItemID) > 0) {
            foreach ($req->deleteItemID as $_id) {
                $itemFind = CreditNoteDetail::find($_id);
                $itemFind->forceDelete();
            }
        }
    }

    public function viewDetail(Request $req, $id)
    {
        Log::info("Start: Admin/CreditNoteController > viewDetail | admin:");
        try {
            $data = $this->queryViewDetailInvoice($id);
            $contract_number = [];
            $contract_number_item = null;
            if ($data->invoices &&  $data->invoices->multiple_po_id && count(json_decode($data->invoices->multiple_po_id)) > 0) {
                foreach (json_decode($data->invoices->multiple_po_id) as $item) {
                    $contract_number_item = Purchase::find($item)->contract_number;
                    if ($contract_number_item) {
                        array_push($contract_number, Purchase::find($item)->contract_number);
                    }
                }
                if (count($contract_number) > 0) {
                    $data->contract_number = implode(",", $contract_number);
                } else {
                    $data->contract_number = '';
                }
            } else {
                $data->contract_number = $data?->purchase?->contract_number;
            }
            $bankAccount = BankAccount::where('status', 1)->get();
            return response()->json([
                'bankAccount' => $bankAccount,
                'data' => $data,
                'message' => true,
                'server_connection_status' => $req->btn_type == "true" ? $this->serverConnection->ServerLogin() : ''
            ]);
        } catch (Exception $error) {
            Log::error("Error: Admin/CreditNoteController > viewDetail | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function queryPickUpInvoice($invoice_number)
    {
        $data = Invoice::with([
            'purchase' => function ($q) {
                $q->with("project");
            },
            'customer',
            'invoiceDetail' => function ($q) {
                $q->with("service");
            }
        ])->where('invoice_number',  $invoice_number)->withTrashed()->first();
        if (isset($data) && $data) {
            $data->total_grand_kh = 0;
            $data->total_price_kh = 0;
            $data->vat_kh = 0;
            $data->contact = Contact::first();
            $data->check_rate_first = 0;
            $data->check_rate_seconde = 0;
            $data->purchase_type = $data?->purchase?->type_id == 2 ? true : false;
            $rateData = DB::table('rates')->first();
            $data->exchangeRateDefault = $rateData;
            //calKhmer
            $data->total_grand_kh = $data->total_grand * ($data->exchange_rate ?? $rateData->rate);
            $data->total_price_kh = $data->total_grand_kh / 1.1;
            $data->vat_kh = $data->total_grand_kh - $data->total_price_kh;

            if (isset($data->invoiceDetail) && count($data->invoiceDetail) > 0) {
                foreach ($data->invoiceDetail as $item) {
                    if ($item->rate_first) {
                        $data->check_rate_first += 1;
                    }
                    if ($item->rate_second) {
                        $data->check_rate_seconde += 1;
                    }
                }
            }
        }
        return $data;
    }

    public function queryViewDetailInvoice($id)
    {
        $data = CreditNote::with([
            'invoices',
            'purchase' => function ($q) {
                $q->with("project");
            },
            'customer',
            'creditNoteDetail' => function ($q) {
                $q->with("service");
            }
        ])->find($id);
        if (isset($data) && $data) {
            $data->total_grand_kh = 0;
            $data->total_price_kh = 0;
            $data->vat_kh = 0;
            $data->contact = Contact::first();
            $data->check_rate_first = 0;
            $data->check_rate_seconde = 0;
            $data->purchase_type = $data?->purchase?->type_id == 2 ? true : false;
            $rateData = DB::table('rates')->first();

            //calKhmer
            $data->total_grand_kh = $data->total_grand * ($data->exchange_rate ?? $rateData->rate);
            $data->total_price_kh = $data->total_grand_kh / 1.1;
            $data->vat_kh = $data->total_grand_kh - $data->total_price_kh;

            if(round($data->total_price*(10 / 100),2) != $data->vat){
                $data->vat_kh = $data->vat *$data->exchange_rate;
                $data->total_price_kh = $data->total_price * $data->exchange_rate;
            }

            if (isset($data->creditNoteDetail) && count($data->creditNoteDetail) > 0) {
                foreach ($data->creditNoteDetail as $item) {
                    if ($item->rate_first) {
                        $data->check_rate_first += 1;
                    }
                    if ($item->rate_second) {
                        $data->check_rate_seconde += 1;
                    }
                }
            }
        }
        return $data;
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/CreditNoteController > onUpdateStatus | admin: " . $req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = CreditNote::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status !== '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $error) {
            $status = false;
            DB::rollback();
            Log::error("Error: Admin/CreditNoteController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    //DMCSubmit
    public function DMCSubmitOld(DMCSubmitRequest $request)
    {
        $res = $this->serverConnection->ServerLogin();

        DB::beginTransaction();
        try {
            if ($res == "login_success") {
                $startDate = Carbon::now();
                $dateTime = $startDate->toDate()->format('Ymd');
                $file = $request->file;

                $data = CreditNote::find($request->invoice_id);

                $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
                $invoicePathSubmitDMCByTypeProject = ($configDMCPath?->mainDev ?? '') . $configDMCPath->infra_Credit_Note;

                $nameFileDMC = $dateTime . ' ' . $data->credit_note_number . '.pdf';
                $datePathUpload = Carbon::now()->format('Y/m');

                //submitDMC
                $this->serverConnection->dmcFile($invoicePathSubmitDMCByTypeProject, $file, $nameFileDMC, function ($result, $err) use ($datePathUpload, $file, $nameFileDMC, $request, $data, $res) {
                    if ($result == true) {

                        $getFile = UploadFile::uploadFileDMC('/credit_note/' . $datePathUpload, $file, $nameFileDMC);
                        $startDate = Carbon::now();

                        $year = $this->formatDate($startDate, 'Y');
                        $month = $this->formatDate($startDate, 'm');
                        $day = $this->formatDate($startDate, 'd');

                        $docItem = [
                            'invoice_id' => $request->invoice_id,
                            'credit_note_id' => $data->id,
                            'year' => $year,
                            'month' => $month,
                            'day'   => $day,
                            'file_name' => $nameFileDMC,
                            'file_path' => $getFile,
                            'file_type' => 'credit_note',
                            'extension_type' => 'pdf',
                            'from_date' => $this->formatDate($startDate, 'Y-m-d'),
                            'to_date'   => $this->formatDate($startDate, 'Y-m-d'),
                            'user_id'   => Auth::user()->id,
                        ];

                        HistoryDmcSendFile::create($docItem);
                        $data->update([
                            'doc_status' => 'is_send'
                        ]);

                        //sendMail
                        //$this->sendMail($dataHistory);

                        $this->message = 'success';
                    } else {
                        $this->message = 'unsuccess';
                    }
                });
                Session::flash($this->message ? 'success' : 'warning', `Credit note dmc submit ($this->message ? 'success' : 'unsuccess')`);
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
            Session::flash('warning', 'Credit note dmc submit unsuccess!');
            return response()->json([
                'data' => null,
                'message' => 'unsuccess',
                'connection_status' => $res
            ]);
        }
    }

    public function DMCSubmit(DMCSubmitRequest $request)
    {
        $res = $this->serverConnection->ServerLogin();
        DB::beginTransaction();
        try {
            if ($res == "login_success") {
                $data = $this->queryViewDetailInvoice($request->invoice_id);
                $bankAccounts = BankAccount::where('status', 1)->get();
                $data->dataCustomer = $data?->data_customer ? (object) json_decode($data?->data_customer) : $data?->customer;
                $project = $data?->purchase?->project;
                $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');

                //generateFilePathAndNameFile
                $fileObject = $this->makeDirWthNotExit($data->credit_note_number);

                if ($data->purchase && $data->purchase->project_id == 2) {
                    $PathSubmitDMCByTypeProject = $configDMCPath->submarine_Credit_Note;
                } else {
                    $PathSubmitDMCByTypeProject = $configDMCPath->infra_Credit_Note;
                }

                $typeView = "zero-rate";
                if (!$data->purchase_type) {
                    if ($data->check_rate_first > 0 && $data->check_rate_seconde == 0) {
                        $typeView = "one-rate";
                    } else if (($data->check_rate_seconde > 0 && $data?->check_rate_first == 0) || ($data?->check_rate_seconde > 0 && $data?->check_rate_first > 0)) {
                        $typeView = "two-rate";
                    }
                } else {
                    $typeView = "sale";
                }
                $htmlView = $this->layout . 'detail.generateFileSendDMC.' . $typeView;
                $pdf = Pdf::loadView($htmlView, compact('data', 'bankAccounts'));
                $pdf->save($fileObject->path_file_url);

                $this->serverConnection->dmcFile($PathSubmitDMCByTypeProject, $fileObject->path_file_url, $fileObject->file_name, function ($result, $err) use ($request, $fileObject, $data, $res) {
                    $this->message = $err;
                    if ($result) {
                        $year = $this->formatDate($fileObject->date, 'Y');
                        $month = $this->formatDate($fileObject->date, 'm');
                        $day = $this->formatDate($fileObject->date, 'd');
                        $docItem = [
                            'invoice_id' => $request->invoice_id,
                            'credit_note_id' => $data->id,
                            'year' => $year,
                            'month' => $month,
                            'day'   => $day,
                            'file_name' => $fileObject->file_name,
                            'file_path' => $fileObject->path_dir,
                            'file_type' => 'credit_note',
                            'extension_type' => 'pdf',
                            'from_date' => $this->formatDate($fileObject->date, 'Y-m-d'),
                            'to_date'   => $this->formatDate($fileObject->date, 'Y-m-d'),
                            'user_id'   => Auth::user()->id,
                        ];
                        HistoryDmcSendFile::create($docItem);
                        CreditNote::find($request->invoice_id)->update([
                            'doc_status' => 'is_send'
                        ]);
                        //sendMail
                        //$this->sendMail($fileObject->path_file_url);

                        //AlertSuccess
                        $flashStatus = ($this->message == 'success') ? 'success' : 'error';
                        $flashMessage = 'Invoice dmc submit ' . $this->message;
                        Session::flash($flashStatus, $flashMessage);
                    } else {
                        File::delete($fileObject->path_file_url);
                    }
                });
            }
            DB::commit();
            return response()->json([
                'data' => null,
                'message' => $this->message ?? 'unsuccess',
                'connection_status' => $this->message ?? 'unsuccess',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'data' => null,
                'message' => 'unsuccess',
                'connection_status' => $res,
                'system_err' => $e->getMessage()
            ]);
        }
    }

    public function makeDirWthNotExit($number)
    {
        $startDate = Carbon::now();
        $dateTime = $startDate->toDate()->format('Ymd');
        $nameFileDMC = $dateTime . ' ' . ($number ?? "_") . '.pdf';
        $datePathUpload = Carbon::now()->format('Y/m');
        $pathDir =  '/credit_note/' . $datePathUpload . '/' . $nameFileDMC;
        $path =  public_path('uploads' . $pathDir);
        $dirname = dirname($path);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
        return (object)[
            "date" => $startDate,
            "file_name" => $nameFileDMC,
            "path_dir"  => $pathDir,
            "path_file_url" => $path,
        ];
    }
    public function sendMail($dataFile)
    {
        $files[] = $dataFile;
        $u = Auth::user();
        $dataEmail = SendMail::where('status', 1)->pluck('email');
        foreach ($dataEmail as $mail) {
            Mail::to($mail)->send(
                new InvoiceDocSend(
                    Auth::user(),
                    $files,
                    $mail,
                    'Credit note'
                )
            );
        }
    }
    public function DocEdit($id)
    {
        Log::info("Start: Admin/CreditNoteController > viewDetailDocEdit | admin:");
        try {
            $data = $this->viewDocQuery($id);
            $check_rate_first = 0;
            $check_rate_seconde = 0;
            $purchase_type = false;

            if ($data['invoice']->purchase->type_id == 2) {
                $purchase_type = true;
            }
            foreach ($data['invoice_detail'] as $item) {
                if ($item->rate_first) {
                    $check_rate_first += 1;
                }
                if ($item->rate_second) {
                    $check_rate_seconde += 1;
                }
            }
            $data['check_rate_first'] = $check_rate_first;
            $data['check_rate_seconde'] = $check_rate_seconde;
            $data['purchase_type'] = $purchase_type;

            return view($this->layout . 'docSubmit.edit', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/CreditNoteController > viewDetailDocEdit | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }
}
