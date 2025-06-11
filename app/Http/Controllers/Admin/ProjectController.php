<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    protected $layout = 'admin::pages.project.';

    public function __construct()
    {
        $this->middleware('permission:project-view', ['only' => ['index']]);
        $this->middleware('permission:project-create', ['only' => ['onCreate', 'onSave']]);
        $this->middleware('permission:project-update', ['only' => ['onEdit', 'onSave', 'onUpdateStatus']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/ProjectController > index | admin: ". $req);
        try{
            $data['status'] = $req->status;

            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-project-list', 1);
            }else{
                $query = Project::where('status', $req->status);
            }
           
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })
                ->orderBy('name', 'asc')->paginate(50);
    
            return view($this->layout . 'index', $data);
        }catch(Exception $error){
            Log::error("Error: Admin/PageController > index | message: ". $error->getMessage());
        }
       
    }

    public function onCreate()
    {
        Log::info("Start: Admin/ProjectController > onCreate | admin: ");
        try{
            return view($this->layout . 'create');
        }catch(Exception $error){
            Log::error("Error: Admin/PageController > onCreate | message: ". $error->getMessage());
        }
    }

    public function onSave(Request $request, $id = null)
    {
        Log::info("Start: Admin/ProjectController > onSave | admin: ".$request);
        $items = [
            'name' => $request->name,
            'vat_tin' => $request->vat_tin,
            'phone' => $request->phone,
            'status' => $request->status,
        ];
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = Project::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
               $data = Project::create($items);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-project-list', 1);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/PageController > onSave | message: ". $error->getMessage());
            return redirect()->back();
        }
    }

    public function onEdit($id)
    {
        Log::info("Start: Admin/ProjectController > onEdit | admin: ");
        try{
             $data["data"] = Project::find($id);
        if ($data) {
            return view($this->layout . 'create', $data);
        }
        return redirect()->route('admin-project-list'); 
        }catch(Exception $error){
            Log::error("Error: Admin/PageController > onEdit | message: ". $error->getMessage());
            return redirect()->back();
        }
      
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/ProjectController > onUpdateStatus | admin: ");
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = Project::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status != '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/PageController > onUpdateStatus | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
}
