<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CloseDateRequest;
use App\Models\CloseDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CloseDateController extends Controller
{
    protected $layout = 'admin::pages.close_date.';

    public function __construct()
    {
        $this->middleware('permission:close-date', ['only' => ['index', 'onCreate', 'onSave', 'onEdit', 'onSave', 'onUpdateStatus']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/CloseDateController > index | admin: " . $req);
        try {
            $data['status'] = $req->status;

            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-close-date-list', 1);
            } else {
                $query = CloseDate::where('status', $req->status);
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                }
            })->orderBy('date', 'desc')->paginate(50);
            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/CloseDateController > index | message: " . $error->getMessage());
        }
    }

    public function onCreate()
    {
        Log::info("Start: Admin/CloseDateController > onCreate | admin: ");
        try {
            return view($this->layout . 'create');
        } catch (Exception $error) {
            Log::error("Error: Admin/CloseDateController > onCreate | message: " . $error->getMessage());
        }
    }

    public function onSave(CloseDateRequest $request, $id = null)
    {
        Log::info("Start: Admin/CloseDateController > onSave | admin: " . $request);
        $item = $request->all();
        $item['date'] = Carbon::parse($request->date)->format('Y-m-d');
        $item['user_id'] = Auth::user()->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = CloseDate::find($id);
                $data->update($item);
                $status = "Update success.";
            } else {
                $data = CloseDate::create($item);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-close-date-list', 1);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/CloseDateController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onEdit($id)
    {
        Log::info("Start: Admin/CloseDateController > onEdit | admin: ");
        try {
            $data["data"] = CloseDate::find($id);
            if ($data) {
                return view($this->layout . 'create', $data);
            }
            return redirect()->route('admin-close-date-list');
        } catch (Exception $error) {
            Log::error("Error: Admin/CloseDateController > onEdit | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/CloseDateController > onUpdateStatus | admin: ");
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = CloseDate::find($req->id);
            $data->update(['status' => $req->status]);
            if ($data->status != '1') {
                $statusGet = 'Disable';
            }
            DB::commit();
            Session::flash('success', $statusGet);
            return redirect()->back();
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/CloseDateController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
}
