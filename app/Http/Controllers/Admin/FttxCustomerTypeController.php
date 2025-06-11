<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FttxCustomerTypeRequest;
use App\Models\FttxCustomerType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class FttxCustomerTypeController extends Controller
{
    protected $layout = 'admin::pages.fttx.customer-type.';
    public function __construct()
    {
        $this->middleware('permission:fttx-customer-type-view', ['only' => ['index']]);
        $this->middleware('permission:fttx-customer-type-update', ['only' => ['onUpdateStatus']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/FttxCustomerTypeController > index | admin: ".$req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-customer-type-list', 1);
            }
            if ($req->status != 'trash') {
                $query = FttxCustomerType::where('status', $req->status);
            } else {
                $query = FttxCustomerType::onlyTrashed();
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })
                ->orderBy('id', 'asc')->paginate(50);

            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/FttxCustomerTypeController > index | message: ". $error->getMessage());
        }

    }
    public function onSave(FttxCustomerTypeRequest $request)
    {
        Log::info("Start: Admin/FttxCustomerTypeController > onSave | admin: ".$request);
        $items = [
            'name'          => $request->name,
            'description'   => $request->description,
            'user_id'       => Auth::id(),
            'status'        => $request->status,
        ];
        try {
            $id=$request->id;
            DB::beginTransaction();
            $status = "Create success.";
            if ($id) {
                $data = FttxCustomerType::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                FttxCustomerType::create($items);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['status'=>'success','message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/FttxCustomerTypeController > onSave | message: ". $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/FttxCustomerTypeController > onUpdateStatus | admin: ".$req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = FttxCustomerType::find($req->id);
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
            Log::error("Error: Admin/FttxCustomerTypeController > onUpdateStatus | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
}
