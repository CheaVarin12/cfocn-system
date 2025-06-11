<?php

namespace App\Services;

use App\Exports\CustomerReportExport;
use App\Models\Customer;
use App\Models\HistoryDmcSendFile;
use App\Models\Project;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportCustomerInfoService
{
    private $disk = null;
    private $FTPConnectionService = null;
    public function __construct(FTPConnectionService $ser)
    {
        $this->FTPConnectionService = $ser;
        $this->disk = Storage::disk('ftp');
    }
    public function fetchData($req = null)
    {
        $data = Customer::with(["latestPurchase" => function ($q) use ($req) {
            $q->orderBy('created_at', 'desc');
            $q->with(["purchaseDetail", "type"]);
            if (isset($req?->search_project) && $req?->search_project) {
                $q->where('project_id', $req->search_project);
            }
        }])->where(function ($q) use ($req) {
            if (isset($req?->search) && $req?->search) {
                $q->where('customer_code', 'like', '%' . $req->search . '%');
                $q->orWhere('name_en', 'like', '%' . $req->search . '%');
                $q->orWhere('name_kh', 'like', '%' . $req->search . '%');
            }
            if ($req?->from_date && $req?->to_date) {
                $q->whereDate('register_date', '>=', $req->from_date);
                $q->whereDate('register_date', '<=', $req->to_date);
            }
            if (isset($req?->search_project) && $req?->search_project) {
                $q->whereHas('latestPurchase', function ($q) use ($req) {
                    $q->where('project_id', $req->search_project);
                });
            }
        })->get();
        return $data;
    }
    public function submitFile($data)
    {
        DB::beginTransaction();
        $res = $this->FTPConnectionService->ServerLogin();
        if ($res != "login_success") {
            return "fail";
        }
        try {
            $startDate = Carbon::now();
            $year = $this->formatDate($startDate, 'Y');
            $month = $this->formatDate($startDate, 'm');
            $day = $this->formatDate($startDate, 'd');
            $nameExport = 'customer_info_' . $this->formatDate($startDate) . '.xlsx';
            $datePathUpload = $year . '/' . $month;
            $pathFile = 'customer_info/' . $datePathUpload . '/' . $nameExport;

            //storeFileInSystem
            Excel::store(new CustomerReportExport($data), $pathFile);

            //getUrl
            $file =  public_path('uploads/' . $pathFile);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            $docItem = [
                'invoice_id' => null,
                'year' => $year,
                'month' => $month,
                'day'   => $day,
                'file_name' => $nameExport,
                'file_path' => '/' . $pathFile,
                'file_type' => 'customer_info',
                'extension_type' => $extension,
                'from_date' => $this->formatDate($startDate),
                'to_date'   => $this->formatDate($startDate)
            ];

            //SendFileDMC
            $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
            $customerPathSubmitDMC = ($configDMCPath?->mainDev ?? '') . $configDMCPath->customer_Info;
            $this->disk->putFileAs($customerPathSubmitDMC, $file, $nameExport);

            //insertRecordFile
            $dataFile = HistoryDmcSendFile::create($docItem);

            DB::commit();
            return response()->json([
                'data' => $dataFile,
                'err'  => false,
                'message' => 'success'
            ]);
        } catch (Exception $err) {
            DB::rollback();
            return response()->json([
                'data' => null,
                'err'  => $err->getMessage(),
                'message' => 'unsuccess'
            ]);
        }
    }
    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }
    public function dataGroupBy($data)
    {
        $dataItem = [];
        $j = [];
        if (count($data) > 0) {
            foreach ($data as $index => $item) {
                if ($item?->latestPurchase?->project_id) {
                    if (count($dataItem) > 0) {
                        if (!isset($dataItem[$item?->latestPurchase?->project_id])) {
                            $dataItem[$item?->latestPurchase?->project_id] = Project::find($item?->latestPurchase?->project_id);
                        }
                    } else {
                        $dataItem[$item->latestPurchase->project_id] = Project::find($item->latestPurchase->project_id);
                    }
                }
            }
        }
        return $dataItem;
    }
}
