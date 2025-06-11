<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\LicenseFee;

class LicenseFeeController extends Controller
{
    protected $layout = 'admin::pages.license_fee.';
    public function __construct()
    {
        $this->middleware('permission:license-fee', ['only' => ['index']]);
    }
    public function index(Request $req){
        Log::info("Start: Admin/LicenseFeeController > index | admin: ");
        try{
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            
            if (!$req->status) {
                return redirect()->route('admin-license-fee-list', 1);
            }
    
            if ($req->status != 'trash') {
                $query =LicenseFee::where('status', $req->status);
            } else {
               $query = LicenseFee::onlyTrashed();
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('year', 'like', '%' . $search . '%');
                    $q->orWhereHas('project', function ($q) {
                        $q->where('name', 'like', '%' . request('search') . '%');
                });   
                }
            })
                ->orderBy('id', 'asc')->paginate(25);
    
            return view($this->layout . 'index',$data);
        }catch(Exception $e){
            Log::error("Error: Admin/LicenseFeeController > index | message: ". $e->getMessage());
        }
    }

    public function onCreate(){
        Log::info("Start: Admin/LicenseFeeController > onCreate | admin: ");
        try{
            $data['projects']= Project::where('status',1)->get();
            return view($this->layout . 'create',$data);
        }catch(Exception $e){
            Log::error("Error: Admin/LicenseFeeController > onCreate | message: ". $e->getMessage());
        }
    }
    
    public function onSave(Request $request){
        Log::info("Start: Admin/LicenseFeeController > onSave | admin: " .$request);
        $items = [
            'project_id'=> $request->project_id,
            'percentage' => $request->percentage,
            'license_fee' => $request->license_fee,
            'year' => $request->year,
            'status' => $request->status,
            
        ];
        DB::beginTransaction();
        try{
            $status = "Create success.";
            LicenseFee::create($items);
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-license-fee-list', 1);
        
        }catch(Exception $e){
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/LicenseFeeController > onSave | message: ". $e->getMessage());
            return redirect()->back();
        }
        
    }

    public function onUpdate(Request $request,$id){
        Log::info("Start: Admin/LicenseFeeController > onUpdate | admin: " .$request);
        $items = [
            'project_id'=> $request->project_id,
            'percentage' => $request->percentage,
            'license_fee' => $request->license_fee,
            'year' => $request->year,
            'status' => $request->status,
            
        ];
        DB::beginTransaction();
        try{
            $status = "Update success.";
            LicenseFee::find($id)->update($items);
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-license-fee-list', 1);
        
        }catch(Exception $e){
            DB::rollback();
            Session::flash('warning', 'Update unsuccessful!');
            Log::error("Error: Admin/LicenseFeeController > onUpdate | message: ". $e->getMessage());
            return redirect()->back();
        }
    }


    public function onEdit($id){
        Log::info("Start: Admin/LicenseFeeController > onEdit | admin: ");
        try{
            $data['projects']= Project::where('status',1)->get();
            $data['data'] = LicenseFee::find($id);
            return view($this->layout . 'edit', $data);
        }catch(Exception $e){
            Log::error("Error: Admin/LicenseFeeController > onEdit | message: ". $e->getMessage());
        }
    }

    public function onUpdateStatus(Request $req)
    { 
        Log::info("Start: Admin/LicenseFeeController > onUpdateStatus | admin:".$req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data=DB::table('license_fees')->where('id',$req->id)->update(['status' => $req->status]);
            $data=DB::table('license_fees')->where('id',$req->id)->first();
            if ($data->status != 1) {
                $statusGet = 'Disable';
            }else{
                $statusGet = 'Enable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $e) {

            $status = false;
            DB::rollback();
            Log::error("Error: Admin/LicenseFeeController > onUpdateStatus | message: ". $e->getMessage());
            return redirect()->back();
        }
    }
}
