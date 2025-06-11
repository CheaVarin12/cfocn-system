<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\receiptExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvoiceCreateReceiptRequest;
use App\Http\Requests\Admin\ReceiptEditStatusRequest;
use App\Http\Requests\Admin\ReceiptRequest;
use App\Models\CreditNote;
use App\Models\Customer;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Session;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\InvoiceDetail;
use App\Models\LogoControl;
use App\Models\Project;
use App\Models\ReceiptDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ReceiptController extends Controller
{
    protected $layout = 'admin::pages.receipt.';

    public function __construct()
    {
        $this->middleware('permission:receipt-view', ['only' => ['index']]);
        $this->middleware('permission:receipt-update', ['only' => ['onUpdate', 'onEdit', 'onEditStatus']]);
        $this->middleware('permission:receipt-detail', ['only' => ['viewReceipt']]);
    }
    public function index(Request $request)
    {
        Log::info("Start: Admin/ReceiptController > index | admin: ");
        try {
            $fromDate = $request->from_date ? $request->from_date : '';
            $toDate = $request->to_date ? $request->to_date : '';
            $data['status'] = $request->status;
            $search = $request->search ? $request->search : '';
            if (!$request->status) {
                return redirect()->route('admin-Receipt-list', 1);
            }
            $data['projects'] = Project::where('status', 1)->get();
            $data['data'] = Receipt::with(["invoices" => function ($q) {
                $q->with(["invoiceDetail" => function ($q) {
                    $q->with(["service"]);
                }, "purchase" => function ($q) {
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
                    });
                }
                if (request('search_project')) {
                    $q->whereHas('invoices', function ($q) {
                        $q->whereHas(
                            'purchase',
                            function ($q) {
                                $q->where('project_id', request('search_project'));
                            }
                        );
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
        } catch (Exception $error) {
            Log::error("Error: Admin/ReceiptController > index | message: " . $error->getMessage());
        }
    }

    public function onSave(InvoiceCreateReceiptRequest $request)
    {
        Log::info("Start: Admin/ReceiptController > onSave | admin: " . $request);
        $invoice = Invoice::find($request->invoice_id);
        $receipt = [
            'receipt_number' => $request->receipt_number,
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'data_customer' => $invoice->data_customer ?? $invoice->customer,
            'total_qty' => $invoice->total_qty,
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
        DB::beginTransaction();
        try {
            $status = "Create success.";
            $data = Receipt::create($receipt);
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
                ReceiptDetail::create($detail);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-receipt-list', 1);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/ReceiptController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onSaveNew(ReceiptRequest $request)
    {
        Log::info("Start: Admin/ReceiptController > onSave | admin: " . $request);
        if ($request->receipt_from == 'invoice') {
            $invoice = Invoice::find($request->invoice_ref_id);
            $invoiceId = $invoice->id;
            $customerId = $invoice->customer_id;
        } else if ($request->receipt_from == 'credit_note') {
            $creditNote = CreditNote::find($request->invoice_ref_id);
            $invoiceId = $creditNote->id;
            $customerId = $creditNote->customer_id;
        } else {
            $customerId = $request->customer_id;
        }
        $customer = Customer::find($request->customer_id);
        $receipt = [
            'receipt_number' => $request->receipt_number,
            'receipt_from' => $request->receipt_from ?? null,
            'invoice_id' => $invoiceId ?? 0,
            'customer_id' => $customerId,
            'type_id' => $request->type_id,
            'data_customer' => json_encode($customer),
            'total_qty' => $request->total_qty,
            'total_price' => $request->total_price,
            'vat' => $request->vat,
            'total_grand' => $request->total_grand,
            'partial_payment' => $request->partial_payment,
            'paid_amount' => $request->paid_amount,
            'debt_amount' => $request->debt_amount,
            'note' => $request->note,
            'issue_date' => $request->issue_date,
            'payment_status' => 'portal',
            "status" => 1,
            "user_id" => Auth::user()->id,
            "status_type"  => "receipt",
        ];
        $details = isset($request->details) && $request->details ? json_decode($request->details) : [];
        DB::beginTransaction();
        try {
            $status = "Create success.";
            $data = Receipt::create($receipt);
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
                ReceiptDetail::create($detailItem);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['status'=>'success','message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/ReceiptController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onEdit($id)
    {
        $data['receipt'] = Receipt::find($id);
        $dateValid = checkValidate($data['receipt']->issue_date);
        if (!$dateValid) {
            return redirect()->route('admin-receipt-list', 1);
        }
        Log::info("Start: Admin/ReceiptController > onEdit | admin: ");
        try {
            $data['invoice'] = $data['receipt']->invoices;
            $data['invoice_detail'] = InvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();
            return view($this->layout . 'edit', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/ReceiptController > onEdit | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdate(ReceiptRequest $request, $id = null)
    {
        Log::info("Start: Admin/ReceiptController > onUpdate | admin: " . $request);

        $status = "Update success.";
        DB::beginTransaction();
        try {
            if ($request->status_type == "receipt") {
                $this->updateReceipt($request, $id);
            } else {
                $this->updateInvoice($request, $id);
            }
            DB::commit();
            Session::flash("success", "Receipt edit " . $status);
            return response()->json(['message' => 'success']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/ReceiptController > onUpdate | message: " . $error->getMessage());
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
            'partial_payment' => $request->partial_payment,
            'paid_amount' => $request->paid_amount,
            'debt_amount' => $request->debt_amount,
            'note' => $request->note,
            'issue_date' => $request->issue_date,
            'payment_status' => 'portal',
            "status" => 1,
            "user_id" => Auth::user()->id,
            "status_type"  => "invoice",
        ];
        Receipt::find($id)->update($receipt);
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
            'total_grand' => $request->total_grand,
            'partial_payment' => $request->partial_payment,
            'paid_amount' => $request->paid_amount,
            'debt_amount' => $request->debt_amount,
            'note' => $request->note,
            'issue_date' => $request->issue_date,
            "user_id" => Auth::user()->id,
            "status_type"  => $request->status_type,
        ];
        Receipt::find($request->id)->update($receipt);
        foreach ($details as $item) {
            $detail = [
                'receipt_id' => $request->id,
                'service_id' => $item->service_id->value,
                'des' => $item->des->value,
                'qty' => $item->qty->value,
                'price' => $item->price->value,
                'uom' => $item->uom->value,
                'amount' => $item->amount->value ?? 0,
            ];
            if ($item?->receipt_detail_id) {
                ReceiptDetail::find($item->receipt_detail_id)->update($detail);
            } else {
                ReceiptDetail::create($detail);
            }
            //deleteUpdateReceiptDetail
            $this->deleteDetail($request);
        }
    }

    public function deleteDetail($req)
    {
        //delete
        if (count($req->receipt_detail_arr_delete) > 0) {
            foreach ($req->receipt_detail_arr_delete as $id) {
                ReceiptDetail::where('id', $id)->forceDelete();
            }
        }
    }

    public function onEditStatus($id)
    {
        $data['receipt'] = Receipt::find($id);
        $dateValid = checkValidate($data['receipt']->issue_date);
        if (!$dateValid) {
            return redirect()->route('admin-receipt-list', 1);
        }
        Log::info("Start: Admin/ReceiptController > onEditStatus | admin: ");
        try {
            return view($this->layout . 'edit-status', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/ReceiptController > onEditStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
    public function onUpdateStatus(ReceiptEditStatusRequest $request, $id)
    {
        Log::info("Start: Admin/ReceiptController > onUpdateStatus | admin: " . $request);
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
            $receipt = Receipt::find($id);
            $receipt->update($item);
            $invoice = Invoice::where('id', $receipt?->invoice_id)->first();
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
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Update unsuccess!');
            Log::error("Error: Admin/ReceiptController > onUpdateStatus | message: " . $error->getMessage());
            return response()->json(['message' => 'unsuccess']);
        }
    }

    public function viewReceipt($id)
    {
        Log::info("Start: Admin/ReceiptController > viewReceipt | admin: ");
        try {
            $data['receipt'] = Receipt::find($id);
            $data['invoice'] = $data['receipt']->invoices;
            $data['invoice_detail'] = InvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();
            return view($this->layout . 'view', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/ReceiptController > viewReceipt | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function exportReceiptExcel($id)
    {
        Log::info("Start: Admin/ReceiptController > exportReceiptExcel | admin: ");
        try {
            $data['receipt'] = Receipt::find($id);
            $data['invoice'] = $data['receipt']->invoices;
            $data['invoice_detail'] = InvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();
            return Excel::download(new receiptExport($data), 'receipt.xlsx');
        } catch (Exception $error) {
            Log::error("Error: Admin/ReceiptController > exportReceiptExcel | message: " . $error->getMessage());
        }
    }

    public function onDelete($id)
    {
        try {
            $receipt = Receipt::findOrFail($id);
            $receipt->delete();
            Session::flash('success', 'Receipt deleted successfully!');
        } catch(\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }
}
