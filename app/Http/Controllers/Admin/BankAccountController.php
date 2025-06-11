<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankAccountController extends Controller
{
    protected $layout = 'admin::pages.bank_account.';

    public function __construct()
    {
        $this->middleware('permission:bank-account', ['only' => ['index', 'onCreate','onSave','onEdit','onUpdateStatus']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/BankAccountController > index | admin: " . $req);
        try {
            $data['status'] = $req->status;
            $search = $req->search ? $req->search : '';
            if (!$req->status) {
                return redirect()->route('admin-bank-account-list', 1);
            }
            if ($req->status != 'trash') {
                $query = BankAccount::where('status', $req->status);
            } else {
                $query = BankAccount::onlyTrashed();
            }
            $data['data'] = $query->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('bank_name', 'like', '%' . $search . '%');
                    $q->orWhere('account_name', 'like', '%' . $search . '%');
                }
            })
                ->orderBy('id', 'asc')->paginate(25);

            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/BankAccountController > index | message: " . $error->getMessage());
        }
    }

    public function onCreate()
    {
        Log::info("Start: Admin/BankAccountController > onCreate | admin: ");
        try {
            return view($this->layout . 'create');
        } catch (Exception $error) {
            Log::error("Error: Admin/BankAccountController > onCreate | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onSave(Request $request, $id = null)
    {
        Log::info("Start: Admin/BankAccountController > onSave | admin: ".$request);
        $items = [
            'bank_name'         => $request->bank_name,
            'account_name'      => $request->account_name,
            'account_number'    => $request->account_number,
            'status'            => $request->status,
        ];
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = BankAccount::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                BankAccount::create($items);
            }
            DB::commit();
            Session::flash('success', $status);
            return redirect()->route('admin-bank-account-list', 1);
        } catch (Exception $error) {;
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/BankAccountController > onSave | message: ". $error->getMessage());
            return redirect()->back();
        }
    }

    public function onEdit($id)
    {
        Log::info("Start: Admin/BankAccountController > onEdit | admin: ");
        try {
            $data["data"] = BankAccount::find($id);
            if ($data['data']) {
                return view($this->layout . 'edit', $data);
            }
            return redirect()->route('admin-bank-account-list');
        } catch (Exception $error) {
            Log::error("Error: Admin/BankAccountController > onEdit | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onUpdateStatus(Request $req)
    {
        Log::info("Start: Admin/BankAccountController > onUpdateStatus | admin: " . $req);
        $statusGet = 'Enable';
        DB::beginTransaction();
        try {
            $data = BankAccount::find($req->id);
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
            Log::error("Error: Admin/BankAccountController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
}
