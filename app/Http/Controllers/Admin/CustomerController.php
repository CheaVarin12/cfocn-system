<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Http\Requests\Admin\ImportCustomerRequest;
use App\Imports\CustomerImport;
use App\Models\CreditNote;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;
use App\Models\CustomerHistory;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Receipt;
use App\Models\WorkOrderInvoice;
use App\Models\WorkOrderReceipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class CustomerController extends Controller
{
    protected $layout = 'admin::pages.customer.';
    private $document = null;
    private $folder = null;
    private $fileStorePath = null;
    private $perPage = null;

    public function __construct()
    {
        $this->document = 'documents';
        $this->folder = 'document_folders';
        $this->fileStorePath = public_path('documents');
        $this->perPage = 20;

        $this->middleware('permission:customer-view', ['only' => ['index']]);
        $this->middleware('permission:customer-create', ['only' => ['onCreate', 'onSave']]);
        $this->middleware('permission:customer-update', ['only' => ['onEdit', 'onUpdateStatus', 'onSave']]);
        $this->middleware('permission:customer-excel-export', ['only' => ['exportCustomerExcel']]);
        $this->middleware('permission:customer-upload-file', ['only' => ['documentIndex']]);
    }
    public function index(Request $req)
    {
        Log::info("Start: Admin/CustomerController > index | admin: ");
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-customer-list', 1);
            }
            $customer = new Customer();
            if ($req->status != 'trash') {
                $customers = $customer->where('status', $req->status);
            } else {
                $customers = $customer->onlyTrashed();
            }
            $data['data'] = $customers->where(function ($q) use ($search, $req) {
                    if ($search) {
                        $q->where('name_en', 'like', '%' . $search . '%');
                        $q->orWhere('name_kh', 'like', '%' . $search . '%');
                        $q->orWhere('customer_code', 'like', '%' . $search . '%');
                        $q->orWhere('vat_tin', 'like', '%' . $search . '%');
                    }
                })
                ->orderBy('id', 'desc')->paginate(25);
            return view($this->layout . 'index', $data);
        } catch (Exception $e) {
            Log::error("Error: Admin/CustomerController > index | message: " . $e->getMessage());
        }
    }
    public function onCreate()
    {
        Log::info("Start: Admin/CustomerController > onCreate | admin: ");
        try {
            return view($this->layout . 'create');
        } catch (Exception $error) {
            Log::error("Error: Admin/CustomerController > onCreate | message: " . $error->getMessage());
        }
    }
    public function onSave(CustomerRequest $request, $id = null)
    {
        Log::info("Start: Admin/CustomerController > onSave | admin: " . $request);
        $items = [
            'name_en'       => $request->name_en,
            'name_kh'       => $request->name_kh,
            'vat_tin'       => $request->vat_tin,
            'phone'         => $request->phone,
            'address_en'    => $request->address_en,
            'address_kh'    => $request->address_kh,
            'email'         => $request->email,
            'gender'        => $request->gender,
            'status'        => $request->status,
            'type'          => $request->type,
            'customer_code' => $request->customer_code,
            'register_date' => $request->register_date,
            'in_active_date'=> $request->in_active_date,
            'user_id'       => Auth::user()->id,
            'attention'     => $request->attention,
        ];
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = Customer::find($id);
                $status = "Update success.";
                if ($request->type_status != "update") {
                    $this->customerHistory($data);
                }
                $data->update($items);
            } else {
                Customer::create($items);
            }

            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-customer-list', 1);
        } catch (Exception $e) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/CustomerController > onSave | message: " . $e->getMessage());
            return redirect()->back();
        }
    }
    public function onEdit($id)
    {
        Log::info("Start: Admin/CustomerController > onEdit | admin: ");
        try {
            $data["data"] = Customer::find($id);
            if ($data['data']) {
                return view($this->layout . 'edit', $data);
            }
            return redirect()->route('admin-customer-list');
        } catch (Exception $e) {
            Log::error("Error: Admin/CustomerController > onEdit | message: " . $e->getMessage());
        }
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/CustomerController > onUpdateStatus | admin:" . $req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $currentDate = Carbon::now()->format('Y-m-d');
            $data = Customer::find($req->id);
            $data->update([
                'in_active_date' => $currentDate,
                'status' => $req->status,
                'user_id'   => Auth::user()->id
            ]);
            if ($data->status !== '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $e) {
            $status = false;
            DB::rollback();
            Log::error("Error: Admin/CustomerController > onUpdateStatus | message: " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function getCustomerHistory($id)
    {
        $data = CustomerHistory::where(function ($q) use ($id) {
            $q->with(["customer"]);
            $q->where('customer_id', $id);
            $q->orderBy('id', 'desc');
        })->paginate(23);

        if (isset($data) && count($data) > 0) {
            foreach ($data as $item) {
                $item->data_customer = $item->data_customer ? (object) json_decode($item->data_customer) : null;
            }
        }
        return response()->json($data);
    }

    public function customerHistory($data)
    {
        $item = [
            "customer_id" => $data->id,
            "data_customer" => json_encode($data),
            "status"    => 1,
            "is_active" => 2,
            "user_id" => Auth::user()->id
        ];
        CustomerHistory::create($item);
    }

    public function exportCustomerExcel(Request $request)
    {
        Log::info("Start: Admin/CustomerController > exportCustomerExcel | admin: ");
        try {
            if ($request->status == 1) {
                $data['customers'] = Customer::where("status", 1)->get();
            } else if ($request->status == 2) {
                $data['customers'] = Customer::where("status", 2)->get();
            } else {
                $data['customers'] = Customer::all();
            }
            return Excel::download(new CustomerExport($data), 'Customer-list.xlsx');
        } catch (Exception $e) {
            Log::error("Error: Admin/CustomerController > exportCustomerExcel | message: " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function documentIndex(Request $req, $id)
    {
        Log::info("Start: Admin/CustomerController > documentIndex | admin: ");
        try {
            $data['customer'] = Customer::find($id);
            return view($this->layout . 'document.index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/CustomerController > index | message: " . $error->getMessage());
        }
    }

    public function first(Request $request, $id)
    {
        Log::info("Start: Admin/CustomerController > first | admin: ");
        try {
            $data['folders'] = DB::table($this->folder)->where('customer_id', $id)
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

            $data['files'] = DB::table($this->document)->where('customer_id', $id)
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
            Log::error("Error: Admin/CustomerController > first | message: " . $error->getMessage());
        }
    }


    public function getFiles(Request $request, $id)
    {
        Log::info("Start: Admin/CustomerController > getFiles | admin: " . $request);
        try {
            $data['files'] = DB::table($this->document)->where('customer_id', $id)
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
            Log::error("Error: Admin/CustomerController > getFiles | message: " . $error->getMessage());
        }
    }

    public function getFolders(Request $request, $id)
    {
        Log::info("Start: Admin/CustomerController > getFolders | admin: " . $request);
        try {
            $folders = DB::table($this->folder);
            if ($request->has('parent_id')) {
                $folders = $folders->where('parent_id', $request->parent_id)->where('customer_id', $id);
            }
            return response()->json($folders->paginate($request->per_page ?? 10));
        } catch (Exception $error) {
            Log::error("Error: Admin/CustomerController > getFolders | message: " . $error->getMessage());
        }
    }

    public function uploadFile(Request $request)
    {
        Log::info("Start: Admin/CustomerController > uploadFile | admin: " . $request);
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
                'type' => 'customer',
                'customer_id' => $request->customer_id,
                'date_upload' => date('y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'file_uploaded']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/CustomerController > uploadFile | message: " . $error->getMessage());
        }
    }

    private function upload($file, ?string $folder_name)
    {
        Log::info("Start: Admin/CustomerController > upload | admin: ");
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
            Log::error("Error: Admin/CustomerController > upload | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    private function rollback($file)
    {
        Log::info("Start: Admin/CustomerController > rollback | admin: ");
        if ($file == null)
            return response()->json(['message' => '_rollback_empty'], 404);
        try {
            File::delete($file);
        } catch (Exception $error) {
            Log::error("Error: Admin/CustomerController > rollback | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    private function fileInfo($file)
    {
        Log::info("Start: Admin/CustomerController > fileInfo | admin: ");
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
            Log::error("Error: Admin/CustomerController > fileInfo | message: " . $error->getMessage());
        }
    }

    public function deleteFile(Request $request)
    {
        Log::info("Start: Admin/CustomerController > deleteFile | admin: " . $request);
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
            Log::error("Error: Admin/CustomerController > deleteFile | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function createFolder(Request $request)
    {
        Log::info("Start: Admin/CustomerController > createFolder | admin: " . $request);
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
                    ->where('name', request('name'))->where('customer_id', request('customer_id'))
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
                'type' => 'customer',
                'customer_id' => $request->customer_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'folder_created']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/CustomerController > createFolder | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function renameFolder(Request $request)
    {
        Log::info("Start: Admin/CustomerController > renameFolder | admin: " . $request);
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
                    ->where('name', request('name'))->where('customer_id', request('customer_id'))
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
            Log::error("Error: Admin/CustomerController > renameFolder | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function deleteFolder(Request $request)
    {
        Log::info("Start: Admin/CustomerController > deleteFolder | admin: " . $request);
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
            Log::error("Error: Admin/CustomerController > deleteFolder | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function deleteAll(Request $request)
    {
        Log::info("Start: Admin/CustomerController > deleteAll | admin: " . $request);
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
            Log::error("Error: Admin/CustomerController > deleteAll | message: " . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function restoreAll(Request $request)
    {
        Log::info("Start: Admin/CustomerController > restoreAll | admin: " . $request);
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
            Log::error("Error: Admin/CustomerController > restoreAll | message: " . $error->getMessage());
            return response()->json(['message' => $error->getLine()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            Session::flash('success', 'Customer has been moved to trash!');
        } catch (Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            CustomerHistory::where('customer_id', $customer->id)->delete();
            Purchase::where('customer_id', $customer->id)->delete();
            Invoice::where('customer_id', $customer->id)->delete();
            CreditNote::where('customer_id', $customer->id)->delete();
            Receipt::where('customer_id', $customer->id)->delete();
            WorkOrderInvoice::where('customer_id', $customer->id)->delete();
            WorkOrderReceipt::where('customer_id', $customer->id)->delete();
            $customer->forceDelete();
            Session::flash('success', 'Customer has been deleted!');
        } catch (Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function restore($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            $customer->restore();
            Session::flash('success', 'Customer restored success!');
        } catch (Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }   
        return back();
    }

    public function importExcel(ImportCustomerRequest $request)
    {
        try {
            $customerImport = new CustomerImport();
            Excel::import($customerImport, $request->file('customer_file'));
            if ($customerImport->message == 'invalid_column') {
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
