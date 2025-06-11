<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DMCFileManagerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DMCFileManagerController extends Controller
{
    protected $layout = 'admin::pages.dmc_file_manager.';
    private $DMCFileManagerService;

    function __construct(DMCFileManagerService $file)
    {
        $this->middleware('permission:dmc-file-manager-view', ['only' => ['index']]);
        $this->DMCFileManagerService = $file;
        
    }
    public function index()
    {
        return view($this->layout . 'index');
    }
    public function fetchDataYear(Request $request)
    {
        $data = $this->DMCFileManagerService->year($request);
        return response()->json(['data' => $data, 'message' => 'success']);
    }
    public function fetchDataYearOfMonth(Request $req)
    {
        $data = $this->DMCFileManagerService->fetchDataYearOfMonth($req);
        return response()->json(['data' => $data, 'message' => 'success']);
    }
    public function fetchData(Request $request)
    {
        $data = $this->DMCFileManagerService->fetchData($request);
        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function downloadFile(Request $req)
    {
        try {
            $file = public_path() . "/uploads/" . $req->file_path;
            return response()->download($file);
        } catch (Exception $e) {
            Session::flash('warning', 'download unsuccess!');
            return redirect()->back();
        }
    }
}
