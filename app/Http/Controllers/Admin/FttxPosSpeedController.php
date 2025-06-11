<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FttxPosSpeedRequest;
use App\Models\FttxPosSpeed;
use App\Models\FttxPriceByPosSpeed;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class FttxPosSpeedController extends Controller
{
    protected $layout = 'admin::pages.fttx.pos-speed.';
    public function __construct()
    {
        $this->middleware('permission:fttx-pos-speed-view', ['only' => ['index']]);
        $this->middleware('permission:fttx-pos-speed-update', ['only' => ['onEdit', 'onUpdateStatus']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/FttxPosSpeedController > index | admin: " . $req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-pos-speed-list', 1);
            }
            if ($req->status != 'trash') {
                $query = FttxPosSpeed::with('priceByPosSpeed')->where('status', $req->status);
            } else {
                $query = FttxPosSpeed::onlyTrashed();
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('split_pos', 'like', '%' . $search . '%');
                }
            })
                ->orderBy('id', 'asc')->paginate(50);

            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/FttxPosSpeedController > index | message: " . $error->getMessage());
        }
    }

    public function onSave(FttxPosSpeedRequest $request)
    {
        Log::info("Start: Admin/FttxPosSpeedController > onSave | admin: " . $request);
        $items = [
            'split_pos'                    => $request->split_pos,
            'key_search_import'            => $this->getLastValue($request->split_pos),
            'rental_price'                 => $this->jsonArray($request->rental_price ? $request->rental_price : ['']),
            'ppcc_price'                   => $this->jsonArray($request->ppcc_price ? $request->ppcc_price : ['']),
            'new_install_price'            => $this->jsonArray($request->new_install_price ? $request->new_install_price : ['']),
            'description'                  => $request->description,
            'user_id'                      => Auth::id(),
            'status'                       => $request->status,
        ];
        $id = $request->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = FttxPosSpeed::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                $data = FttxPosSpeed::create($items);

                FttxPriceByPosSpeed::create(
                    [
                        'pos_speed_id' => $data->id,
                        'rental_price_six_month' => 0,
                        'rental_price_twelve_month' => 0,
                    ]
                );
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/FttxPosSpeedController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/FttxPosSpeedController > onUpdateStatus | admin: ");
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = FttxPosSpeed::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status != '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/FttxPosSpeedController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function getLastValue($time)
    {
        if (strpos($time, ':') === false && strpos($time, '-') === false) {
            return null;
        }
        if (preg_match('/.*([:\-].+)$/', $time, $matches)) {
            return $matches[1];
        }
        return $time;
    }

    public function onUpdatePriceByPaymentPeriod(Request $request)
    {
        Log::info("Start: Admin/FttxPosSpeedController > onSave | admin: " . $request);
        $items = [
            'rental_price_six_month'             => $request->rental_price_six_month,
            'rental_price_twelve_month'          => $request->rental_price_twelve_month,
        ];
        $id = $request->id;
        DB::beginTransaction();
        try {
            $status = " success.";
            if ($id) {
                $data = FttxPriceByPosSpeed::find($id);
                $data->update($items);
                $status = "Update success.";
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'update unsuccessful!');
            Log::error("Error: Admin/FttxPosSpeedController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
}
