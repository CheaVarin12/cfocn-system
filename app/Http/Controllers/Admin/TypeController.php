<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Type;
use Illuminate\Support\Facades\Log;

class TypeController extends Controller
{
    protected $layout = 'admin::pages.type.';
    public function __construct()
    {
        $this->middleware('permission:service-type-view', ['only' => ['index']]);
        $this->middleware('permission:service-type-create', ['only' => ['onSave', 'onCreate']]);
        $this->middleware('permission:service-type-update', ['only' => ['onEdit', 'onUpdateStatus']]);
    }
    public function index(Request $req)
    {
        Log::info("Start: Admin/TypeController > index | admin: ".$req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-type-list', 1);
            }
            if ($req->status != 'trash') {
                $query = Type::where('status', $req->status);
            } else {
                $query = Type::onlyTrashed();
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                }
            })
                ->orderBy('id', 'asc')->paginate(50);

            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/TypeController > index | message: ". $error->getMessage());
        }

    }
    public function onCreate()
    {
        Log::info("Start: Admin/TypeController > index | admin: ");
        try {
            return view($this->layout . 'create');
        } catch (Exception $error) {
            Log::error("Error: Admin/TypeController > index | message: ". $error->getMessage());
            return redirect()->back();
        }

    }
    public function onSave(Request $request, $id = null)
    {
        Log::info("Start: Admin/TypeController > onSave | admin: ".$request);
        $items = [
            'name' => $request->name,
            'status' => $request->status,
            'code' => $request->code,
        ];
        try {
            DB::beginTransaction();
            $status = "Create success.";
            if ($id) {
                $data = Type::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                Type::create($items);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-type-list', 1);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/TypeController > onSave | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
    public function onEdit($id)
    {
        Log::info("Start: Admin/TypeController > onEdit | admin: ");
        try {
            $data["data"] = Type::find($id);
            if ($data['data']) {
                return view($this->layout . 'edit', $data);
            }
            return redirect()->route('admin-type-list');
        } catch (Exception $error) {
            Log::error("Error: Admin/TypeController > onEdit | message: ". $error->getMessage());
            return redirect()->back();
        }

    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/TypeController > onUpdateStatus | admin: ".$req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = Type::find($req->id);
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
            Log::error("Error: Admin/TypeController > onUpdateStatus | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
}