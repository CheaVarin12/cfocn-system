<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Exception;

class LogoControlController extends Controller
{
    protected $layout = 'admin::pages.logo-control.';
    public function __construct()
    {
        $this->middleware('permission:logo-control', ['only' => ['index', 'store']]);
    }

    public function index()
    {
        Log::info("Start: Admin/LogoControlController > index | admin: ");
        try {
            $data['logoControl'] = DB::table('logo_controls')->first();
            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/LogoControlController > index | message: ". $error->getMessage());
        }
    }

    public function store(Request $req)
    {
        Log::info("Start: Admin/LogoControlController > store | admin: ");
        $logoControl = DB::table('logo_controls')->first();
        $status = "Create success.";
        DB::beginTransaction();
        try {
            if (!$logoControl) {
                DB::table('logo_controls')->insert([
                    'status' => $req->status
                ]);
            } else {
                DB::table('logo_controls')->where('id', $logoControl->id)->update([
                    'status' => $req->status
                ]);
                $status = "Update success.";
            }
            DB::commit();
            Session::flash("success", $status);
            return redirect()->route("admin-logo-control-index");
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccess!');
            Log::error("Error: Admin/LogoControlController > store | message: ". $error->getMessage());
            return redirect()->back();
        }
    }
}
