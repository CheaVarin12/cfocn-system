<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseOrderRequest;
use App\Models\Document;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Service;
use App\Models\Type;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\Admin\PurchaseRequest;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Customer;
use App\Models\OldCustomerInfo;
use App\Models\DMCCustomer;
use App\Models\DMCPurchase;

class PurchaseOrderController extends Controller
{
    private $document = 'documents';
    private $fileStorePath = null;

    protected $layout = 'admin::pages.po.';
    public function __construct()
    {
        $this->fileStorePath = public_path('documents');
        $this->middleware('permission:purchase-order-view', ['only' => ['index']]);
        $this->middleware('permission:purchase-order-create-pac', ['only' => ['onSavePac']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/PurchaseOrderController > index | admin: " . $req);
        try {
            $data['projects'] = Project::where('status', 1)->get();
            $data['status'] = $req->status;
            if (!$req->status) {
                return redirect()->route('admin-po-service-list', 1);
            }
            if ($req->status != 'trash') {
                $query = PurchaseOrder::where('status', $req->status);
            } else {
                $query = PurchaseOrder::onlyTrashed();
            }
            $data['data'] = $query->with('purchaseOrderDetail', 'purchaseOrderDetail.service', 'file')
                ->when(request('search'), function ($q) {
                    $q->where('po_number', 'like', '%' . request('search') . '%');
                    $q->orWhereHas('customer', function ($q) {
                        $q->where('name_en', 'like', '%' . request('search') . '%');
                    });
                    $q->orWhereHas('type', function ($q) {
                        $q->where('name', 'like', '%' . request('search') . '%');
                    });
                })
                ->when(request('project_id'), function ($q) {
                    $q->where('project_id',request('project_id'));
                })
                ->when(request('po_service_type'), function ($q) {
                    $q->where('type',request('po_service_type'));
                  
                })->orderBy('id', 'desc')->paginate(50);
            $data['rate'] = DB::table('rates')->first();
            $data['serviceTypes'] = Type::where("status", 1)->get();

            return view($this->layout . 'index', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/PurchaseOrderController > index | message: " . $error->getMessage());
        }
    }

    public function onSave(PurchaseOrderRequest $req)
    {
        Log::info("Start: Admin/PurchaseOrderController > onSave | admin: " . $req);
        $dataTable = isset($req->dataTable) && $req->dataTable ? json_decode($req->dataTable) : [];
        $purchasesOrder = $req->all();
        $purchasesOrder['user_id'] = Auth::user()->id;
        $checkError = true;
        foreach ($dataTable as $item) {
            if (
                $item->service_id->value == null ||
                $item->des->value == null ||
                $item->core->value == null ||
                $item->length->value == null ||
                $item->qty->value == null ||
                $item->uom->value == null ||
                $item->price->value == null ||
                $item->amount->value == null ||
                $item->status->value == null
            ) {
                $checkError = false;
            }
        };
        DB::beginTransaction();
        try {
            if ($checkError == true) {
                $status = "Create success.";
                if (!$req->id) {
                    $data = PurchaseOrder::create($purchasesOrder);
                } else {
                    $data = PurchaseOrder::find($req->id);
                    $data->update($purchasesOrder);
                    $status = "Update success.";
                }
                foreach ($dataTable as $item) {
                    $purchases_detail = [
                        'purchase_order_id' => $data->id,
                        'service_id'        => $item->service_id->value,
                        'name'              => Service::find($item->service_id->value)->name,
                        'des'               => $item->des->value,
                        'core'              => $item->core->value,
                        'length'            => $item->length->value,
                        'qty'               => $item->qty->value,
                        'price'             => $item->price->value,
                        'uom'               => $item->uom->value,
                        'amount'            => $item->amount->value,
                        'status'            => $item->status->value,
                    ];
                    if (isset($item->purchase_order_detail_id) && $item?->purchase_order_detail_id) {
                        PurchaseOrderDetail::find($item->purchase_order_detail_id)->update($purchases_detail);
                    } else {
                        PurchaseOrderDetail::create($purchases_detail);
                    }
                }
                $this->deleteItemID($req?->deleteItemID);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success', 'data' => $data]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/PurchaseOrderController > onSave | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }
    public function deleteItemID($itemId)
    {
        if (isset($itemId) && count($itemId) > 0) {
            PurchaseOrderDetail::whereIn('id', $itemId)->delete();
        }
    }
    public function selectTypeToSerive($id)
    {
        Log::info("Start: Admin/PurchaseOrderController > selectTypeToSerive | admin: ");
        try {
            $dataAll = Service::where("type_id", $id)->get();
            $data = $dataAll->where('status', 1);
            return $data;
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseOrderController > selectTypeToSerive | message: " . $error->getMessage());
        }
    }
    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/PurchaseOrderController > onUpdateStatus | admin: ");
        $statusGet = 'Active';
        DB::beginTransaction();
        try {
            $data = PurchaseOrder::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status == '2') {
                $statusGet = 'Inactive';
            }
            if ($data->status == '3') {
                $statusGet = 'Terminate';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $error) {
            DB::rollback();
            $status = false;
            Log::error("Error: Admin/PurchaseOrderController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    //upload
    public function uploadFile(Request $request)
    {
        Log::info("Start: Admin/PurchaseOrderController > uploadFile | admin: " . $request);
        $validate = Validator::make($request->all(), [
            'file' => 'required',
        ], [
            'file.required' => 'files_required'
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        // $this->deleteFile($request->po_id);
        $uploadedFile = $this->upload($request->file('file'), $request->folder_name);
        DB::beginTransaction();
        try {
            DB::table($this->document)->insert([
                'name' => $uploadedFile->name,
                'name_new' => $uploadedFile->name_new,
                'path' => $uploadedFile->path,
                'extension' => $uploadedFile->extension,
                'type' => 'po',
                'po_id' => $request->po_id,
                'date_upload' => date('y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'file_uploaded']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseOrderController > uploadFile | message: " . $error->getMessage());
        }
    }
    private function upload($file, ?string $folder_name)
    {
        Log::info("Start: Admin/PurchaseOrderController > upload | admin: ");
        $uploadedFiles = new Collection();
        if ($file == null)
            return response()->json(['message' => 'files_empty'], 404);
        try {
            $fileInfo = $this->fileInfo($file);
            $fullPath = $folder_name ? $this->fileStorePath . "/$folder_name" : $this->fileStorePath;
            $pathWithFolder = ($folder_name ? "/$folder_name" : '') . "/$fileInfo->file_name.$fileInfo->file_extension";
            $file->move($fullPath, "$fileInfo->file_name.$fileInfo->file_extension");
            $uploadedFiles->push((object) [
                'name' => $fileInfo->original_name,
                'name_new' => $fileInfo->file_name,
                'path' => $pathWithFolder,
                'extension' => $fileInfo->file_extension,
            ]);
            return $uploadedFiles->first();
        } catch (Exception $error) {
            $this->rollback($uploadedFiles->pluck('path'));
            Log::error("Error: Admin/PurchaseOrderController > upload | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    private function rollback($file)
    {
        Log::info("Start: Admin/PurchaseOrderController > rollback | admin: ");
        if ($file == null)
            return response()->json(['message' => '_rollback_empty'], 404);
        try {
            File::delete($file);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseOrderController > rollback | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
    private function fileInfo($file)
    {
        Log::info("Start: Admin/PurchaseOrderController > fileInfo | admin: ");
        try {
            if (!is_file($file))
                return response()->json(['message' => 'not_file'], 404);
            $fileExtension = $file->getClientOriginalExtension();
            return (object) [
                'file_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Str::uuid(),
                'file_extension' => $fileExtension,
                'original_name' => $file->getClientOriginalName(),
            ];
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseOrderController > fileInfo | message: " . $error->getMessage());
        }
    }

    public function deleteFile($poId)
    {
        Log::info("Start: Admin/PurchaseOrderController > deleteFile | admin: ");
        $file = DB::table($this->document)->where('po_id', $poId)->first();
        if ($file == null)
            return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
        try {
            DB::table($this->document)->where('po_id', $poId)->delete();
            File::delete($this->fileStorePath . $file->path);
            DB::commit();
            return response()->json(['message' => 'file_deleted']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseOrderController > deleteFile | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function onSavePac(PurchaseRequest $req)
    {
        Log::info("Start: Admin/PurchaseOrderController > onSave | admin: " . $req);
        $dataTable = isset($req->dataTable) && $req->dataTable ? json_decode($req->dataTable) : [];
        $purchases = $req->all();
        $purchases['user_id'] = Auth::user()->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            $data = Purchase::create($purchases);
            foreach ($dataTable as $item) {
                $purchases_detail = [
                    'purchase_id' => $data->id,
                    'service_id' => $item->service_id->value,
                    'name' => Service::find($item->service_id->value)->name,
                    'des' => $item->des->value,
                    'qty' => $item->qty->value,
                    'price' => $item->price->value,
                    'uom' => $item->uom->value,
                    'amount' => $item->amount->value,
                ];
                if (isset($item->purchase_detail_id) && $item?->purchase_detail_id) {
                    PurchaseDetail::find($item->purchase_detail_id)->update($purchases_detail);
                } else {
                    PurchaseDetail::create($purchases_detail);
                }
            }
            $this->deleteItemID($req?->deleteItemID);

            // Store item to old customer info
            $customer = Customer::find($req->customer_id);
            $serviceType = Type::find($req->type_id);
            $descriptions = collect($dataTable)->pluck('des.value')->toArray();
            OldCustomerInfo::updateOrCreate(
                [
                    'customer_code' => $customer->customer_code,
                    'pac_number' => $data->pac_number,
                ],
                [
                    'register_date' => $customer->register_date,
                    'customer_name' => $customer->name_en,
                    'customer_address' => $customer->address_en ?? null,
                    'status' => $customer->status,
                    'inactive_date' => $customer->in_active_date ?? null,
                    'po_number' => $data->po_number,
                    'pac_number' => $data->pac_number,
                    'service_type' => $serviceType->name,
                    'description' => count($descriptions) > 0 ? implode(', ', $descriptions) : null,
                    'type' => $data->pac_type ?? null,
                    'qty_cores' => $data->cores ?? null,
                    'length' => $data->length ?? null,
                    'user_id' => Auth::id(),
                ]
            );

            //Customer & PAC DMC
            $existingCustomer = DMCCustomer::where('customer_code', $customer->customer_code)->first();
            if (!$existingCustomer) {
                $dmcCustomer = DMCCustomer::create([
                    'customer_code' => $customer->customer_code,
                    'customer_name' => $customer->name_en,
                    'register_date' => $customer->register_date,
                    'customer_address' => $customer->address_en ?? null,
                    'status' => $customer->status,
                    'inactive_date' => $customer->in_active_date ?? null,
                ]);
            }
            DMCPurchase::create([
                'dmc_customer_id' => $existingCustomer ? $existingCustomer->id : $dmcCustomer?->id,
                'project_id' => $data->project_id,
                'po_number' => $data->po_number,
                'pac_number' => $data->pac_number,
                'service_type' => $serviceType->name ?? null,
                'description' => count($descriptions) > 0 ? implode(', ', $descriptions) : null,
                'type' => $data->pac_type ?? null,
                'qty_cores' => $data->cores ?? null,
                'length' => $data->length ?? null,
                'pac_date' => Carbon::now()->format('Y-m-d'),
                'register_date' => $data->issue_date ?? null,
                'customer_code' => $customer->customer_code,
                'customer_name' => $customer->name_en,
                'customer_address' => $customer->address_en ?? null,
                'status' => $customer->status,
                'inactive_date' => $customer->in_active_date ?? null,
                ''
            ]);


            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success', 'data' => $data]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/PurchaseOrderController > onSave | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }

    public function onDeleteFile(Request $request)
    {
        $id = $request->id;
        Log::info("Start: Admin/PurchaseOrderController > onDeleteFile | admin: ");
        $file = DB::table($this->document)->where('id',  $id)->first();
        if ($file == null)
            return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
        try {
            DB::table($this->document)->where('id', $id)->delete();
            File::delete($this->fileStorePath . $file->path);
            DB::commit();
            return response()->json(['message' => 'file_deleted']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseOrderController > onDeleteFile | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function onDeletePo($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $purchaseOrder->purchaseOrderDetail()->delete();
            $purchaseOrder->delete();

            Session::flash('success', 'Po deleted successfully!');
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function onGetFile($poId)
    {
        $data = Document::where('po_id', $poId)->orderBy('id', 'asc')->get();
        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }
}
