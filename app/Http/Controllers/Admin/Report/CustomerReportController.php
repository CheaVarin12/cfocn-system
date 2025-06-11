<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\CustomerDMCReportExport;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerReportExport;
use App\Exports\OldCustomerReportExport;
use App\Http\Requests\Admin\EditCustomerDMCRequest;
use App\Http\Requests\Admin\ImportCustomerDMCRequest;
use App\Http\Requests\Admin\ImportCustomerRequest;
use App\Imports\OldCustomerImport;
use App\Models\Customer;
use App\Models\DMCCustomer;
use App\Models\DMCPurchase;
use App\Models\HistoryDmcSendFile;
use App\Models\OldCustomerInfo;
use Illuminate\Support\Facades\Storage;
use App\Services\FTPConnectionService;
use App\Services\ReportCustomerInfoService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerReportController extends Controller
{
    private $disk = null;
    private $sftp = null;
    private $serverConnection = null;
    private $message = null;
    private $serverReportCustomerInfo = null;
    protected $layout = 'admin::pages.report.customer.';

    public function __construct(FTPConnectionService $ser, ReportCustomerInfoService $customerSer)
    {
        $this->middleware('permission:report-customer-view', ['only' => ['index']]);
        $this->disk = Storage::disk('ftp');
        $this->serverConnection = $ser;
        $this->serverReportCustomerInfo = $customerSer;
    }
    public function index(Request $req)
    {
        $startDate = Carbon::now();
        $from_date = $req->from_date ? $req->from_date : $startDate->firstOfMonth()->toDate()->format('Y-m-d');
        $to_date =  $req->to_date ? $req->to_date : $startDate->lastOfMonth()->toDate()->format('Y-m-d');
        $data["from_date"] = $from_date;
        $data["to_date"]   = $to_date;
        $data["projectData"] = [];
        $data['server_connection_status'] = 'unable_connection';
        $data['data'] = $this->serverReportCustomerInfo->fetchData($req);
        $data['projects'] = Project::where('status', 1)->get();
        $data['projectInExport'] =  $this->dataGroupByII($data['data']);
        $nameExport = "customer_info_" . $from_date . "_" . $to_date . '.xlsx';
        if ($req->check == "export") {
            return Excel::download(new CustomerReportExport($data), $nameExport);
        } else if ($req->check == "submitDMC") {
            $res = $this->serverConnection->ServerLogin();
            DB::beginTransaction();
            try {
                if ($res == "login_success") {
                    // $startDate = Carbon::now();
                    $startDate = $req->from_date;

                    $year = $this->formatDate($startDate, 'Y');

                    $month = $this->formatDate($startDate, 'm');
                    $day = $this->formatDate($startDate, 'd');

                    $datePathUpload = $year . '/' . $month;

                    $invoiceID = $this->formatDate($req->from_date) . $this->formatDate($req->to_date);
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
                        'from_date' => $req->from_date,
                        'to_date'   => $req->to_date,
                        'user_id'   => Auth::user()->id
                    ];


                    $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
                    $invoicePathSubmitDMCByTypeProject = ($configDMCPath?->mainDev ?? '') . $configDMCPath->customer_Info;

                    //submitDMC
                    $this->serverConnection->dmcFile($invoicePathSubmitDMCByTypeProject, $file, $nameExport, function ($result, $err) use ($docItem, $res) {
                        if ($result == true) {
                            HistoryDmcSendFile::create($docItem);

                            //sendMail
                            //$this->sendMail($dataHistory);

                            $this->message = 'success';
                        } else {
                            $this->message = 'unsuccess';
                        }
                    });
                    Session::flash('success', 'Report customer dmc submit success');
                    DB::commit();
                    return response()->json([
                        'data' => null,
                        'message' => $this->message,
                        'connection_status' => $res
                    ]);
                } else {
                    return response()->json([
                        'data' => null,
                        'message' => 'unsuccess',
                        'connection_status' => $res
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'data' => null,
                    'err'  => $e->getMessage(),
                    'message' => 'unsuccess',
                    'connection_status' => $res
                ]);
            }
        }
        return view($this->layout . 'index', $data);
    }
    public function formatDate($date, $format = null)
    {
        return Carbon::parse($date)->format(($format ? $format : 'Ymd'));
    }
    public function queryData($req)
    {
        $queryFilter = Purchase::with(['customer', 'type', 'purchaseDetail'])->where(function ($query) use ($req) {
            $query->whereHas('customer', function ($q) use ($req) {
                if (isset($req?->search) && $req?->search) {
                    $q->where('customer_code', 'like', '%' . $req->search . '%');
                    $q->orWhere('name_en', 'like', '%' . $req->search . '%');
                    $q->orWhere('name_kh', 'like', '%' . $req->search . '%');
                }
                if ($req->from_date && $req->to_date) {
                    $q->whereDate('register_date', '>=', $req->from_date);
                    $q->whereDate('register_date', '<=', $req->to_date);
                }
            });
            if (isset($req->search_project) && $req?->search_project) {
                $query->where('project_id', $req->search_project);
            }
        })->orderBy('id', 'asc')->get();
        $project = isset($req->search_project) && $req?->search_project ? Project::find($req->search_project) : null;
        $item = [
            "from_date" => $req->from_date,
            "to_date"   => $req->to_date,
            "data"  => $queryFilter,
            "projectData" => $project,
            "projectInExport" =>  $this->dataGroupBy($queryFilter)
        ];
        return $item;
    }

    public function dataGroupBy($data)
    {
        $dataItem = [];
        if (count($data) > 0) {
            foreach ($data as $index => $item) {
                if (count($dataItem) > 0) {
                    if (!isset($dataItem[$item->project_id])) {
                        $dataItem[$item->project_id] = Project::find($item->project_id);
                    }
                } else {
                    $dataItem[$item->project_id] = Project::find($item->project_id);
                }
            }
        }
        return $dataItem;
    }
    public function dataGroupByII($data)
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

    //Old Customer Info 
    public function oldCustomerIndex(Request $req)
    {
        $startDate = Carbon::now();
        $from_date = $req->from_date ? $req->from_date : $startDate->firstOfMonth()->toDate()->format('Y-m-d');
        $to_date =  $req->to_date ? $req->to_date : $startDate->lastOfMonth()->toDate()->format('Y-m-d');
        $data["projectData"] = [];
        $data['server_connection_status'] = 'unable_connection';
        $data['data'] = OldCustomerInfo::when(request('search'), function ($q) {
            $q->where('customer_code', 'like', '%' . request('search') . '%');
            $q->orWhere('customer_name', 'like', '%' . request('search') . '%');
            $q->orWhere('po_number', 'like', '%' . request('search') . '%');
            $q->orWhere('pac_number', 'like', '%' . request('search') . '%');
        })
            ->when(request('from_date') && request('to_date'), function ($q) {
                $q->whereDate('register_date', '>=', request('from_date'));
                $q->whereDate('register_date', '<=', request('to_date'));
            })
            ->orderBy('customer_code')
            ->get();
        $nameExport = "customer_information_" . $from_date . "_" . $to_date . '.xlsx';
        if ($req->check == "submitDMC") {
            $res = $this->serverConnection->ServerLogin();
            DB::beginTransaction();
            try {
                if ($res == "login_success") {
                    $startDate = $req->from_date;
                    $year = $this->formatDate($startDate, 'Y');
                    $month = $this->formatDate($startDate, 'm');
                    $day = $this->formatDate($startDate, 'd');

                    $datePathUpload = $year . '/' . $month;
                    $pathFile = 'customer_info/' . $datePathUpload . '/' . $nameExport;

                    //storeFileInSystem
                    Excel::store(new OldCustomerReportExport($data), $pathFile);

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
                        'from_date' => $req->from_date,
                        'to_date'   => $req->to_date,
                        'user_id'   => Auth::user()->id
                    ];

                    $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
                    $invoicePathSubmitDMCByTypeProject = ($configDMCPath?->mainDev ?? '') . $configDMCPath->customer_Info;

                    //submitDMC
                    $this->serverConnection->dmcFile($invoicePathSubmitDMCByTypeProject, $file, $nameExport, function ($result, $err) use ($docItem, $res) {
                        if ($result == true) {
                            HistoryDmcSendFile::create($docItem);
                            $this->message = 'success';
                        } else {
                            $this->message = 'unsuccess';
                        }
                    });
                    Session::flash('success', 'Report customer dmc submit success');
                    DB::commit();
                    return response()->json([
                        'data' => null,
                        'message' => $this->message,
                        'connection_status' => $res
                    ]);
                } else {
                    return response()->json([
                        'data' => null,
                        'message' => 'unsuccess',
                        'connection_status' => $res
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'data' => null,
                    'err'  => $e->getMessage(),
                    'message' => 'unsuccess',
                    'connection_status' => $res
                ]);
            }
        }
        return view('admin::pages.report.old_customer.index', $data);
    }

    public function oldCustomerImportExcel(ImportCustomerRequest $request)
    {
        try {
            $file = $request->file('customer_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $row_limit = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $rowRange = range(3, $row_limit);

            foreach ($rowRange as $row) {
                if ($sheet->getCell('A' . $row)->getValue() != '') {
                    OldCustomerInfo::create([
                        'register_date' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('A' . $row)->getValue()))->format('Y-m-d'),
                        'customer_code' => $sheet->getCell('B' . $row)->getFormattedValue(),
                        'customer_name' => $sheet->getCell('C' . $row)->getValue(),
                        'po_number' => $sheet->getCell('D' . $row)->getValue(),
                        'pac_number' => $sheet->getCell('E' . $row)->getValue(),
                        'customer_address' => $sheet->getCell('F' . $row)->getValue(),
                        'service_type' => $sheet->getCell('G' . $row)->getValue(),
                        'description' => $sheet->getCell('H' . $row)->getValue(),
                        'type' => $sheet->getCell('I' . $row)->getValue(),
                        'qty_cores' => $sheet->getCell('J' . $row)->getValue(),
                        'length' => $sheet->getCell('K' . $row)->getValue(),
                        'status' => $sheet->getCell('L' . $row)->getValue() == 'Active' ? 1 : 2,
                        'inactive_date' => $sheet->getCell('M' . $row)->getValue() != '' ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('M' . $row)->getValue()))->format('Y-m-d') : null,
                        'user_id'   => Auth::user()->id
                    ]);
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'File imported success!',
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'danger',
                'message' => 'Import Failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    //Customer DCM
    public function customerDMC(Request $req)
    {
        $startDate = Carbon::now();
        $data['server_connection_status'] = 'unable_connection';
        $data['data'] = DMCPurchase::with('dmcCustomer')
            ->when(request('search_project'), function ($q) {
                $q->where('project_id', request('search_project'));
            })->when(request('from_date') && request('to_date'), function ($q) {
                $q->whereDate('pac_date', '>=', request('from_date'));
                $q->whereDate('pac_date', '<=', request('to_date'));
            })
            ->orderBy('id', 'asc')
            ->get();
        $data['projects'] = Project::where('status', 1)->get();
        $data['projectInExport'] = $this->dataGroupBy($data['data']);
        $currentYearMonth = $this->formatDate($startDate, 'Y') . $this->formatDate($startDate, 'm');

        $searchProject = $req->search_project;
        if ($searchProject == 1) {
            $projectName = 'Infra_Customer_info';
        } else if ($searchProject == 2) {
            $projectName = 'Submarine_Cust_Info';
        } else if ($searchProject == 4) {
            $projectName = 'Underground_Cust_Info';
        } else {
            $projectName = 'Customer_Info';
        }
        if ($req->check == "export") {
            $nameExport = $currentYearMonth . '_' . $projectName . '.xlsx';
            return Excel::download(new CustomerDMCReportExport($data), $nameExport);
        } else if ($req->check == "submitDMC") {
            $res = $this->serverConnection->ServerLogin();
            DB::beginTransaction();
            try {
                if ($res == "login_success") {
                    $startDate = Carbon::now()->subMonth();
                    $year = $this->formatDate($startDate, 'Y');
                    $month = $this->formatDate($startDate, 'm');
                    $day = $this->formatDate($startDate, 'd');
                    $lastMonth = $this->formatDate($startDate, 'Y') . $this->formatDate($startDate, 'm');
                    $nameExport = $lastMonth . '_' . $projectName . '.csv';

                    $datePathUpload = $year . '/' . $month;
                    $pathFile = 'customer_info/' . $datePathUpload . '/' . $nameExport;

                    //storeFileInSystem
                    Excel::store(new CustomerDMCReportExport($data), $pathFile);

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
                        'from_date' => $req->from_date,
                        'to_date'   => $req->to_date,
                        'user_id'   => Auth::user()->id
                    ];

                    $configDMCPath = (object) config('dmc-file-manager.submitPathFolder');
                    if ($searchProject == 2) {
                        $invoicePathSubmitDMCByTypeProject = $configDMCPath->submarine_customer_info;
                    } else {
                        $invoicePathSubmitDMCByTypeProject = $configDMCPath->infra_customer_info;
                    }

                    //submitDMC
                    $this->serverConnection->dmcFile($invoicePathSubmitDMCByTypeProject, $file, $nameExport, function ($result, $err) use ($docItem, $res) {
                        if ($result == true) {
                            HistoryDmcSendFile::create($docItem);
                            $this->message = 'success';
                        } else {
                            $this->message = 'unsuccess';
                        }
                    });
                    Session::flash('success', 'Report customer dmc submit success');
                    DB::commit();
                    return response()->json([
                        'data' => null,
                        'message' => $this->message,
                        'connection_status' => $res
                    ]);
                } else {
                    return response()->json([
                        'data' => null,
                        'message' => 'unsuccess',
                        'connection_status' => $res
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'data' => null,
                    'err'  => $e->getMessage(),
                    'message' => 'unsuccess',
                    'connection_status' => $res
                ]);
            }
        }
        return view('admin::pages.report.customer_dmc.index', $data);
    }

    public function customerDMCImportExcel(ImportCustomerDMCRequest $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('customer_file');
            // $fileName = $file->getClientOriginalName();
            // $explodeFileName = explode('_', $fileName);
            // $pacDate = count($explodeFileName) > 0 ? Carbon::createFromFormat('Ym', $explodeFileName[0])->format('Y-m-d') : null;

            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rowLimit = $sheet->getHighestDataRow();
            $rowRange = range(3, $rowLimit);
            foreach ($rowRange as $row) {
                if ($sheet->getCell('A' . $row)->getValue() != '') {
                    $customerCode = $sheet->getCell('B' . $row)->getFormattedValue();
                    $existingCustomer = DMCCustomer::where('customer_code', $customerCode)->first();
                    if (!$existingCustomer) {
                        $dmcCustomer = DMCCustomer::create([
                            'register_date' => Carbon::parse($sheet->getCell('A' . $row)->getFormattedValue())->format('Y-m-d'),
                            'customer_code' => $sheet->getCell('B' . $row)->getFormattedValue(),
                            'customer_name' => $sheet->getCell('C' . $row)->getValue(),
                            'customer_address' => $sheet->getCell('F' . $row)->getValue(),
                            'status' => $sheet->getCell('L' . $row)->getValue() == 'Active' ? 1 : 2,
                            'inactive_date' => $sheet->getCell('M' . $row)->getValue() != '' ? Carbon::parse($sheet->getCell('M' . $row)->getFormattedValue())->format('Y-m-d') : null,
                        ]);
                    }
                    DMCPurchase::create([
                        'dmc_customer_id' => $existingCustomer ? $existingCustomer->id : $dmcCustomer?->id,
                        'project_id' => $request->project_id,
                        'register_date' => Carbon::parse($sheet->getCell('A' . $row)->getFormattedValue())->format('Y-m-d'),
                        'customer_code' => $sheet->getCell('B' . $row)->getFormattedValue(),
                        'customer_name' => $sheet->getCell('C' . $row)->getValue(),
                        'po_number' => $sheet->getCell('D' . $row)->getFormattedValue(),
                        'pac_number' => $sheet->getCell('E' . $row)->getFormattedValue(),
                        'customer_address' => $sheet->getCell('F' . $row)->getValue(),
                        'service_type' => $sheet->getCell('G' . $row)->getValue(),
                        'description' => $sheet->getCell('H' . $row)->getValue(),
                        'type' => $sheet->getCell('I' . $row)->getValue(),
                        'qty_cores' => $sheet->getCell('J' . $row)->getFormattedValue(),
                        'length' => $sheet->getCell('K' . $row)->getFormattedValue(),
                        'status' => $sheet->getCell('L' . $row)->getValue() == 'Active' ? 1 : 2,
                        'inactive_date' => $sheet->getCell('M' . $row)->getValue() != '' ? Carbon::parse($sheet->getCell('M' . $row)->getFormattedValue())->format('Y-m-d') : null,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'File imported success!',
                'error' => false,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'danger',
                'message' => 'Import Failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function editCustomer(EditCustomerDMCRequest $request)
    {
        DB::beginTransaction();
        try {
            $dmcPurchase = DMCPurchase::find($request->id);
            $dmcPurchase->update([
                'po_number'         => $request->po_number,
                'pac_number'        => $request->pac_number,
                'service_type'      => $request->service_type,
                'type'              => $request->type, 
                'qty_cores'         => $request->qty_cores,
                'length'            => $request->length,
                'register_date'     => Carbon::make($request->register_date),
                'po_date'           => Carbon::make($request->po_date),
                'pac_date'          => Carbon::make($request->pac_date),
                'customer_code'     => $request->customer_code,
                'customer_name'     => $request->customer_name,
                'customer_address'  => $request->customer_address,
                'status'            => $request->status,
                'inactive_date'     => $request->inactive_date ? Carbon::make($request->inactive_date) : null,
                'location'          => $request->location,
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Updated Success!',
                'error' => false,
                'data' =>$dmcPurchase,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'danger',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function onDelete(Request $request)
    {
        try {
            $dmcPurchase = DMCPurchase::find($request->id);
            $dmcPurchase->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted Success!',
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Delete failed',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getCustomerDmcById(){
        $data = DMCPurchase::with('dmcCustomer')->find(Request('id'));
        return response()->json($data);
    }
}
