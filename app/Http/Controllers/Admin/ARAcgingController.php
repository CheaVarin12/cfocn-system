<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Purchase;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TotalARDetailReportExport;
use App\Exports\ARDetailReportExport;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ARAcgingController extends Controller
{
    protected $layout = 'admin::pages.report.ar_acging.';
    public function index(Request $request)
    {
        $data['data'] = Invoice::limit(100)->get();
        return view($this->layout . 'index', $data);
    }

    public function arDetail(Request $request, $id)
    {
        Log::info("Start: Admin/ARAcgingController > arDetail | admin: " . $request);
        try {
            $search = $request->search ? $request->search : '';
            $data['id'] = $id;
            $data['customerName'] = Customer::find($id)->name_en;
            $data['projects'] = [];
            $data['purchase'] = Purchase::where('customer_id', $id)->where('status', 1)->get();
            foreach ($data['purchase'] as $item) {
                if ($item->project) {
                    $data['projects'][$item->project->id] = $item->project->id;
                }
            }
            $query  = Project::whereIn('id', $data['projects']);
            if ($request->check == '' ||  $request->check == 'search') {
                $data['data'] = $query->where(function ($q) use ($search) {
                    if ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    }
                })->orderBy('id', 'asc')->paginate(25);
            }
            if ($request->check == 'export') {
                $data['data'] = $query->where(function ($q) use ($search) {
                    if ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    }
                })->orderBy('id', 'asc')->get();
                return Excel::download(new ARDetailReportExport($data), 'AR Detail list.xlsx');
            }

            return view($this->layout . 'detail-index', $data);
        } catch (Exception $e) {
            Log::error("Error: Admin/ARAcgingController > arDetail | message: " . $e->getMessage());
        }
    }
}
