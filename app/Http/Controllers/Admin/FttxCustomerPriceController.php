<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FttxCustomerPriceRequest;
use App\Models\FttxCustomerPrice;
use App\Models\FttxPosSpeed;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class FttxCustomerPriceController extends Controller
{
    protected $layout = 'admin::pages.fttx.customer-price.';
    public function __construct()
    {
        $this->middleware('permission:fttx-customer-price-view', ['only' => ['index']]);
        $this->middleware('permission:fttx-customer-price-update', ['only' => ['onUpdateStatus']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/FttxCustomerPriceController > index | admin: " . $req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-customer-price-list', 1);
            }
            if ($req->status != 'trash') {
                $query = FttxCustomerPrice::where('status', $req->status);
            } else {
                $query = FttxCustomerPrice::onlyTrashed();
            }
            $data['data'] = $query->with('customer')->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                    $q->whereHas('customer', function ($qq) use ($search) {
                        $qq->where('name_en', 'like', '%' . $search . '%');
                        $qq->where('name_kh', 'like', '%' . $search . '%');
                    });
                }
            })
                ->orderBy('id', 'asc')->paginate(50);

            $data['posSpeeds'] = FttxPosSpeed::where('status', 1)->get();
            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/FttxCustomerPriceController > index | message: " . $error->getMessage());
        }
    }
    public function onSave(FttxCustomerPriceRequest $request)
    {
        Log::info("Start: Admin/FttxCustomerPriceController > onSave | admin: " . $request);
        $items = [
            'customer_id'   => $request->customer_id,
            'new_install_price' => json_encode([
                'first'     => $request->new_install_price_first_level,
                'second'    => $request->new_install_price_second_level,
                'third'     => $request->new_install_price_third_level,
                'fourth'    => $request->new_install_price_fourth_level,
            ]),
            'pos_speeds'    => json_encode([$request->pos_speeds]),
            'user_id'       => Auth::id(),
            'status'        => $request->status,
        ];
        try {
            $id = $request->id;
            DB::beginTransaction();
            $status = "Create success.";
            if ($id) {
                $data = FttxCustomerPrice::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                FttxCustomerPrice::create($items);
            }
            DB::commit();
            Session::flash('success', $status);
            return response()->json(['status' => 'success', 'message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/FttxCustomerPriceController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/FttxCustomerPriceController > onUpdateStatus | admin: " . $req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = FttxCustomerPrice::find($req->id);
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
            Log::error("Error: Admin/FttxCustomerPriceController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
}
