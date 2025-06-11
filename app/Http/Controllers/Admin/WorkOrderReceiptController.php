<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\receiptExport;
use App\Http\Requests\Admin\WorkOrderInvoiceCreateReceiptRequest;
use App\Http\Requests\Admin\WorkOrderReceiptEditStatusRequest;
use App\Http\Requests\Admin\WorkOrderReceiptRequest;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\LogoControl;
use App\Models\Project;
use App\Models\WorkOrderCreditNote;
use App\Models\WorkOrderInvoice;
use App\Models\WorkOrderInvoiceDetail;
use App\Models\WorkOrderReceipt;
use App\Models\WorkOrderReceiptDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WorkOrderReceiptController extends Controller
{
    protected $layout = 'admin::pages.work-order.receipt.';

    public function __construct()
    {
        $this->middleware('permission:work-order-receipt', ['only' => ['index']]);
        $this->middleware('permission:work-order-receipt', ['only' => ['onUpdate', 'onEdit', 'onEditStatus']]);
        $this->middleware('permission:work-order-receipt', ['only' => ['viewReceipt']]);
    }

    public function index(Request $request)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > index | admin: ");
        try {
            $fromDate = $request->from_date ? $request->from_date : '';
            $toDate = $request->to_date ? $request->to_date : '';
            $data['status'] = $request->status;
            $search = $request->search ? $request->search : '';
            if (!$request->status) {
                return redirect()->route('admin-work-order-receipt-list', 1);
            }
            $data['projects'] = Project::where('status', 1)->get();
            $data['data'] = WorkOrderReceipt::with(["invoices" => function ($q) {
                $q->with(["invoiceDetail" => function ($q) {
                    $q->with(["service"]);
                }, "order" => function ($q) {
                    $q->with(["type"]);
                }]);
            }, "customer", "type", "receiptDetail" => function ($q) {
                $q->with(["service"]);
            }])->where(function ($q) use ($fromDate, $toDate, $search, $request) {
                if ($search) {
                    $q->where('receipt_number', $search);
                    $q->orWhereHas('invoices', function ($q) {
                        $q->where('invoice_number', request('search'));
                    });
                    $q->orWhereHas('customer', function ($q) {
                        $q->where('name_en', 'like', '%' . request('search') . '%');
                        $q->orWhere('name_kh', 'like', '%' . request('search') . '%');
                        $q->orWhere('customer_code', 'like', '%' . request('search') . '%');
                    });
                }
                if (request('search_project')) {
                    $q->whereHas('invoices.order', function ($q) {
                        $q->where('project_id', request('search_project'));
                    });
                }
                if ($fromDate && $toDate) {
                    $q->whereBetween(DB::raw('date(issue_date)'), [$fromDate, $toDate]);
                }
                if ($request->status != "all") {
                    $q->where('status', $request->status);
                }
            })->orderBy('id', 'desc')->paginate(25);
            $data['logoControl']=LogoControl::first();
            return view($this->layout . 'index', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderReceiptController > index | message: " . $error->getMessage());
        }
    }

    public function onSave(WorkOrderInvoiceCreateReceiptRequest $request)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > onSave | admin: " . $request);
        $invoice = WorkOrderInvoice::find($request->invoice_id);
        $receipt = [
            'receipt_number' => $request->receipt_number,
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'data_customer' => $invoice->data_customer ?? $invoice->customer,
            'total_qty' => $invoice->total_qty,
            'total_price' => $request->total_price,
            'vat' => $request->vat,
            'partial_payment' => $request->partial_payment,
            'total_grand' => $request->total_grand,
            'paid_amount' => $request->paid_amount,
            'debt_amount' => $request->debt_amount,
            'note' => $request->note,
            'issue_date' => $request->issue_date,
            'payment_status' => 'portal',
            "status" => 1,
            "user_id" => Auth::user()->id,
            "status_type"  => "invoice",
        ];
        DB::beginTransaction();
        try {
            $status = "Create success.";
            $data = WorkOrderReceipt::create($receipt);
            foreach ($invoice->invoiceDetail as $item) {
                $detail = [
                    'receipt_id' => $data->id,
                    'service_id' => $item->service_id,
                    'des' => $item->des,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'uom' => $item->uom,
                    'amount' => $item->amount ?? 0,
                ];
                WorkOrderReceiptDetail::create($detail);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['status'=>'success','message' => $status, 'data' => null]);
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/WorkOrderReceiptController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onSaveNew(WorkOrderReceiptRequest $request)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > onSaveNew | admin: " . $request);
        if ($request->receipt_from == 'invoice') {
            $invoice = WorkOrderInvoice::find($request->invoice_ref_id);
            $invoiceId = $invoice->id;
            $customerId = $invoice->customer_id;
        } else if ($request->receipt_from == 'credit_note') {
            $creditNote = WorkOrderCreditNote::find($request->invoice_ref_id);
            $invoiceId = $creditNote->id;
            $customerId = $creditNote->customer_id;
        } else {
            $customerId = $request->customer_id;
        }
        $customer = Customer::find($request->customer_id);
        $receipt = [
            'receipt_number' => $request->receipt_number,
            'receipt_from' => $request->receipt_from ?? null,
            'invoice_id' => $invoiceId ?? null,
            'customer_id' => $customerId,
            'data_customer' => json_encode($customer),
            'total_qty' => $request->total_qty,
            'total_price' => $request->total_price,
            'vat' => $request->vat,
            'partial_payment' => $request->partial_payment,
            'total_grand' => $request->total_grand,
            'paid_amount' => $request->paid_amount,
            'debt_amount' => $request->debt_amount,
            'note' => $request->note,
            'issue_date' => $request->issue_date,
            'payment_status' => 'portal',
            "status" => 1,
            "user_id" => Auth::user()->id,
            "status_type"  => "receipt",
            "type_id"  => 8,
        ];
        $details = isset($request->details) && $request->details ? json_decode($request->details) : [];
        DB::beginTransaction();
        try {
            $status = "Create success.";
            $data = WorkOrderReceipt::create($receipt);
            foreach ($details as $item) {
                $detailItem = [
                    'receipt_id' => $data->id,
                    'service_id' => $item->service_id->value,
                    'des' => $item->des->value,
                    'qty' => $item->qty->value,
                    'price' => $item->price->value,
                    'uom' => $item->uom->value,
                    'amount' => $item->amount->value ?? 0,
                ];
                WorkOrderReceiptDetail::create($detailItem);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['status'=>'success','message' => $status, 'data' => null]);
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/WorkOrderReceiptController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onEdit($id)
    {
        $data['receipt'] = WorkOrderReceipt::find($id);
        $dateValid = checkValidate($data['receipt']->issue_date);
        if (!$dateValid) {
            return redirect()->route('admin-work-order-receipt-list', 1);
        }
        Log::info("Start: Admin/WorkOrderReceiptController > onEdit | admin: ");
        try {
            $data['invoice'] = $data['receipt']->invoices;
            $data['invoice_detail'] = WorkOrderInvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();
            return view($this->layout . 'edit', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderReceiptController > onEdit | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdate(WorkOrderReceiptRequest $request)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > onUpdate | admin: " . $request);
        $id = $request->id;
        DB::beginTransaction();
        try {
            if ($request->status_type == "receipt") {
                $this->updateReceipt($request, $id);
            } else {
                $this->updateInvoice($request, $id);
            }
            DB::commit();
            Session::flash("success", "Receipt update success! ");
            return response()->json(['message' => 'success']);
        } catch (\Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/WorkOrderReceiptController > onUpdate | message: " . $error->getMessage());
            Session::flash('warning', 'Update unsuccess!');
            return response()->json(['message' => 'unsuccess']);
        }
    }

    public function updateInvoice($request, $id)
    {
        $customer = Customer::find($request->customer_id);
        $receipt = [
            'receipt_number' => $request->receipt_number,
            'customer_id' => $customer->id,
            'type_id' => $request->type_id,
            'data_customer' => json_encode($customer),
            'total_qty' => $request->total_qty,
            'total_price' => $request->total_price,
            'vat' => $request->vat,
            'total_grand' => $request->total_grand,
            'paid_amount' => $request->paid_amount,
            'debt_amount' => $request->debt_amount,
            'note' => $request->note,
            'issue_date' => $request->issue_date,
            'payment_status' => 'portal',
            "status" => 1,
            "user_id" => Auth::user()->id,
            "status_type"  => "invoice",
        ];
        WorkOrderReceipt::find($id)->update($receipt);
    }

    public function updateReceipt($request, $id)
    {
        $customer = Customer::find($request->customer_id);
        $details = isset($request->details) && $request->details ? json_decode($request->details) : [];
        $dataCustomer = json_encode($customer);
        $receipt = [
            'receipt_number' => $request->receipt_number,
            'customer_id' => $customer->id,
            'type_id' => $request->type_id,
            'data_customer' => $dataCustomer,
            'total_qty' => $request->total_qty,
            'total_price' => $request->total_price,
            'vat' => $request->vat,
            'partial_payment' => $request->partial_payment,
            'total_grand' => $request->total_grand,
            'paid_amount' => $request->paid_amount,
            'debt_amount' => $request->debt_amount,
            'note' => $request->note,
            'issue_date' => $request->issue_date,
            "user_id" => Auth::user()->id,
            "status_type"  => $request->status_type,
        ];
        WorkOrderReceipt::find($id)->update($receipt);

        $receiptDetailIds = [];
        foreach ($details as $item) {
            WorkOrderReceiptDetail::updateOrCreate(
                [
                    'id' => $item->receipt_detail_id,
                    'receipt_id' => $id
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
            array_push($receiptDetailIds, $item->receipt_detail_id);
        }
        WorkOrderReceiptDetail::where('receipt_id', $id)->whereNotIn('id', $receiptDetailIds)->forceDelete();
    }

    public function deleteDetail($req)
    {
        if (count($req->receipt_detail_arr_delete) > 0) {
            foreach ($req->receipt_detail_arr_delete as $id) {
                WorkOrderReceiptDetail::where('id', $id)->forceDelete();
            }
        }
    }

    public function onEditStatus($id)
    {
        $data['receipt'] = WorkOrderReceipt::find($id);
        $dateValid = checkValidate($data['receipt']->issue_date);
        if (!$dateValid) {
            return redirect()->route('admin-work-order-receipt-list', 1);
        }
        Log::info("Start: Admin/WorkOrderReceiptController > onEditStatus | admin: ");
        try {
            return view($this->layout . 'edit-status', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderReceiptController > onEditStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdateStatus(WorkOrderReceiptEditStatusRequest $request)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > onUpdateStatus | admin: " . $request);
        $status = $request->payment_status == 'portal' ? 1 : 2;
        $item = [
            "status" => $status,
            "payment_method" => $request->payment_method,
            "payment_status" => $request->payment_status,
            "payment_des" => $request->payment_des,
            "paid_date" => $request->paid_date,
            "user_id" => Auth::user()->id
        ];
        $status_message = $request->payment_status == 'portal' ? "Portal" : "Paid";
        DB::beginTransaction();
        try {
            $receipt = WorkOrderReceipt::find($request->id);
            $receipt->update($item);
            $invoice = WorkOrderInvoice::where('id', $receipt?->invoice_id)->first();
            if ($invoice) {
                $portal_amount = $receipt?->partial_payment ?? 0;
                $paid_amount = $receipt?->paid_amount ?? 0;
                $invoice->update([
                    'paid_amount' => $receipt->payment_status == 'portal' ? $portal_amount  : $paid_amount,
                    'paid_status' => $receipt->payment_status == 'portal' ? 'Portal' : 'Paid',
                ]);
            }
            DB::commit();
            Session::flash("success", "Edit payment " . $status_message);
            return response()->json(['message' => 'success']);
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Update unsuccess!');
            Log::error("Error: Admin/WorkOrderReceiptController > onUpdateStatus | message: " . $error->getMessage());
            return response()->json(['message' => 'unsuccess']);
        }
    }

    public function viewReceipt($id)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > viewReceipt | admin: ");
        try {
            $data['receipt'] = WorkOrderReceipt::find($id);
            $data['invoice'] = $data['receipt']->invoices;
            $data['invoice_detail'] = WorkOrderInvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();
            return view($this->layout . 'view', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderReceiptController > viewReceipt | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function exportReceiptExcel($id)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > exportReceiptExcel | admin: ");
        try {
            $data['receipt'] = WorkOrderReceipt::find($id);
            $data['invoice'] = $data['receipt']->invoices;
            $data['invoice_detail'] = WorkOrderInvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();
            return Excel::download(new receiptExport($data), 'receipt.xlsx');
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderReceiptController > exportReceiptExcel | message: " . $error->getMessage());
        }
    }

    public function pickUpWorkOrderInvoice($invoice_number = null)
    {
        try {
            $data = WorkOrderInvoice::with(['order.project', 'customer', 'invoiceDetail.service'])->where('invoice_number', $invoice_number)->first();
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

                $data->invoice_detail = $data->invoiceDetail;
            }
            return response()->json(['data' => $data, 'message' => true]);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderReceiptController > pickUpWorkOrderInvoice | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function pickUpWorkOrderCreditNote($invoice_number = null)
    {
        try {
            $data = WorkOrderCreditNote::with(['order.project', 'customer', 'invoiceDetail.service'])->where('credit_note_number', $invoice_number)->first();
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

                $data->invoice_detail = $data->invoiceDetail;
            }
            return response()->json(['data' => $data, 'message' => true]);
        } catch (\Exception $error) {
            Log::error("Error: Admin/WorkOrderReceiptController > pickUpWorkOrderCreditNote | message: " . $error->getMessage());
            return response()->json(['data' => null, 'message' => false]);
        }
    }

    public function onDelete(Request $req)
    {
        Log::info("Start: Admin/WorkOrderReceiptController > onDelete | admin: " . $req);
        $status = "Delete successful!";
        try {
            WorkOrderReceipt::find($req->id)->delete();
            WorkOrderReceiptDetail::where('receipt_id', $req->id)->forceDelete();
        } catch (\Exception $error) {
            $status = "Delete unsuccess!";
            Log::error("Error: Admin/WorkOrderReceiptController > onDelete | message: " . $error->getMessage());
        }
        Session::flash("success", $status);
        return redirect()->back();
    }
}
