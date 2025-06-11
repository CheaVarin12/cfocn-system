<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportPACDetailRequest;
use App\Http\Requests\Admin\ImportPACRequest;
use App\Http\Requests\Admin\InvoiceRequest;
use App\Http\Requests\Admin\PurchaseRequest;
use App\Imports\PACDetailImport;
use App\Imports\PACImport;
use App\Models\ChildInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Exception;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Type;
use App\Models\Project;
use App\Models\Contact;
use App\Models\DMCCustomer;
use App\Models\DMCPurchase;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\OldCustomerInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class PurchaseController extends Controller
{
    private $document = 'documents';
    private $folder = 'document_folders';
    private $fileStorePath = null;
    private $perPage = 20;

    protected $layout = 'admin::pages.purchase.';
    public function __construct()
    {
        $this->fileStorePath = public_path('documents');
        $this->middleware('permission:purchase-view', ['only' => ['index']]);
        $this->middleware('permission:purchase-create', ['only' => ['onSave', 'onCreate']]);
        $this->middleware('permission:purchase-update', ['only' => ['onEdit', 'onUpdateStatus', 'onSave']]);
        $this->middleware('permission:purchase-upload-file', ['only' => ['documentIndex']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/PurchaseController > index | admin: " . $req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-purchase-list', 1);
            }

            $query = Purchase::with('customer');
            if ($req->status == "old") {
                $query = Purchase::where('type_data', $req->status);
            } else if ($req->status != "all" && $req->status != "old") {
                $query = Purchase::where('status', $req->status);
            }
            $data['projects'] = Project::where('status', 1)->get();
            $data['data'] = $query->with('customer')->where(function ($q) use ($req, $search) {
                if ($search) {
                    $q->where('po_number', 'like', '%' . $search . '%');
                    $q->orWhereHas('customer', function ($q) {
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
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > index | message: " . $error->getMessage());
        }
    }

    public function onCreate()
    {
        Log::info("Start: Admin/PurchaseController > onCreate | admin: ");
        try {
            $data['rate'] = DB::table('rates')->first();
            $data['types'] = Type::where("status", 1)->get();
            return view($this->layout . 'create', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > onCreate | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onSave(PurchaseRequest $req)
    {
        Log::info("Start: Admin/PurchaseController > onSave | admin: " . $req);
        $dataTable = isset($req->dataTable) && $req->dataTable ? json_decode($req->dataTable) : [];
        $purchases = $req->all();
        $purchases['status'] = 1;
        $purchases['user_id'] = Auth::user()->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if (!$req->id) {
                $data = Purchase::create($purchases);
            } else {
                $data = Purchase::find($req->id);
                $data->update($purchases);
                $status = "Update success.";
            }
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
                    'location' => $data->location ?? null
                ]
            );

            //Customer & PAC DMC
            if (!$req->id) {
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
                    'pac_date' => $data->issue_date ?? null,
                    'register_date' => null,
                    'customer_code' => $customer->customer_code,
                    'customer_name' => $customer->name_en,
                    'customer_address' => $customer->address_en ?? null,
                    'status' => $customer->status,
                    'inactive_date' => $customer->in_active_date ?? null,
                    'location' => $data->location ?? null,
                    'po_date' => $data->po_date ?? null
                ]);
            }

            DB::commit();
            Session::flash('success', $status);
            return response()->json(['message' => 'success', 'data' => $data]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/PurchaseController > onSave | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }

    public function deleteItemID($itemId)
    {
        if (isset($itemId) && count($itemId) > 0) {
            PurchaseDetail::whereIn('id', $itemId)->delete();
        }
    }

    public function selectTypeToSerive($id)
    {
        Log::info("Start: Admin/PurchaseController > selectTypeToSerive | admin: ");
        try {
            $dataAll = Service::where("type_id", $id)->get();
            $data = $dataAll->where('status', 1);
            return $data;
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > selectTypeToSerive | message: " . $error->getMessage());
        }
    }


    public function onEdit($id)
    {
        Log::info("Start: Admin/PurchaseController > onEdit | admin: ");
        try {
            $data["data"] = Purchase::with([
                "customer" => function ($query) {
                    $query->select('id', 'name_en', 'name_kh');
                },
                "project" => function ($query) {
                    $query->select('id', 'name');
                },
                "purchaseDetail"
            ])->find($id);
            $data['rate'] = DB::table('rates')->first();
            $data['types'] = Type::where("status", 1)->get();

            if ($data['data']) {
                return view($this->layout . 'edit', $data);
            }
            return redirect()->route('admin-purchase-list', 1);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > onEdit | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function createInvoice($id)
    {
        Log::info("Start: Admin/PurchaseController > createInvoice | admin: ");
        try {
            $data['rate'] = DB::table('rates')->first();
            $data['purchase'] = Purchase::with([
                'purchaseDetail' => function ($q) {
                    $q->with('service');
                },
                'project',
                'customer',
                'invoice'
            ])->find($id);
            $data['contact'] = Contact::first();
            if ($data['purchase']['type_id'] == 2) {
                $data['invoice'] = Invoice::where('po_id', $data['purchase']['id'])->first();
                $data['count_invoice'] = Invoice::where('po_id', $data['purchase']['id'])->count();
            }
            return response()->json($data);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > createInvoice | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
    public function onSaveInvoice(InvoiceRequest $req)
    {
        Log::info("Start: Admin/InvoiceController > onSave | admin: ");
        $purchaseDetails = isset($req->purchase_details) && $req->purchase_details ? json_decode($req->purchase_details) : [];
        $childInvoice = isset($req->child_invoice) && $req->child_invoice ? json_decode($req->child_invoice) : [];
        $invoices = $req->all();
        $invoices['user_id'] = Auth::user()->id;
        $invoices['data_customer'] = $this->dataCustomerEncode($req->customer_id);
        if($req->check_multiple_pac != null){
            $new_multiple_po_id = array_map(function ($item) {
                return $item['_id'];
            }, $req->multiple_po_id);
           $invoices['multiple_po_id'] = json_encode($new_multiple_po_id);  

           $new_po_number = array_merge(...array_map(function ($item) {
            return explode(',', $item);
        }, $req->po_number));
          $invoices['po_number']= implode(',', $new_po_number);
        }
        $dateValid = checkValidate($req->issue_date);
        DB::beginTransaction();
        try {
            if ($dateValid) {
                $status = "Create success.";
                $data = Invoice::create($invoices);
                foreach ($purchaseDetails as $item) {
                    $detail = [
                        'purchase_id'=> $item->purchase_id,
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
                    InvoiceDetail::create($detail);
                }
                foreach ($childInvoice as $item) {
                    $childInvoices = [
                        'invoice_id' => $data->id,
                        'purchase_id' => $item->purchase_id,
                        'total_qty' => $item->total_qty,
                        'vat' => $item->vat,
                        'sub_total' => $item->sub_total,
                        'grand_total' => $item->grand_total,
                        'issue_date' => $data->issue_date,
                    ];
                    ChildInvoice::create($childInvoices);
                }
                DB::commit();
                return response()->json(['message' => 'success', 'data' => null]);
            }
            return response()->json(['message' => 'dateValid']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/InvoiceController > onSave | message: " . $error->getMessage());
            return response()->json(['message' => 'error', 'error' => $error->getMessage()]);
        }
    }

    public function queryViewDetailInvoice($id)
    {
        $data = Invoice::with([
            'purchase' => function ($q) {
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
        $data->purchase_type = $data->purchase->type_id == 2 ? true : false;
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
    public function exportExcel($id)
    {
        Log::info("Start: Admin/PurchaseController > exportExcel | admin: ");
        try {
            $data['sub_total_dollar'] = null;
            $data['sub_total_khmer'] = null;
            $data['vat_dollar'] = null;
            $data['vat_khmer'] = null;
            $data['grand_total_dollar'] = null;
            $data['grand_total_khmer'] = null;

            $data['purchase'] = Purchase::find($id);
            $data['purchase_detail'] = PurchaseDetail::with('service')->where("purchase_id", $id)->get();
            foreach ($data['purchase_detail'] as $item) {
                $data['sub_total_dollar'] += $item->price * $item->qty;
                $data['sub_total_khmer'] += ($item->price * $item->qty * $item->rate);
                $data['vat_dollar'] += ($item->amount * 0.1);
                $data['vat_khmer'] += ($item->amount * 0.1) * $item->rate;
            }
            $data['grand_total_khmer'] = $data['sub_total_khmer'] + $data['vat_khmer'];
            $data['grand_total_dollar'] = $data['sub_total_dollar'] + $data['vat_dollar'];
            $data['customer'] = Customer::where("id", $data['purchase']->customer_id)->first();
            $data['contact'] = Contact::first();
            $data['rate'] = DB::table('rates')->first();
            return Excel::download(new PurchaseExport($data), 'purchase_detail.xlsx');
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > exportExcel | message: " . $error->getMessage());
        }
    }
    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/PurchaseController > onUpdateStatus | admin: ");
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = Purchase::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status !== '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $error) {
            DB::rollback();
            $status = false;
            Log::error("Error: Admin/PurchaseController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    //document
    public function documentIndex(Request $req, $id)
    {
        Log::info("Start: Admin/PurchaseController > documentIndex | admin: ");
        try {
            $data['purchase'] = Purchase::find($id);
            return view($this->layout . 'document.index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > index | message: " . $error->getMessage());
        }
    }

    public function first(Request $request, $id)
    {
        Log::info("Start: Admin/PurchaseController > first | admin: ");
        try {
            $data['folders'] = DB::table($this->folder)->where('pac_id', $id)
                ->when(request('q'), function ($query) {
                    $query->where('name', 'like', '%' . request('q') . '%');
                })
                ->when(!request('only_trash'), function ($query) {
                    $query
                        ->when(request('folder_id'), function ($query) {
                            $query->whereParentId(request('folder_id'));
                        })
                        ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('parent_id');
                        })->whereNull('deleted_at');
                })
                ->when(request('only_trash'), function ($query) {
                    $query->whereNotNull('deleted_at');
                })
                ->orderByDesc('created_at')
                ->get();

            $data['files'] = DB::table($this->document)->where('pac_id', $id)
                ->when(request('q'), function ($query) {
                    $query->where('name', 'like', '%' . request('q') . '%');
                })
                ->when(!request('only_trash'), function ($query) {
                    $query
                        ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                        ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
                })
                ->when(request('only_trash'), function ($query) {
                    $query->whereNotNull('deleted_at');
                })
                ->orderByDesc('created_at')
                ->paginate($this->perPage);

            $data['base_path'] = asset('documents');
            return response()->json($data);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > first | message: " . $error->getMessage());
        }
    }


    public function getFiles(Request $request, $id)
    {

        Log::info("Start: Admin/PurchaseController > getFiles | admin: " . $request);
        try {
            $data['files'] = DB::table($this->document)->where('pac_id', $id)
                ->when(request('q'), function ($query) {
                    $query->where('name', 'like', '%' . request('q') . '%');
                })
                ->when(!request('only_trash'), function ($query) {
                    $query
                        ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                        ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
                })
                ->when(request('only_trash'), function ($query) {
                    $query->whereNotNull('deleted_at');
                })
                ->orderByDesc('created_at')
                ->paginate($this->perPage);

            return response()->json($data);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > getFiles | message: " . $error->getMessage());
        }
    }

    public function getFolders(Request $request, $id)
    {
        Log::info("Start: Admin/PurchaseController > getFolders | admin: " . $request);
        try {
            $folders = DB::table($this->folder);
            if ($request->has('parent_id')) {
                $folders = $folders->where('parent_id', $request->parent_id)->where('pac_id', $id);
            }
            return response()->json($folders->paginate($request->per_page ?? 10));
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > getFolders | message: " . $error->getMessage());
        }
    }

    public function uploadFile(Request $request)
    {
        Log::info("Start: Admin/PurchaseController > uploadFile | admin: " . $request);
        $validate = Validator::make($request->all(), [
            'file' => 'required',
        ], [
            'file.required' => 'files_required'
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        $uploadedFile = $this->upload($request->file('file'), $request->folder_name);
        DB::beginTransaction();
        try {
            DB::table($this->document)->insert([
                'folder_id' => $request->folder_id ?? null,
                'name' => $uploadedFile->name,
                'name_new' => $uploadedFile->name_new,
                'path' => $uploadedFile->path,
                'extension' => $uploadedFile->extension,
                'type' => 'pac',
                'pac_id' => $request->pac_id,
                'date_upload' => date('y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'file_uploaded']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseController > uploadFile | message: " . $error->getMessage());
        }
    }

    private function upload($file, ?string $folder_name)
    {
        Log::info("Start: Admin/PurchaseController > upload | admin: ");
        $uploadedFiles = new Collection();
        if ($file == null)
            return response()->json(['message' => 'files_empty'], 404);
        try {
            $fileInfo = $this->fileInfo($file);
            $fullPath = $folder_name ? $this->fileStorePath . "/$folder_name" : $this->fileStorePath;
            $pathWithFolder = ($folder_name ? "/$folder_name" : '') . "/$fileInfo->file_name.$fileInfo->file_extension";
            $file->move($fullPath, "$fileInfo->file_name.$fileInfo->file_extension");
            $uploadedFiles->push((object) [
                // 'folder_id' => $folder_id ?? null,
                'name' => $fileInfo->original_name,
                'name_new' => $fileInfo->file_name,
                'path' => $pathWithFolder,
                'extension' => $fileInfo->file_extension,
            ]);
            return $uploadedFiles->first();
        } catch (Exception $error) {
            $this->rollback($uploadedFiles->pluck('path'));
            Log::error("Error: Admin/PurchaseController > upload | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    private function rollback($file)
    {
        Log::info("Start: Admin/PurchaseController > rollback | admin: ");
        if ($file == null)
            return response()->json(['message' => '_rollback_empty'], 404);
        try {
            File::delete($file);
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > rollback | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    private function fileInfo($file)
    {
        Log::info("Start: Admin/PurchaseController > fileInfo | admin: ");
        try {
            if (!is_file($file))
                return response()->json(['message' => 'not_file'], 404);
            $fileExtension = $file->getClientOriginalExtension();
            return (object) [
                'file_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Str::uuid(),
                'file_extension' => $fileExtension,
                'original_name' => $file->getClientOriginalName(),
                // 'is_document' => in_array($fileExtension, ['doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx', 'ppt', 'pptx']),
            ];
        } catch (Exception $error) {
            Log::error("Error: Admin/PurchaseController > fileInfo | message: " . $error->getMessage());
        }
    }

    public function deleteFile(Request $request)
    {
        Log::info("Start: Admin/PurchaseController > deleteFile | admin: " . $request);
        $file = DB::table($this->document)->find($request->file_id);
        if ($file == null)
            return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
        try {
            if ($request->to_trash && $request->to_trash == 'true') {
                DB::table($this->document)->whereId($request->file_id)->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table($this->document)->whereId($request->file_id)->delete();
                File::delete($this->fileStorePath . $file->path);
            }
            DB::commit();
            return response()->json(['message' => 'file_deleted']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseController > deleteFile | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function createFolder(Request $request)
    {
        Log::info("Start: Admin/PurchaseController > createFolder | admin: " . $request);
        $validate = Validator::make($request->all(), [
            'name' => [
                'bail',
                'required',
                Rule::unique('document_folders')
                    ->where(function ($query) {
                        $query
                            ->when(request('parent_id'), function ($query) {
                                $query->where('parent_id', request('parent_id'));
                            })
                            ->when(!request('parent_id'), function ($query) {
                                $query->whereNull('parent_id');
                            });
                    })
                    ->where('name', request('name'))->where('pac_id', request('pac_id'))
            ],
        ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        DB::beginTransaction();
        try {
            DB::table($this->folder)->insert([
                'parent_id' => $request->parent_id ?? null,
                'name' => $request->name,
                'is_hidden' => 0,
                'type' => 'pac',
                'pac_id' => $request->pac_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'folder_created']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseController > createFolder | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function renameFolder(Request $request)
    {
        Log::info("Start: Admin/PurchaseController > renameFolder | admin: " . $request);
        $validate = Validator::make($request->all(), [
            'name' => [
                'bail',
                'required',
                Rule::unique('document_folders')
                    ->ignore(request('folder_id'), 'id')
                    ->where(function ($query) {
                        $query
                            ->when(request('parent_id'), function ($query) {
                                $query->where('parent_id', request('parent_id'));
                            })
                            ->when(!request('parent_id'), function ($query) {
                                $query->whereNull('parent_id');
                            });
                    })
                    ->where('name', request('name'))->where('pac_id', request('pac_id'))
            ],
        ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        DB::beginTransaction();
        try {
            DB::table($this->folder)->where('id', request('folder_id'))->update([
                'name' => $request->name,
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'folder_updated']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseController > renameFolder | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function deleteFolder(Request $request)
    {
        Log::info("Start: Admin/PurchaseController > deleteFolder | admin: " . $request);
        $file = DB::table($this->folder)->find($request->folder_id);
        if ($file == null)
            return response()->json(['message' => 'folder_not_found'], 404);
        // if ($file->user_id != auth()->id()) return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
        try {
            if ($request->to_trash && $request->to_trash == 'true') {
                DB::table($this->folder)->whereId($request->folder_id)->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table($this->folder)->whereId($request->folder_id)->delete();
            }
            DB::commit();
            return response()->json(['message' => 'folder_deleted']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PurchaseController > deleteFolder | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function deleteAll(Request $request)
    {
        Log::info("Start: Admin/PurchaseController > deleteAll | admin: " . $request);
        if (!isset($request->all) && !isset($request->data))
            return response()->json(['message' => 'data_not_found'], 404);
        DB::beginTransaction();
        try {
            if (isset($request->all)) {
                $files = DB::table($this->document)->whereNotNull('deleted_at')->get();
                DB::table($this->document)->whereNotNull('deleted_at')->delete();
                DB::table($this->folder)->whereNotNull('deleted_at')->delete();
                foreach ($files as $file) {
                    File::delete($this->fileStorePath . $file->path);
                }
            } else {
                foreach ($request->data as $item) {
                    if (property_exists((object) $item, 'folder_id')) {
                        DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                        File::delete($this->fileStorePath . $item['path']);
                    } else {
                        DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'all_deleted']);
        } catch (Exception $error) {
            DB::rollBack();
            Log::error("Error: Admin/PurchaseController > deleteAll | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function restoreAll(Request $request)
    {
        Log::info("Start: Admin/PurchaseController > restoreAll | admin: " . $request);
        if (!isset($request->all) && !isset($request->data))
            return response()->json(['message' => 'data_not_found'], 404);
        DB::beginTransaction();
        try {
            $restoreData = [
                'deleted_at' => null,
                'updated_at' => now(),
            ];
            if (isset($request->all)) {
                DB::table($this->document)->whereNotNull('deleted_at')->update($restoreData);
                DB::table($this->folder)->whereNotNull('deleted_at')->update($restoreData);
            } else {
                foreach ($request->data as $item) {
                    if (property_exists((object) $item, 'folder_id')) {
                        DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                    } else {
                        DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'all_restored']);
        } catch (Exception $error) {
            DB::rollBack();
            Log::error("Error: Admin/PurchaseController > restoreAll | message: " . $error->getMessage());
            return response()->json(['message' => $error->getLine()], 500);
        }
    }

    public function importExcel(ImportPACRequest $request)
    {
        try {
            $PACImport = new PACImport();
            Excel::import($PACImport, $request->file('pac_file'));
            if ($PACImport->message == 'invalid_column') {
                return response()->json([
                    'status' => 'danger',
                    'message' => 'File invalid column name!',
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'File imported success!',
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'danger',
                'message' => 'Import Failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function importExcelDetail(ImportPACDetailRequest $request)
    {
        try {
            $PACDetailImport = new PACDetailImport();
            Excel::import($PACDetailImport, $request->file('pac_detail_file'));
            if ($PACDetailImport->message == 'invalid_column') {
                return response()->json([
                    'status' => 'danger',
                    'message' => 'File invalid column name!',
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'File imported success!',
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'danger',
                'message' => 'Import Failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
