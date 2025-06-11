<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class RateController extends Controller
{
    protected $layout = 'admin::pages.rate.';
    public function __construct()
    {
        $this->middleware('permission:exchange-rate', ['only' => ['index', 'store']]);
    }

    public function index()
    {
        Log::info("Start: Admin/RateController > index | admin: ");
        try {
            $data['rate'] = DB::table('rates')->first();
            return view($this->layout . 'rate', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/RateController > index | message: ". $error->getMessage());
        }
    }

    public function store(Request $req)
    {
        Log::info("Start: Admin/RateController > store | admin: ");
        $rate = DB::table('rates')->first();
        $status = "Create success.";
        DB::beginTransaction();
        try {
            if (!$rate) {
                DB::table('rates')->insert([
                    'rate' => $req->rate
                ]);
            } else {
                DB::table('rates')->where('id', $rate->id)->update([
                    'rate' => $req->rate
                ]);
                $status = "Update success.";
            }
            DB::commit();
            Session::flash("success", $status);
            return redirect()->route("admin-rate-index");
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/RateController > store | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
}