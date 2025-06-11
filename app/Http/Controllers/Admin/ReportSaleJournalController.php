<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportSaleJournalController extends Controller
{
    protected $layout = 'admin::pages.report.sale-journal.';

    public function __construct()
    {
        $this->middleware('permission:report-sale-journal-view', ['only' => ['index']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/ReportController > index | admin: ".$req);
        try {
            $data['projects'] = Project::whereStatus(1)->orderByDesc('id')->get();
            
            return view($this->layout.'index', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/ReceiptController > index | message: ". $error->getMessage());
        }
    }
}
