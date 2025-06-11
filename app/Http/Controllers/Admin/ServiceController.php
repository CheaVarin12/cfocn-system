<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Service;
use App\Models\Type;
use Illuminate\Support\Facades\Log;
class ServiceController extends Controller
{
    protected $layout = 'admin::pages.service.';
    public function __construct()
    {
        $this->middleware('permission:service-view', ['only' => ['index']]);
        $this->middleware('permission:service-create', ['only' => ['onCreate','onSave']]);
        $this->middleware('permission:service-update', ['only' => ['onEdit', 'onUpdateStatus','onSave']]);
    }
    public function index(Request $req)
    {
        Log::info("Start: Admin/ServiceController > index | admin: ".$req);
        try{
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-service-list', 1);
            }
            if ($req->status != 'trash') {
                $query = Service::with('type')->where('status', $req->status);
            } else {
                $query = Service::with('type')->onlyTrashed();
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })
                ->orderBy('id', 'asc')->paginate(50);
    
            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/ServiceController > index | message: ". $error->getMessage());
        }

    }
    public function onCreate()
    {
        Log::info("Start: Admin/ServiceController > onCreate | admin: ");
        try{
            $data['types'] = Type::where('status',1)->get();
        return view($this->layout . 'create',$data); 
    } catch (Exception $error) {
        Log::error("Error: Admin/ServiceController > onCreate | message: ". $error->getMessage());
        return redirect()->back();
    }
       
    }
    public function onSave(Request $request, $id = null)
    {
        Log::info("Start: Admin/ServiceController > onSave | admin: ".$request);
        $items = [
            'type_id' => $request->type_id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ];
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = Service::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                Service::create($items);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-service-list', 1);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/ServiceController > onSave | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
    public function onEdit($id)
    {
        Log::info("Start: Admin/ServiceController > onEdit | admin: ");
        try{
             $data["data"] = Service::find($id);
        $data['types'] = Type::where('status',1)->get();
        if ($data['data']) {
            return view($this->layout . 'edit', $data);
        }
        return redirect()->route('admin-service-list');
    } catch (Exception $error) {
        Log::error("Error: Admin/ServiceController > onEdit | message: ". $error->getMessage());
        return redirect()->back();
    }
       
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/ServiceController > onUpdateStatus | admin: ".$req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = Service::find($req->id);
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
            Log::error("Error: Admin/ServiceController > onUpdateStatus | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
}
