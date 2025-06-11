<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequest;
use App\Http\Requests\Admin\WorkOrderInvoiceRequest;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\FTTHService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Project;
use App\Models\Service;
use App\Models\Type;
use App\Models\WorkOrderInvoice;
use App\Models\WorkOrderInvoiceDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $layout = 'admin::pages.work-order.order.';
    public function __construct()
    {
        $this->middleware('permission:work-order-order', ['only' => ['index']]);
        $this->middleware('permission:work-order-order', ['only' => ['onSave', 'onCreate']]);
        $this->middleware('permission:work-order-order', ['only' => ['onEdit', 'onUpdateStatus', 'onSave']]);
        $this->middleware('permission:work-order-order', ['only' => ['createInvoice']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/OrderController > index | admin: " . $req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-order-list', 1);
            }

            $data['projects'] = Project::where('status', 1)->get();

            $query = Order::with('customer')
                        ->where('status', $req->status);

            $data['data'] = $query
            ->where(function ($q) use ($req, $search) {
                if ($search) {
                    $q->whereHas('customer', function ($q) {
                        $q->where('name_en', 'like', '%' . request('search') . '%');
                    });
                    $q->orWhereHas('type', function ($q) {
                        $q->where('name', 'like', '%' . request('search') . '%');
                    });
                }
                if ($req->search_project) {
                    $q->where('project_id', $req->search_project);
                }
            })
            ->orderBy('id', 'desc')->paginate(25);

            return view($this->layout . 'index', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/OrderController > index | message: " . $error->getMessage());
        }
    }

    public function onCreate()
    {
        Log::info("Start: Admin/OrderController > onCreate | admin: ");
        try {
            $data['rate'] = DB::table('rates')->first();
            $data['types'] = Type::where("status", 1)->get();
            $data['ftthSevices'] = FTTHService::where("status", 1)->orderByDesc('id')->get();
            return view($this->layout . 'create', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/OrderController > onCreate | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onSave(OrderRequest $req)
    {
        Log::info("Start: Admin/OrderController > onSave | admin: " . $req);
        $dataTable = isset($req->dataTable) && $req->dataTable ? json_decode($req->dataTable) : [];
        $order = $req->all();
        $order['status'] = 1;
        $order['user_id'] = Auth::user()->id;
        $order['type_id'] = 8; //FTTH
        DB::beginTransaction();

        try {
            $status = "Create success.";
            if (!$req->id) {
                $data = Order::create($order);
            } else {
                $data = Order::find($req->id);
                $data->update($order);
                $status = "Update success.";
            }

            foreach ($dataTable as $item) {
                $order_detail = [
                    'order_id'  => $data->id,
                    'service_id' => $item->service_id->value,
                    'name'      => Service::find($item->service_id->value)->name,
                    'des'       => $item->des->value,
                    'qty'       => $item->qty->value,
                    'uom'       => $item->uom->value,
                    'price'     => $item->price->value,
                    'amount'    => $item->amount->value,
                ];
                if (isset($item->order_detail_id) && $item?->order_detail_id) {
                    OrderDetail::find($item->order_detail_id)->update($order_detail);
                } else {
                    OrderDetail::create($order_detail);
                }
            }
            $this->deleteItemID($req?->deleteItemID);
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success', 'data' => $data]);
        } catch (\Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/OrderController > onSave | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }

    public function deleteItemID($itemId)
    {
        if (isset($itemId) && count($itemId) > 0) {
            OrderDetail::whereIn('id', $itemId)->delete();
        }
    }

    public function selectTypeToSerive($id)
    {
        Log::info("Start: Admin/OrderController > selectTypeToSerive | admin: ");
        try {
            $dataAll = Service::where("type_id", $id)->get();
            $data = $dataAll->where('status', 1);
            return $data;
        } catch (\Exception $error) {
            Log::error("Error: Admin/OrderController > selectTypeToSerive | message: " . $error->getMessage());
        }
    }

    public function onEdit($id)
    {
        Log::info("Start: Admin/OrderController > onEdit | admin: ");
        try {
            $data["data"] = Order::with([
                "customer" => function ($query) {
                    $query->select('id', 'name_en', 'name_kh');
                },
                "project" => function ($query) {
                    $query->select('id', 'name');
                },
                "orderDetail"
            ])->find($id);

            $data['rate'] = DB::table('rates')->first();
            $data['types'] = Type::where("status", 1)->get();
            $data['ftthSevices'] = FTTHService::where("status", 1)->orderByDesc('id')->get();
            if ($data['data']) {
                return view($this->layout . 'edit', $data);
            }
            return redirect()->route('admin-order-list', 1);
        } catch (\Exception $error) {
            Log::error("Error: Admin/OrderController > onEdit | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function createInvoice($id)
    {
        Log::info("Start: Admin/OrderController > createInvoice | admin: ");
        try {
            $data['rate'] = DB::table('rates')->first();
            $data['order'] = Order::with([
                'orderDetail' => function ($q) {
                    $q->with('service');
                },
                'project',
                'customer',
                'invoice'
            ])->find($id);
            $data['contact'] = Contact::first();
            if ($data['order']['type_id'] == 2) {
                $data['invoice'] = WorkOrderInvoice::where('order_id', $data['order']['id'])->first();
                $data['count_invoice'] = WorkOrderInvoice::where('order_id', $data['order']['id'])->count();
            }
            return response()->json($data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/OrderController > createInvoice | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
    public function onSaveInvoice(WorkOrderInvoiceRequest $req)
    {
        Log::info("Start: Admin/InvoiceController > onSave | admin: ");
        $orderDetails = isset($req->order_details) && $req->order_details ? json_decode($req->order_details) : [];
        $invoices = $req->all();
        $invoices['user_id'] = Auth::user()->id;
        $invoices['data_customer'] = $this->dataCustomerEncode($req->customer_id);
        $dateValid = checkValidate($req->issue_date);
        DB::beginTransaction();
        try {
            if ($dateValid) {
                $status = "Create success.";
                $data = WorkOrderInvoice::create($invoices);
                foreach ($orderDetails as $item) {
                    $detail = [
                        'invoice_id' => $data->id,
                        'service_id' => $item->service_id,
                        'des' => $item->des,
                        'qty' => $item->qty,
                        'price' => $item->price,
                        'uom' => $item->uom,
                        'rate_first' => $item?->rate_first ?? null,
                        'rate_second' => $item?->rate_second ?? null,
                        'amount' => $item->amount,
                    ];
                    WorkOrderInvoiceDetail::create($detail);
                }
                DB::commit();
                return response()->json(['message' => 'success', 'data' => null]);
            }
            return response()->json(['message' => 'dateValid']);
        } catch (\Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/InvoiceController > onSave | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
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
        $data->order_type = $data->order->type_id == 2 ? true : false;
        $rateData = DB::table('rates')->first();

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
        return $data;
    }

    public function dataCustomerEncode($customerId)
    {
        $data = Customer::find($customerId);
        return json_encode($data);
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/OrderController > onUpdateStatus | admin: ");
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = Order::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status !== '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (\Exception $error) {
            DB::rollback();
            $status = false;
            Log::error("Error: Admin/OrderController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
}
