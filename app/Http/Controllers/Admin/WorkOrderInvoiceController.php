<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DMCSubmitRequest;
use App\Http\Requests\Admin\InvoiceRequest;
use App\Http\Requests\Admin\WorkOrderInvoiceRequest;
use App\Mail\InvoiceDocSend;
use App\Models\BankAccount;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\FTTHService;
use App\Models\HistoryDmcSendFile;
use App\Models\LogoControl;
use App\Models\Project;
use App\Models\SendMail;
use App\Models\UploadFile;
use App\Models\WorkOrderInvoice;
use App\Models\WorkOrderInvoiceDetail;
use App\Services\FTPConnectionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\File;

class WorkOrderInvoiceController extends Controller
{
    protected $layout = 'admin::pages.work-order.invoice.';
    private $disk = null;
    private $sftp = null;
    private $serverConnection = null;
    private $message = null;
    private $system_err = null;
    
    public function __construct(FTPConnectionService $ser)
    {
        $this->middleware('permission:work-order-invoice', ['only' => ['index']]);
        $this->middleware('permission:work-order-invoice', ['only' => ['onEdit', 'onUpdate']]);
        $this->middleware('permission:work-order-invoice', ['only' => ['onDelete']]);
        $this->middleware('permission:work-order-invoice', ['only' => ['viewDetail']]);
        $this->middleware('permission:work-order-invoice', ['only' => ['viewReceipt']]);
        $this->middleware('permission:work-order-invoice', ['only' => ['copy', 'onCopy']]);
        $this->middleware('permission:work-order-invoice', ['only' => ['DMCSubmit']]);
        $this->disk = Storage::disk('ftp');
        $this->serverConnection = $ser;
    }
    
    public function index(Request $req)
    {
        Log::info("Start: Admin/WorkOrderWorkOrderInvoiceController > index | admin: " . $req);
        $fromDate = $req->from_date ? $req->from_date : '';
        $toDate = $req->to_date ? $req->to_date : '';
        $search = $req->search;
        $data['status'] = $req->status;
        $data['projects'] = Project::where('status', 1)->orderBy('id', 'asc')->get();
        $sort = $req->sort ?? 'desc';
        try {
            if (!$req->status) {
                return redirect()->route('admin-work-order-invoice-list', 'all');
            }
            
            if ($req->status != 3) {
                if ($req->status == "all") {
                    $query = WorkOrderInvoice::with('customer')->where('status', '!=', 3);
                } else {
                    $query = WorkOrderInvoice::with('customer')->where('status', $req->status);
                }
            } else {
                $query = WorkOrderInvoice::with('customer')->onlyTrashed();
            }

            $data['data'] = $query->with(['invoiceDetail' => function ($q) {
                $q->with(["service"]);
            }])->where(function ($q) use ($fromDate, $toDate) {
                if (request('search')) {
                    $q->where('invoice_number', 'like', '%' . request('search') . '%');
                    $q->orWhereHas('customer', function ($q) {
                        $q->where('name_en', 'like', '%' . request('search') . '%');
                    });
                    $q->orWhereHas('order', function ($q) {
                        $q->where('order_number', 'like', '%' . request('search') . '%');
                    });
                }
                if ($fromDate && $toDate) {
                    $q->whereDate('issue_date', '>=', $fromDate);
                    $q->whereDate('issue_date', '<=', $toDate);
                }
                if (request('search_project')) {
                    $q->whereHas('order',
                        function ($q) {
                            $q->where('project_id', request('search_project'));
                        }
                    );
                }
            })->orderBy('id', $sort)->paginate(25);
            $data['logoControl']=LogoControl::first();
            $data['rate'] = DB::table('rates')->first();
            return view($this->layout . 'index', $data);

        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderInvoiceController > index | message: " . $error->getMessage());
        }
    }

    public function onEdit(Request $req, $id)
    {
        Log::info("Start: Admin/WorkOrderInvoiceController > onEdit | admin: ");
        try {
            $data['invoice'] = WorkOrderInvoice::with([
                'invoiceDetail' => function ($q) {
                    $q->with('service');
                },
                'order' => function ($q) {
                    $q->with('project');
                }
            ])->find($id);

            $data['rate'] = DB::table('rates')->first();
            $data['ftthSevices'] = FTTHService::where("status", 1)->orderByDesc('id')->get();
            return response()->json($data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderInvoiceController > onEdit | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function onUpdate(WorkOrderInvoiceRequest $request)
    {
        Log::info("Start: Admin/WorkOrderInvoiceController > onUpdate | admin: " . $request);
        $orderDetails = isset($request->order_details) && $request->order_details ? json_decode($request->order_details) : [];
        $invoices = $request->all();
        $dataCustomer = $request?->data_customer ? $request->data_customer : $this->dataCustomerEncode($request->customer_id);
        $invoices['data_customer'] = $dataCustomer;
        $invoices['user_id'] = Auth::user()->id;
        DB::beginTransaction();
        try {
            $status = "Update success.";
            WorkOrderInvoice::find($request->id)->update($invoices);
            foreach ($orderDetails as $item) {
                $detail = [
                    'invoice_id' => $request->id,
                    'service_id' => $item->service_id->value,
                    'des' => $item->des->value,
                    'qty' => $item->qty->value,
                    'price' => $item->price->value,
                    'uom' => $item->uom->value,
                    'rate_first' => $item?->rate_first->value ? $item?->rate_first->value : null,
                    'rate_second' => $item?->rate_second->value ? $item?->rate_second->value : null,
                    'amount' => $item->amount->value ?? 0,
                ];
                if ($item?->invoice_detail_id) {
                    WorkOrderInvoiceDetail::find($item->invoice_detail_id)->update($detail);
                } else {
                    WorkOrderInvoiceDetail::create($detail);
                }
                //deleteUpdateInvoiceDetail
                $this->deleteInvoiceDetail($request);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success']);
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Update unsuccess!');
            Log::error("Error: Admin/WorkOrderInvoiceController > onCopy | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }

    public function deleteInvoiceDetail($req)
    {
        //delete
        if (count($req->invoice_detail_arr_delete) > 0) {
            foreach ($req->invoice_detail_arr_delete as $id) {
                WorkOrderInvoiceDetail::where('id', $id)->forceDelete();
            }
        }
    }

    public function copy($id)
    {
        Log::info("Start: Admin/WorkOrderInvoiceController > copy | admin: ");
        try {
            if ($id) {
                $data['invoice'] = WorkOrderInvoice::with(['invoiceDetail' => function ($q) {
                    $q->with('service');
                },])->find($id);
                $data['rate'] = DB::table('rates')->first();
                $data['project'] = $data['invoice']->order->project->name;

                return response()->json($data);
            }
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderInvoiceController > copy | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onCopy(WorkOrderInvoiceRequest $req)
    {
        Log::info("Start: Admin/WorkOrderInvoiceController > onCopy | admin: ");
        $orderDetails = isset($req->order_details) && $req->order_details ? json_decode($req->order_details) : [];
        $invoices = $req->all();
        $dataCustomer = $req->data_customer ? $req->data_customer : $this->dataCustomerEncode($req->customer_id);
        $invoices['data_customer'] = $dataCustomer;
        $invoices['user_id'] = Auth::user()->id;
        DB::beginTransaction();
        try {
            $status = "Copy success.";
            $data = WorkOrderInvoice::create($invoices);
            foreach ($orderDetails as $item) {
                $detail = [
                    'invoice_id' => $data->id,
                    'service_id' => $item->service_id->value,
                    'des' => $item->des->value,
                    'qty' => $item->qty->value,
                    'price' => $item->price->value,
                    'uom' => $item->uom->value,
                    'rate_first' => $item?->rate_first->value ? $item?->rate_first->value : null,
                    'rate_second' => $item?->rate_second->value ? $item?->rate_second->value : null,
                    'amount' => $item->amount->value ?? 0,
                ];
                WorkOrderInvoiceDetail::create($detail);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success']);
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Copy unsuccess!');
            Log::error("Error: Admin/WorkOrderInvoiceController > onCopy | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }

    public function dataCustomerEncode($customerId)
    {
        $data = Customer::find($customerId);
        return json_encode($data);
    }

    public function viewReceipt($id)
    {
        Log::info("Start: Admin/WorkOrderInvoiceController > viewReceipt | admin: ");
        try {
            $data['invoice'] = WorkOrderInvoice::with('invoiceDetail', 'customer')->find($id);
            $data['invoice_detail'] = WorkOrderInvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();

            return view($this->layout . 'receipt', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderInvoiceController > viewReceipt | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function viewDetail(Request $req, $id)
    {
        Log::info("Start: Admin/WorkOrderInvoiceController > viewDetail | admin: ");
        try {
            $data = $this->queryViewDetailInvoice($id);
            $bankAccount=BankAccount::where('status',1)->get();
            return response()->json([
                'bankAccount' => $bankAccount,
                'data' => $data,
                'message' => true,
                'server_connection_status' => $req->btn_type == "true" ? $this->serverConnection->ServerLogin() : ''
            ]);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderInvoiceController > viewDetail | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function queryViewDetailInvoice($id)
    {
        $data = WorkOrderInvoice::with([
            'order' => function ($q) {
                $q->with("project");
            },
            'customer',
            'invoiceDetail' => function ($q) {
                $q->with("service");
            }
        ])->withTrashed()->find($id);

        $data->total_grand_kh = 0;
        $data->total_price_kh = 0;
        $data->vat_kh = 0;
        $data->contact = Contact::first();
        $data->check_rate_first = 0;
        $data->check_rate_seconde = 0;
        $data->order_type = $data?->order?->type_id == 2 ? true : false;
        $rateData = DB::table('rates')->first();

        //calKhmer
        $data->total_grand_kh = $data->total_grand * ($data->exchange_rate ?? $rateData->rate);
        $data->total_price_kh = $data->total_grand_kh / 1.1;
        $data->vat_kh = $data->total_grand_kh - $data->total_price_kh;

        if(round($data->total_price*(10 / 100),2) != $data->vat){
            $data->vat_kh = $data->vat *$data->exchange_rate;
            $data->total_price_kh = $data->total_price * $data->exchange_rate;
        }

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
        return $data;
    }

    public function onDelete(Request $req)
    {
        Log::info("Start: Admin/WorkOrderInvoiceController > onDelete | admin: " . $req);
        DB::beginTransaction();
        try {
            WorkOrderInvoice::find($req->id)->delete();
            DB::commit();
            $status = "Successful!";
            Session::flash("success", "Void success");
            return redirect()->back();
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderInvoiceController > onDelete | message: " . $error->getMessage());
            DB::rollback();
            Session::flash("success", "Void unsuccess!");
            return redirect()->back();
        }
    }

    //DMC
    public function DMCSubmitOld(DMCSubmitRequest $request)
    {
        $res = $this->serverConnection->ServerLogin();
        DB::beginTransaction();
        try {
            if ($res == "login_success") {
                $startDate = Carbon::now();
                $dateTime = $startDate->toDate()->format('Ymd');

                $file = $request->file;

                $data = WorkOrderInvoice::withTrashed()->find($request->invoice_id);
                $project = $data?->order?->project;

                $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
                $invoicePathSubmitDMCByTypeProject = ($configDMCPath?->mainDev ?? '') . ($project->id == 2 ? $configDMCPath->submarine_Invoice : $configDMCPath->infra_Invoice);

                $nameFileDMC = $dateTime . ' ' . $data->invoice_number . '.pdf';
                $datePathUpload = Carbon::now()->format('Y/m');

                $this->serverConnection->dmcFile($invoicePathSubmitDMCByTypeProject, $file, $nameFileDMC, function ($result, $err) use ($datePathUpload, $file, $nameFileDMC, $request, $data, $res) {
                    $this->system_err = $err;
                    if ($result == true) {
                        $getFile = UploadFile::uploadFileDMC('/invoice/' . $datePathUpload, $file, $nameFileDMC);

                        $startDate = Carbon::now();
                        $year = $this->formatDate($startDate, 'Y');

                        $month = $this->formatDate($startDate, 'm');
                        $day = $this->formatDate($startDate, 'd');

                        $docItem = [
                            'invoice_id' => $request->invoice_id,
                            'year' => $year,
                            'month' => $month,
                            'day'   => $day,
                            'file_name' => $nameFileDMC,
                            'file_path' => $getFile,
                            'file_type' => 'invoice',
                            'extension_type' => 'pdf',
                            'from_date' => $this->formatDate($startDate, 'Y-m-d'),
                            'to_date'   => $this->formatDate($startDate, 'Y-m-d'),
                            'user_id'   => Auth::user()->id,
                        ];

                        HistoryDmcSendFile::create($docItem);

                        $data->update([
                            'doc_status' => $request->typeBtnStatus == 'void' ? 'is_void' : 'is_send'
                        ]);
                        //sendMail
                        //$this->sendMail($dataHistory);

                        $this->message = 'success';
                    } else {
                        $this->message = 'unsuccess';
                    }
                });
                Session::flash('success', 'Invoice dmc submit success');
                DB::commit();
                return response()->json([
                    'data' => null,
                    'message' => $this->message,
                    'connection_status' => $res,
                    'system_err' => $this->system_err
                ]);
            } else {
                return response()->json([
                    'data' => null,
                    'message' => 'unsuccess',
                    'connection_status' => $res,
                    'system_err' => $this->system_err
                ]);
            }
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

    public function DMCSubmit(DMCSubmitRequest $request)
    {
        $res = $this->serverConnection->ServerLogin();
        DB::beginTransaction();
        try {
            if ($res == "login_success") {
                $data = $this->queryViewDetailInvoice($request->invoice_id);
                $bankAccounts=BankAccount::where('status',1)->get();
                $data->dataCustomer = $data?->data_customer ? (object) json_decode($data?->data_customer) : $data?->customer;
                $project = $data?->order?->project;
                $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
                $invoiceNumber = $data->deleted_at ? $data->invoice_number . '_Void' : $data->invoice_number;
                
                $issueDate = Carbon::parse($data->issue_date)->format('Ymd');
                //generateFilePathAndNameFile
                $fileObject = $this->makeDirWthNotExit($invoiceNumber, $issueDate);

                if ($data->deleted_at) {
                    $PathSubmitDMCByTypeProject = ($project->id == 2 ? $configDMCPath->submarine_invoice_void : $configDMCPath->infra_invoice_void);
                } else {
                    $PathSubmitDMCByTypeProject = ($project->id == 2 ? $configDMCPath->submarine_Invoice : $configDMCPath->infra_Invoice);
                }

                $typeView = "zero-rate";
                if (!$data->order_type) {
                    if ($data->check_rate_first > 0 && $data->check_rate_seconde == 0) {
                        $typeView = "one-rate";
                    } else if (($data->check_rate_seconde > 0 && $data?->check_rate_first == 0) || ($data?->check_rate_seconde > 0 && $data?->check_rate_first > 0)) {
                        $typeView = "two-rate";
                    }
                } else {
                    $typeView = "sale";
                }
                $htmlView = $this->layout . 'detail.generateFileSendDMC.' . $typeView;
                $pdf = Pdf::loadView($htmlView, compact('data','bankAccounts'));
                if ($data->deleted_at) {
                    $pdf->mpdf->setWatermarkText('VOID');
                    $pdf->mpdf->showWatermarkText = true;
                    $pdf->mpdf->watermarkTextAlpha = 0.1;
                    $pdf->mpdf->watermark_font = 'battambang';
                }
                $pdf->save($fileObject->path_file_url);

                $this->serverConnection->dmcFile($PathSubmitDMCByTypeProject, $fileObject->path_file_url, $fileObject->file_name, function ($result, $err) use ($request, $fileObject, $data, $res) {
                    $this->message = $err;
                    if ($result) {
                        $year = $this->formatDate($fileObject->date, 'Y');
                        $month = $this->formatDate($fileObject->date, 'm');
                        $day = $this->formatDate($fileObject->date, 'd');
                        $docItem = [
                            'invoice_id' => $request->invoice_id,
                            'year' => $year,
                            'month' => $month,
                            'day'   => $day,
                            'file_name' => $fileObject->file_name,
                            'file_path' => $fileObject->path_dir,
                            'file_type' => $data->deleted_at ? 'invoice_void' : 'invoice',
                            'extension_type' => 'pdf',
                            'from_date' => $this->formatDate($fileObject->date, 'Y-m-d'),
                            'to_date'   => $this->formatDate($fileObject->date, 'Y-m-d'),
                            'user_id'   => Auth::user()->id,
                            'is_ftth'   => 1,
                        ];
                        HistoryDmcSendFile::create($docItem);
                        WorkOrderInvoice::withTrashed()->find($request->invoice_id)->update([
                            'doc_status' => $request->typeBtnStatus == 'void' ? 'is_void' : 'is_send'
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

    public function makeDirWthNotExit($number, $issueDate = null)
    {
        $startDate = Carbon::now();
        $dateTime = $startDate->toDate()->format('Ymd');
        // $nameFileDMC = $dateTime . ' ' . ($number ?? "_") . '.pdf';
        $nameFileDMC = $issueDate . ' ' . ($number ?? "_") . '.pdf';
        $datePathUpload = Carbon::now()->format('Y/m');
        $pathDir =  '/invoice/' . $datePathUpload . '/' . $nameFileDMC;
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
                    'Void'
                )
            );
        }
    }
    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }
    public function destroy(Request $req)
    {
        $id = $req->id;
        DB::beginTransaction();
        try {
            WorkOrderInvoice::withTrashed()->find($id)->forceDelete();
            WorkOrderInvoiceDetail::where('invoice_id', $id)->forceDelete();
            DB::commit();
            Session::flash('success', 'Delete success');
            return response()->json('success');
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Delete unsuccess!');
            return response()->json('unsuccess');
        }
    }

    public function fetchFTTHService()
    {
        $services = FTTHService::where('status', 1)->orderByDesc('id')->get();
        return response()->json($services);
    }
}
