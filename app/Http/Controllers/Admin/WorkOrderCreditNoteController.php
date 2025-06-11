<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DMCSubmitRequest;
use App\Http\Requests\Admin\WorkOrderCreditNoteRequest;
use App\Models\BankAccount;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\HistoryDmcSendFile;
use App\Models\LogoControl;
use App\Models\Project;
use App\Models\WorkOrderCreditNote;
use App\Models\WorkOrderCreditNoteDetail;
use App\Models\WorkOrderInvoice;
use App\Services\FTPConnectionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Session;

class WorkOrderCreditNoteController extends Controller
{
    private $serverConnection = null;
    private $message = null;
    public function __construct(FTPConnectionService $server)
    {
        $this->serverConnection = $server;
    }

    public function index(Request $request)
    {
        try {
            $data['status'] = $request->status;
            $search = $request->search ? $request->search : '';
            if (!$request->status) {
                return redirect()->route('admin-work-order-credit-note-list', 1);
            }
            if ($request->status != 'trash') {
                $query = WorkOrderCreditNote::where('status', $request->status);
            } else {
                $query = WorkOrderCreditNote::onlyTrashed();
            }
            $fromDate = $request->from_date ? $request->from_date : '';
            $toDate = $request->to_date ? $request->to_date : '';
            $data['projects'] = Project::where('status', 1)->get();
            $data['data'] = $query->with(['creditNoteDetails', 'order.project'])->where(function ($q) use ($search, $fromDate, $toDate) {
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
                        'order',
                        function ($q) {
                            $q->where('project_id', request('search_project'));
                        }
                    );
                }
            })
                ->orderBy('id', 'desc')
                ->paginate(25);
            $data['logoControl'] = LogoControl::first();
            return view('admin::pages.work-order.credit_note.index', $data);
        } catch (\Exception $e) {
            Log::error("Error: Admin/WorkOrderCreditNoteController > index | message: " . $e->getMessage());
        }
    }

    public function SelectInvoice(Request $request)
    {
        try {
            $data = WorkOrderInvoice::withTrashed()->with(["order" => function ($q) {
                $q->with(["project"]);
            }])->where(function (Builder $q) use ($request) {
                if ($request->search) {
                    $q->where('invoice_number', 'LIKE', '%' . $request->search . '%');
                }
            })
                ->orderBy('created_at', 'asc')
                ->take(12)->get();
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }

    public function pickUpInvoice($invoice_number)
    {
        try {
            $data = WorkOrderInvoice::with(['order.project', 'customer', 'invoiceDetail.service'])
                ->where('invoice_number', $invoice_number)
                ->withTrashed()
                ->first();

            if (isset($data) && $data) {
                $data->total_grand_kh = 0;
                $data->total_price_kh = 0;
                $data->vat_kh = 0;
                $data->contact = Contact::first();
                $rateData = DB::table('rates')->first();
                $data->exchangeRateDefault = $rateData;

                //calRiel
                $data->total_grand_kh = $data->total_grand * ($data->exchange_rate ?? $rateData->rate);
                $data->total_price_kh = $data->total_price * ($data->exchange_rate ?? $rateData->rate);
                $data->vat_kh = $data->vat * ($data->exchange_rate ?? $rateData->rate);
            }
            return response()->json(['data' => $data, 'message' => true]);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderCreditNoteController > pickUpInvoice | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function onSave(WorkOrderCreditNoteRequest $req)
    {
        $creditNoteDetails = isset($req->credit_note_details) && $req->credit_note_details ? json_decode($req->credit_note_details) : [];
        $creditNoteDetailIds = collect($creditNoteDetails)->pluck('id')->toArray();
        $itemCreditNote = $req->all();
        $customer = Customer::where('id', $req->customer_id)->first();
        $dataCustomer = $req->data_customer ? $req->data_customer : json_encode($customer);
        $itemCreditNote['data_customer'] = $dataCustomer;
        DB::beginTransaction();
        try {
            $status = "Credit note create success.";
            if (!$req->id) {
                $itemCreditNote['paid_status'] = 'Pending';
                $itemCreditNote['user_id'] = Auth::user()->id;
                $data = WorkOrderCreditNote::create($itemCreditNote);
            } else {
                $data = WorkOrderCreditNote::find($req->id);
                $data->update($itemCreditNote);
            }
            WorkOrderCreditNoteDetail::whereNotIn('id', $creditNoteDetailIds)->where('credit_note_id', $data->id)->delete();
            foreach ($creditNoteDetails as $item) {
                WorkOrderCreditNoteDetail::updateOrCreate(
                    [
                        'id' => $item->id,
                        'credit_note_id' => $data->id
                    ],
                    [
                        'service_id' => $item->service_id->value,
                        'des' => $item->des->value,
                        'qty' => $item->qty->value,
                        'price' => $item->price->value,
                        'uom' => $item->uom->value,
                        'amount' => $item->amount->value ?? 0,
                    ]
                );
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success', 'data' => null]);
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('error', 'Credit note create unsuccess!');
            Log::error("Error: Admin/WorkOrderCreditNoteController > onSave | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }

    public function onUpdateStatus(Request $req)
    {
        try {
            $data = WorkOrderCreditNote::find($req->id);
            $data->update(['status' => $req->status]);
            $status = $req->status == 1 ? 'Enable Success' : 'Disable Success';
            Session::flash('success', $status);
            return redirect()->back();
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderCreditNoteController > onUpdateStatus | message: " . $error->getMessage());
            Session::flash('error', 'Update Field!');
            return redirect()->back();
        }
    }

    public function viewDetail(Request $req, $id)
    {
        Log::info("Start: Admin/CreditNoteController > viewDetail | admin:");
        try {
            $data = $this->queryViewDetailCreditNote($id);
            $bankAccount = BankAccount::where('status', 1)->get();
            return response()->json([
                'bankAccount' => $bankAccount,
                'data' => $data,
                'message' => true,
                'server_connection_status' => $req->btn_type == "true" ? $this->serverConnection->ServerLogin() : ''
            ]);
        } catch (\Exception $error) {
            Log::error("Error: Admin/CreditNoteController > viewDetail | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function queryViewDetailCreditNote($id)
    {
        $data = WorkOrderCreditNote::with(['order.project', 'customer', 'creditNoteDetails.service', 'invoices'])->find($id);
        if (isset($data) && $data) {
            $data->total_grand_kh = 0;
            $data->total_price_kh = 0;
            $data->vat_kh = 0;
            $data->contact = Contact::first();
            $rateData = DB::table('rates')->first();

            //calKhmer
            $data->total_grand_kh = $data->total_grand * ($data->exchange_rate ?? $rateData->rate);
            $data->total_price_kh = $data->total_grand_kh / 1.1;
            $data->vat_kh = $data->total_grand_kh - $data->total_price_kh;

            if (round($data->total_price * (10 / 100), 2) != $data->vat) {
                $data->vat_kh = $data->vat * $data->exchange_rate;
                $data->total_price_kh = $data->total_price * $data->exchange_rate;
            }
        }
        return $data;
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

    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }

    public function DMCSubmit(DMCSubmitRequest $request)
    {
        $res = $this->serverConnection->ServerLogin();
        DB::beginTransaction();
        try {
            if ($res == "login_success") {
                $data = $this->queryViewDetailCreditNote($request->invoice_id);
                $bankAccounts = BankAccount::where('status', 1)->get();
                $data->dataCustomer = $data?->data_customer ? (object) json_decode($data?->data_customer) : $data?->customer;
                $project = $data?->order?->project;
                $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');

                //generateFilePathAndNameFile
                $fileObject = $this->makeDirWthNotExit($data->credit_note_number);

                $PathSubmitDMCByTypeProject = $configDMCPath->infra_Credit_Note;
                if ($data->order && $data->order->project_id == 2) {
                    $PathSubmitDMCByTypeProject = $configDMCPath->submarine_Credit_Note;
                } else {
                    $PathSubmitDMCByTypeProject = $configDMCPath->infra_Credit_Note;
                }

                $htmlView = 'admin::pages.work-order.credit_note.detail.dmc_file';
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
                            'is_ftth'   => 1,
                        ];
                        HistoryDmcSendFile::create($docItem);
                        WorkOrderCreditNote::find($request->invoice_id)->update(['doc_status' => 'is_send']);

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
}
