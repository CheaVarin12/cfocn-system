<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FttxSettingPriceRequest;
use App\Models\FttxSettingPrice;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FttxSettingPriceController extends Controller
{
    protected $layout = 'admin::pages.fttx.setting-price.';
    public function __construct()
    {
        $this->middleware('permission:fttx-setting-price-view', ['only' => ['index']]);
        $this->middleware('permission:fttx-setting-price-update', ['only' => ['onEdit', 'onUpdateStatus']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/FttxSettingPriceController > index | admin: ".$req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-setting-price-list', 1);
            }
            if ($req->status != 'trash') {
                $query = FttxSettingPrice::where('status', $req->status);
            } else {
                $query = FttxSettingPrice::onlyTrashed();
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('price', 'like', '%' . $search . '%');
                    $q->orWhere('type', 'like', '%' . $search . '%');
                }
            })
                ->orderBy('id', 'asc')->paginate(50);

            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/FttxSettingPriceController > index | message: ". $error->getMessage());
        }

    }

    public function onSave(FttxSettingPriceRequest $request)
    {
        Log::info("Start: Admin/FttxSettingPriceController > onSave | admin: " . $request);
        $items = [
            'price'                        => $this->jsonArray($request->price ? $request->price : ['']),
            'type'                         => $request->type,
            'description'                  => $request->description,
            'user_id'                      => Auth::id(),
            'status'                       => $request->status,
        ];
        $id=$request->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = FttxSettingPrice::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                $data = FttxSettingPrice::create($items);
            }
            DB::commit();
            return response()->json(['status'=>'success','message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/FttxSettingPriceController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/FttxSettingPriceController > onUpdateStatus | admin: ");
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = FttxSettingPrice::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status != '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/FttxSettingPriceController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
}
