<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FttxExport;
use App\Exports\TemplateFttxUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FttxDetailRequest;
use App\Http\Requests\Admin\FttxRenewalRequest;
use App\Http\Requests\Admin\FttxRequest;
use App\Http\Requests\Admin\ImportFttxRequest;
use App\Imports\FttxImport;
use App\Models\Fttx;
use App\Models\FttxCustomerPrice;
use App\Models\FttxCustomerType;
use App\Models\FttxDetail;
use App\Models\FttxPosSpeed;
use App\Models\FttxSettingPrice;
use App\Models\FttxShowHideColumn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

class FttxController extends Controller
{
    protected $layout = 'admin::pages.fttx.fttx.';

    public function __construct()
    {
        $this->middleware('permission:fttx-view', ['only' => ['index']]);
        $this->middleware('permission:fttx-create', ['only' => ['onCreate', 'importExcel']]);
        $this->middleware('permission:fttx-update', ['only' => ['onEdit', 'onUpdateStatus']]);
        $this->middleware('permission:fttx-delete', ['only' => ['onDelete']]);
        $this->middleware('permission:fttx-renewal', ['only' => ['onRenewal']]);
    }

    public function index(Request $req)
    {
        Log::info("Start: Admin/FttxController > index | admin: " . json_encode($req->all()));
        try {
            $posSpeedId = $req->pos_speed_id ? explode(",", $req->pos_speed_id) : '';
            $status = $req->status && request('status') != 'all' ? explode(",", $req->status) : '';
            $today = Carbon::now()->format('d-M-Y');

            $baseQuery = Fttx::with('customer')->when(filled(request('search')), function ($q) {
                $q->where(function ($q) {
                    $q->where('work_order_isp', 'like', '%' . request('search') . '%');
                    $q->orWhere('work_order_cfocn', 'like', '%' . request('search') . '%');
                    $q->orWhere('subscriber_no', 'like', '%' . request('search') . '%');
                    $q->orWhere('team_install', 'like', '%' . request('search') . '%');
                    $q->orWhereHas('customer', function ($qq) {
                        $qq->where('name_en', 'like', '%' . request('search') . '%');
                        $qq->orWhere('name_kh', 'like', '%' . request('search') . '%');
                    });
                });
            })
                ->when(request('status') && request('status') != 'all', function ($q) use ($status) {
                    $q->whereIn('status', $status);
                })
                ->when(request('pos_speed_id'), function ($q) use ($posSpeedId) {
                    $q->whereIn('pos_speed_id', $posSpeedId);
                })
                ->when(request('start_completed_date') && request('end_completed_date'), function ($q) {
                    $q->whereDate('completed_time', '>=', request('start_completed_date'));
                    $q->whereDate('completed_time', '<=', request('end_completed_date'));
                })
                ->when(request('start_payment_date') && request('end_payment_date'), function ($q) {
                    $q->whereDate('payment_date', '>=', request('start_payment_date'));
                    $q->whereDate('payment_date', '<=', request('end_payment_date'));
                })
                ->when(request('start_dismantle_date') && request('end_dismantle_date'), function ($q) {
                    $q->whereDate('dismantle_date', '>=', request('start_dismantle_date'));
                    $q->whereDate('dismantle_date', '<=', request('end_dismantle_date'));
                })
                ->when(request('start_deadline_date') && request('end_deadline_date'), function ($q) {
                    $q->whereDate('deadline', '>=', request('start_deadline_date'));
                    $q->whereDate('deadline', '<=', request('end_deadline_date'));
                })
                ->when(request('expire'), function ($q) {
                    $q->where('deadline', '<', Carbon::today())
                        ->where('status', '!=', 3)
                        ->where(function ($query) {
                            $query->where('check_status', '!=', 'inactive')
                                ->orWhereNull('check_status');
                        });
                })
                ->when(request('trash'), function ($q) {
                    $q->onlyTrashed();
                })
                ->orderByRaw("CASE WHEN check_status = 'active' THEN 0 ELSE 1 END")
                ->orderBy("id", "desc");

            if ($req->check == "export") {
                $data['data'] = $baseQuery->get()->map(function ($item) {
                    $item->total_calculate = in_array($item->status, [1, 4, 5]) ? Round($item->new_installation_fee + $item->fiber_jumper_fee + $item->digging_fee + (($item->rental_price + $item->ppcc + $item->rental_pole) * $item->first_payment_period) + $item->other_fee - $item->discount, 2) : $item->total;
                    $item->total_new_installation_fee = $item->fttxDetail ? $item->fttxDetail->sum('new_installation_fee') : '';
                    $item->total_fiber_jumper_fee = $item->fttxDetail ? $item->fttxDetail->sum('fiber_jumper_fee') : '';
                    $item->total_digging_fee = $item->fttxDetail ? $item->fttxDetail->sum('digging_fee') : '';
                    $item->total_rental_unit_price = $item->fttxDetail ? $item->fttxDetail->sum('rental_unit_price') : '';
                    $item->total_ppcc = $item->fttxDetail ? $item->fttxDetail->sum('ppcc') : '';
                    $item->total_pole_rental_fee = $item->fttxDetail ? $item->fttxDetail->sum('pole_rental_fee') : '';
                    $item->total_other_fee = $item->fttxDetail ? $item->fttxDetail->sum('other_fee') : '';
                    $item->total_discount = $item->fttxDetail ? $item->fttxDetail->sum('discount') : '';
                    $item->total_total_amount = $item->fttxDetail ? $item->fttxDetail->sum('total_amount') : '';
                    return $item;
                });
            } else {
                $data['data'] = $baseQuery->paginate(100)->through(function ($item) {
                    $item->total_calculate = in_array($item->status, [1, 4, 5]) ? Round($item->new_installation_fee + $item->fiber_jumper_fee + $item->digging_fee + (($item->rental_price + $item->ppcc + $item->rental_pole) * $item->first_payment_period) + $item->other_fee - $item->discount, 2) : $item->total;

                    return $item;
                });
            }

            // Clone for count queries
            $cloneQuery = clone $baseQuery;

            $totalWorkOrderExpire = (clone $cloneQuery)
                ->where('deadline', '<', Carbon::today())
                ->where('status', '!=', 3)
                ->where(function ($query) {
                    $query->whereNull('check_status')
                        ->orWhere('check_status', '!=', 'inactive');
                })
                ->get();

            $totalWorkOrderNewInstall = (clone $cloneQuery)
                ->where('status', 1)
                ->where('check_status', 'active')
                ->get();

            $totalWorkOrderReactive = (clone $cloneQuery)
                ->where('status', 4)
                ->where('check_status', 'active')
                ->get();

            $totalWorkOrderRelocation = (clone $cloneQuery)
                ->where('status', 5)
                ->where('check_status', 'active')
                ->get();

            if (request('expire') && request('check_renewal_all')) {
                $this->onRenewalAll($totalWorkOrderExpire);
            }

            $data['totalWorkOrderExpire'] = $totalWorkOrderExpire->count();
            $data['totalWorkOrderNewInstall'] = $totalWorkOrderNewInstall->count();
            $data['totalWorkOrderReactive'] = $totalWorkOrderReactive->count();
            $data['totalWorkOrderRelocation'] = $totalWorkOrderRelocation->count();

            $data['customerType'] = FttxCustomerType::where('status', 1)->get();
            $data['posSpeed'] = FttxPosSpeed::where('status', 1)->get();

            $data['fiberJumperFee'] = FttxSettingPrice::where('type', config('dummy.setting_price_type.fiber_jumper_fee.key'))
                ->where('status', 1)
                ->pluck('price')
                ->flatten()
                ->all();

            $data['diggingFee'] = FttxSettingPrice::where('type', config('dummy.setting_price_type.digging_fee.key'))
                ->where('status', 1)
                ->pluck('price')
                ->flatten()
                ->all();

            $data['rentalPole'] = FttxSettingPrice::where('type', config('dummy.setting_price_type.rental_pole.key'))
                ->where('status', 1)
                ->pluck('price')
                ->flatten()
                ->all();

            $firstDate = FttxDetail::orderBy('date', 'asc')->first();
            $endDate = FttxDetail::orderBy('date', 'desc')->first();

            $data["firstDate"] = isset($firstDate->date) ? Carbon::parse($firstDate->date) : null;
            $data["endDate"] = isset($endDate->date) ? Carbon::parse($endDate->date) : null;
            $data["totalMonth"] = ($data["firstDate"] && $data["endDate"])
                ? $data["firstDate"]->diffInMonths($data["endDate"]) + 1
                : 0;

            if ($req->check == "export") {
                return Excel::download(new FttxExport($data), 'fttx_' . $today . '.xlsx');
            }

            $data['columnFttx'] = FttxShowHideColumn::all();
            return view($this->layout . 'index', $data);
        } catch (Exception $error) {
            Log::error("Error: Admin/FttxController > index | message: " . $error->getMessage());
            abort(500, 'Internal Server Error');
        }
    }


    public function onSave(FttxRequest $request)
    {
        Log::info("Start: Admin/FttxController > onSave | admin: " . $request);
        $items = [
            'work_order_isp'                              => $request->work_order_isp,
            'customer_id'                                 => $request->customer_id,
            'work_order_cfocn'                            => $request->work_order_cfocn,
            'subscriber_no'                               => $request->subscriber_no,
            'isp_ex_work_order_isp'                       => $request->isp_ex_work_order_isp,
            'status'                                      => $request->status,
            'name'                                        => $request->name,
            'phone'                                       => $request->phone,
            'address'                                     => $request->address,
            'zone'                                        => $request->zone,
            'city'                                        => $request->city,
            'port'                                        => $request->port,
            'pos_speed_id'                                => $request->pos_speed_id,
            'applicant_team_install'                      => $request->applicant_team_install,
            'team_install'                                => $request->team_install,
            'create_time'                                 => $request->create_time,
            'completed_time'                              => $request->completed_time,
            'date_ex_complete_old_order'                  => $request->date_ex_complete_old_order,
            'dismantle_date'                              => $request->dismantle_date,
            'dismantle_order_cfocn'                       => $request->dismantle_order_cfocn,
            'lay_fiber'                                   => $request->lay_fiber,
            'remark_first'                                => $request->remark_first,
            'reactive_date'                               => $request->reactive_date,
            'reactive_payment_period'                     => $request->reactive_payment_period,
            'change_splitter_date'                        => $request->change_splitter_date,
            'relocation_date'                             => $request->relocation_date,
            'start_payment_date'                          => $request->start_payment_date,
            'last_payment_date'                           => $request->last_payment_date,
            'initial_installation_order_complete_time'    => $request->initial_installation_order_complete_time,
            'first_relocation_order_complete_date'        => $request->first_relocation_order_complete_date,
            'payment_date'                                => $request->payment_date,
            'payment_status'                              => $request->payment_status,
            'online_days'                                 => $request->online_days,
            'deadline'                                    => $request->deadline,
            'customer_type'                               => $request->customer_type,
            'new_installation_fee'                        => $request->new_installation_fee,
            'fiber_jumper_fee'                            => $request->fiber_jumper_fee,
            'digging_fee'                                 => $request->digging_fee,
            'first_payment_period'                        => $request->first_payment_period,
            'initial_payment_period'                      => $request->initial_payment_period,
            'rental_price'                                => $request->rental_price,
            'ppcc'                                        => $request->ppcc,
            'number_of_pole'                              => $request->number_of_pole,
            'rental_pole'                                 => $request->rental_pole,
            'other_fee'                                   => $request->other_fee,
            'discount'                                    => $request->discount,
            'total'                                       => $request->total,
            'remark_second'                               => $request->remark_second,
            'check_status'                                => $this->getStatusCheck($request->deadline, $request->subscriber_no),
            'reactive_date_check'                => $request->reactive_date ? $this->checkDataStoreOrNot($request->reactive_date, 'reactive_date', $request->work_order_isp, $request->work_order_cfocn, $request->subscriber_no) : null,
            'change_splitter_date_check'         => $request->change_splitter_date ? $this->checkDataStoreOrNot($request->change_splitter_date, 'change_splitter_date', $request->work_order_isp, $request->work_order_cfocn, $request->subscriber_no) : null,
            'relocation_date_check'              => $request->relocation_date ? $this->checkDataStoreOrNot($request->relocation_date, 'relocation_date', $request->work_order_isp, $request->work_order_cfocn, $request->subscriber_no) : null,
            'user_id'                                     => Auth::id(),
        ];
        $id = $request->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = Fttx::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                $data = Fttx::create($items);
                $this->createFttxDetail($data);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/FttxController > onSave | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function createFttxDetail($data)
    {
        try {
            $totalMonth = getNumberOfMonth($data->completed_time ?? $data->start_payment_date, $data->deadline);
            $totalMonth = round($totalMonth);
            $detailDate = $this->adjustDayToMatch($data->completed_time, $data->deadline) ?? $this->adjustDayToMatch($data->start_payment_date, $data->deadline);
            $fttxDetailData = collect([]);
            $data->first_payment_period = $data->first_payment_period && $data->first_payment_period > 0 ? $data->first_payment_period : $totalMonth;
            $checkTrueStatus = $this->getBiggestDate($data->reactive_date_check, $data->change_splitter_date_check, $data->relocation_date_check);
            if ($data->status == 3) {
                $workOrderIsp = $data->work_order_isp;
                $workOrderCfocn = $data->work_order_cfocn;
                $subscriberNo = $data->subscriber_no;
                $getStartNewCompleteDate = Fttx::where('status', 2)
                    ->when($subscriberNo, function ($q) use ($subscriberNo) {
                        $q->where('subscriber_no', $subscriberNo);
                    })
                    ->orderBy('deadline', 'desc')
                    ->value('deadline');

                $detailDate = $getStartNewCompleteDate;
                $totalMonth = getNumberOfMonth($getStartNewCompleteDate, $data->deadline);
                $totalMonth = round($totalMonth);
                if ($data->dismantle_date > $data->deadline) {
                    $totalMonth =  $totalMonth + $this->countMonthsIncludePartial($data->deadline, $data->dismantle_date);
                }

                for ($i = 1; $i <=  $totalMonth; $i++) {
                    $fttxDetails = [];
                    $rentalPrice = $this->getPrice($data->customer_id, $data->pos_speed_id, $detailDate, $data->rental_price, $data->first_payment_period);
                    if ($i == 1) {
                        $dataFttx = Fttx::find($data->id);
                        if ($dataFttx) {
                            $dataFttx->update(['rental_price' => $rentalPrice]);
                        }
                    }
                    if ($i == 1) {
                        $fttxDetails = [
                            'fttx_id'               => $data->id,
                            'customer_id'           => $data->customer_id,
                            'date'                  => $detailDate,
                            'expiry_date'           => addMonth($detailDate, 1),
                            'new_installation_fee'  => null,
                            'fiber_jumper_fee'      => null,
                            'digging_fee'           => null,
                            'rental_unit_price'     => $rentalPrice,
                            'ppcc'                  => $data->ppcc,
                            'pole_rental_fee'       => $data->rental_pole,
                            'other_fee'             => null,
                            'discount'              => null,
                            'remark'                => null,
                            'invoice_number'        => null,
                            'receipt_number'        => null,
                            'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                            'user_id'               => Auth::id(),
                        ];
                    } else {
                        $fttxDetails = [
                            'fttx_id'               => $data->id,
                            'customer_id'           => $data->customer_id,
                            'date'                  => $detailDate,
                            'expiry_date'           => $fttxDetailData ? addMonth($fttxDetailData->expiry_date, 1) : null,
                            'new_installation_fee'  => null,
                            'fiber_jumper_fee'      => null,
                            'digging_fee'           => null,
                            'rental_unit_price'     => $rentalPrice,
                            'ppcc'                  => $data->ppcc,
                            'pole_rental_fee'       => $data->rental_pole,
                            'other_fee'             => null,
                            'discount'              => null,
                            'remark'                => null,
                            'invoice_number'        => null,
                            'receipt_number'        => null,
                            'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                            'user_id'               => Auth::id(),
                        ];
                    }

                    $detailDate = addMonth($detailDate, 1);
                    if ($fttxDetails) {
                        $fttxDetailData = FttxDetail::create($fttxDetails);
                    }
                }
            } else {
                if ($data->status == 2 && $data->reactive_date_check && $checkTrueStatus == 'reactive_date' || $data->status == 4) {
                    $workOrderIsp = $data->work_order_isp;
                    $workOrderCfocn = $data->work_order_cfocn;
                    $subscriberNo = $data->subscriber_no;
                    $ispExWorkOrderIsp = $data->isp_ex_work_order_isp;

                    $getLastDismantleFttx = null;

                    // If not found, try by subscriber_no
                    if ($subscriberNo) {
                        $getLastDismantleFttx = Fttx::where('status', 3)
                            ->where('subscriber_no', $subscriberNo)
                            ->orderBy('dismantle_date', 'desc')
                            ->first();
                    }

                    // If not found, try by work_order_cfocn
                    if (!$getLastDismantleFttx && $workOrderCfocn) {
                        $getLastDismantleFttx = Fttx::where('status', 3)
                            ->where('work_order_cfocn', $workOrderCfocn)
                            ->orderBy('dismantle_date', 'desc')
                            ->first();
                    }
                    // Try by work_order_isp
                    if (!$getLastDismantleFttx && $workOrderIsp) {
                        $getLastDismantleFttx = Fttx::where('status', 3)
                            ->where('work_order_isp', $workOrderIsp)
                            ->orderBy('dismantle_date', 'desc')
                            ->first();
                    }

                    // If not found, try by isp_ex_work_order_isp
                    if (!$getLastDismantleFttx && $ispExWorkOrderIsp) {
                        $getLastDismantleFttx = Fttx::where('status', 3)
                            ->where('isp_ex_work_order_isp', $ispExWorkOrderIsp)
                            ->orderBy('dismantle_date', 'desc')
                            ->first();
                    }

                    if ($getLastDismantleFttx->dismantle_date <= $getLastDismantleFttx->deadline) {
                        $getStartNewCompleteDate = $getLastDismantleFttx->deadline;
                    } elseif ($getLastDismantleFttx->dismantle_date > $getLastDismantleFttx->deadline) {
                        $getStartNewCompleteDate = addMonth($getLastDismantleFttx->deadline, $this->countMonthsIncludePartial($getLastDismantleFttx->deadline, $getLastDismantleFttx->dismantle_date));
                    }
                    $totalMonth = getNumberOfMonth($getStartNewCompleteDate, $data->deadline);
                    $detailDate = $getStartNewCompleteDate;
                    $this->removeFttxDetail($getStartNewCompleteDate, $data->work_order_isp, $data->work_order_cfocn, $data->subscriber_no, $data->deadline);
                    $totalPayWhenReactive = 0;
                    $rentalPricePayWhenReactive = 0;
                    $ppccPayWhenReactive = 0;
                    $rentalPolePayWhenReactive = 0;
                    $checkSameDate = false;
                    $dateInReactive = null;
                    for ($i = 1; $i <=  $totalMonth; $i++) {
                        $fttxDetails = [];
                        $samDate = $this->isSameMonth($data->reactive_date, $detailDate) ?? false;
                        $rentalPrice = $this->getPrice($data->customer_id, $data->pos_speed_id, $detailDate, $data->rental_price, $data->first_payment_period);

                        if ($i == 1) {
                            $dataFttx = Fttx::find($data->id);
                            if ($dataFttx) {
                                $dataFttx->update(['rental_price' => $rentalPrice]);
                            }
                        }

                        if ($samDate) {
                            $checkSameDate = true;
                            $fttxDetails = [
                                'fttx_id'               => $data->id,
                                'customer_id'           => $data->customer_id,
                                'date'                  => $detailDate,
                                'expiry_date'           => $detailDate ? addMonth($detailDate, $data->reactive_payment_period) : null,
                                'new_installation_fee'  => $samDate ? $data->new_installation_fee : null,
                                'fiber_jumper_fee'      => $samDate ? $data->fiber_jumper_fee : null,
                                'digging_fee'           => $samDate ? $data->digging_fee : null,
                                'rental_unit_price'     => round($rentalPrice * $data->reactive_payment_period + $rentalPricePayWhenReactive, 2),
                                'ppcc'                  => round($data->ppcc * $data->reactive_payment_period + $ppccPayWhenReactive, 2),
                                'pole_rental_fee'       => round($data->rental_pole * $data->reactive_payment_period + $rentalPolePayWhenReactive, 2),
                                'other_fee'             => $samDate ? $data->other_fee : null,
                                'discount'              => null,
                                'remark'                => null,
                                'invoice_number'        => null,
                                'receipt_number'        => null,
                                'total_amount'          => round(($samDate ? $data->new_installation_fee : 0) + ($samDate ? $data->fiber_jumper_fee : 0) + ($samDate ? $data->digging_fee : 0) + ($samDate ? $data->other_fee : 0) + (($rentalPrice + $data->ppcc + $data->rental_pole) * $data->reactive_payment_period) + $totalPayWhenReactive, 2),
                                'user_id'               => Auth::id(),
                            ];
                            $dateInReactive = $detailDate;
                        } else {
                            if ($checkSameDate) {
                                if ($dateInReactive && addMonth($dateInReactive, $data->reactive_payment_period)  < $data->deadline && $detailDate >= addMonth($dateInReactive, $data->reactive_payment_period)) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($detailDate, 1) ?? null,
                                        'new_installation_fee'  => $samDate ? $data->new_installation_fee : null,
                                        'fiber_jumper_fee'      => $samDate ? $data->fiber_jumper_fee : null,
                                        'digging_fee'           => $samDate ? $data->digging_fee : null,
                                        'rental_unit_price'     => $rentalPrice,
                                        'ppcc'                  => $data->ppcc,
                                        'pole_rental_fee'       => $data->rental_pole,
                                        'other_fee'             => $samDate ? $data->other_fee : null,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round(($samDate ? $data->new_installation_fee : 0) + ($samDate ? $data->fiber_jumper_fee : 0) + ($samDate ? $data->digging_fee : 0) + ($samDate ? $data->other_fee : 0) + ($rentalPrice + $data->ppcc + $data->rental_pole), 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            }

                            $totalPayWhenReactive += round(($samDate ? $data->new_installation_fee : 0) + ($samDate ? $data->fiber_jumper_fee : 0) + ($samDate ? $data->digging_fee : 0) + ($samDate ? $data->other_fee : 0) + ($rentalPrice + $data->ppcc + $data->rental_pole), 2);
                            $rentalPricePayWhenReactive += $rentalPrice;
                            $ppccPayWhenReactive +=  $data->ppcc;
                            $rentalPolePayWhenReactive += $data->other_fee;
                        }
                        $detailDate = addMonth($detailDate, 1);
                        if ($fttxDetails) {
                            $fttxDetailData = FttxDetail::create($fttxDetails);
                        }
                    }
                } elseif ($data->status == 2 && $data->change_splitter_date_check && $checkTrueStatus == 'change_splitter_date') {
                    $firstPaymentPeriod = $data->first_payment_period ?? 0;
                    $inPeriod = false;
                    $inAfterPeriod = false;
                    if ($data->change_splitter_date < addMonth($data->completed_time, 12)) {
                        $inPeriod = true;
                    } else {
                        $inAfterPeriod = true;
                    }
                    $totalMonth = getNumberOfMonth($data->completed_time, $data->deadline);
                    $totalMonth = round($totalMonth);
                    $detailDate = $data->completed_time;
                    $subscriberNo = $data->subscriber_no;
                    $getDataBeforeLast = Fttx::when($subscriberNo, function ($q) use ($subscriberNo) {
                        $q->where('subscriber_no', $subscriberNo);
                    })
                        ->orderBy('id', 'desc')->get()
                        ->skip(1)
                        ->first();
                    $getStartNewCompleteDate = $getDataBeforeLast->deadline;

                    for ($i = 1; $i <=  $totalMonth; $i++) {
                        $fttxDetails = [];
                        $rentalPrice = $this->getPrice($data->customer_id, $data->pos_speed_id, $detailDate, $data->rental_price, $data->first_payment_period);
                        if ($inPeriod) {
                            if ($detailDate <= $data->change_splitter_date && $data->change_splitter_date <= addMonth($detailDate, 1)) {
                                $getLastReportDerail = FttxDetail::where('fttx_id', $data->id)->latest()->first();
                                if ($getLastReportDerail && $getLastReportDerail->new_installation_fee) {
                                    $getLastReportDerail->update([
                                        'new_installation_fee' => 0,
                                        'fiber_jumper_fee' => 0,
                                        'digging_fee' => 0,
                                        'rental_unit_price' => 0,
                                        'total_amount' => 0,
                                    ]);
                                }

                                $startDetail = Carbon::parse($detailDate);
                                $endDetail = Carbon::parse(addMonth($detailDate, 1));
                                $changeSplitterDate = Carbon::parse($data->change_splitter_date);
                                $totalDayOfMonth =  $startDetail->diffInDays($endDetail);
                                $totalDayOldPrice =  $startDetail->diffInDays($changeSplitterDate);
                                $totalDayNewPrice = $changeSplitterDate->diffInDays($endDetail);
                                $secondToLast = Fttx::where('id', '<', $data->id)
                                    ->orderBy('id', 'desc')
                                    ->first();
                                $oldPrice = $secondToLast->rental_price;
                                $newPrice = $rentalPrice;

                                if ($oldPrice < $newPrice) {
                                    $totalOldNewRentalPrice = round($newPrice - $oldPrice, 2);
                                } else {
                                    $totalOldNewRentalPrice = null;
                                }
                                $this->removeFttxDetail($detailDate, $data->work_order_isp, $data->work_order_cfocn, $data->subscriber_no, $data->deadline);
                                $dataFttx = Fttx::find($data->id);
                                if ($dataFttx) {
                                    $dataFttx->update(['rental_price' => $rentalPrice]);
                                }
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => $data->new_installation_fee,
                                    'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                    'digging_fee'           => $data->digging_fee,
                                    'rental_unit_price'     => $totalOldNewRentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => $data->other_fee,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + $data->other_fee + ($totalOldNewRentalPrice + $data->ppcc + $data->rental_pole), 2),
                                    'user_id'               => Auth::id(),
                                ];
                                $indexCheck = $i;
                            }
                            if (isset($oldPrice) && isset($newPrice) && $indexCheck < $i && $i < 13) {
                                if ($oldPrice < $newPrice) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                        'new_installation_fee'  => null,
                                        'fiber_jumper_fee'      => null,
                                        'digging_fee'           => null,
                                        'rental_unit_price'     => round($newPrice - $oldPrice, 2),
                                        'ppcc'                  => $data->ppcc,
                                        'pole_rental_fee'       => $data->rental_pole,
                                        'other_fee'             => null,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round(round($newPrice - $oldPrice, 2) + $data->ppcc + $data->rental_pole, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            }
                            if (12 < $i) {
                                if (12 < $i && 13 > $i) {
                                    $detailDate = addMonth($data->completed_time, 12);
                                }
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        } elseif ($inAfterPeriod) {
                            if ($detailDate <= $data->change_splitter_date && $data->change_splitter_date <= addMonth($detailDate, 1)) {
                                $getLastReportDerail = FttxDetail::where('fttx_id', $data->id)->latest()->first();
                                if ($getLastReportDerail && $getLastReportDerail->new_installation_fee) {
                                    $getLastReportDerail->update([
                                        'new_installation_fee' => 0,
                                        'fiber_jumper_fee' => 0,
                                        'digging_fee' => 0,
                                        'rental_unit_price' => 0,
                                        'total_amount' => 0,
                                    ]);
                                }
                                $startDetail = Carbon::parse($detailDate);
                                $endDetail = Carbon::parse(addMonth($detailDate, 1));
                                $changeSplitterDate = Carbon::parse($data->change_splitter_date);

                                $totalDayOfMonth =  $startDetail->diffInDays($endDetail);
                                $totalDayOldPrice =  $startDetail->diffInDays($changeSplitterDate);
                                $totalDayNewPrice = $changeSplitterDate->diffInDays($endDetail);
                                $secondToLast = Fttx::where('id', '<', $data->id)
                                    ->orderBy('id', 'desc')
                                    ->first();
                                $oldPrice = $secondToLast->rental_price;
                                $newPrice = $rentalPrice;
                                if ($oldPrice < $newPrice) {
                                    $totalOldNewRentalPrice = round($newPrice - $oldPrice, 2);
                                } else {
                                    $totalOldNewRentalPrice = round(($totalDayOldPrice * round($oldPrice / $totalDayOfMonth, 2)), 2) +  round(($totalDayNewPrice * round($newPrice / $totalDayOfMonth, 2)), 2);
                                }

                                $this->removeFttxDetail($detailDate, $data->work_order_isp, $data->work_order_cfocn, $data->subscriber_no, $data->deadline);
                                $dataFttx = Fttx::find($data->id);
                                if ($dataFttx) {
                                    $dataFttx->update(['rental_price' => $rentalPrice]);
                                }
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => $data->new_installation_fee,
                                    'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                    'digging_fee'           => $data->digging_fee,
                                    'rental_unit_price'     => $totalOldNewRentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => $data->other_fee,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + $data->other_fee + ($totalOldNewRentalPrice + $data->ppcc + $data->rental_pole), 2),
                                    'user_id'               => Auth::id(),
                                ];

                                $indexCheck = $i;
                            }
                            if (isset($indexCheck) && $i > $indexCheck) {
                                $dataFttx = Fttx::find($data->id);
                                if ($dataFttx) {
                                    $dataFttx->update(['rental_price' => $rentalPrice]);
                                }
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                            if ($detailDate >= $getStartNewCompleteDate && !isset($indexCheck)) {
                                $dataFttx = Fttx::find($data->id);
                                if ($dataFttx) {
                                    $dataFttx->update(['rental_price' => $rentalPrice]);
                                }
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $getDataBeforeLast->rental_price,
                                    'ppcc'                  => $getDataBeforeLast->ppcc,
                                    'pole_rental_fee'       => $getDataBeforeLast->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($getDataBeforeLast->rental_price + $getDataBeforeLast->ppcc + $getDataBeforeLast->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        }

                        $detailDate = addMonth($detailDate, 1);
                        if (isset($fttxDetails) && $fttxDetails) {
                            if ($fttxDetails) {
                                $fttxDetailData = FttxDetail::create($fttxDetails);
                            }
                            $fttxDetails = [];
                        }
                    }
                } elseif ($data->status == 2 && $data->relocation_date_check && $checkTrueStatus == 'relocation_date' || $data->status == 5) {
                    $fttxFirst = Fttx::where('subscriber_no', $data->subscriber_no)->first();
                    $firstPaymentPeriod =  $fttxFirst->first_payment_period ?? 0;
                    $firstCompleteDate = $fttxFirst->completed_time;
                    $inPeriod12 = false;
                    $inPeriod6 = false;
                    $inPeriod3 = false;
                    $inAfterPeriod = false;
                    if ($firstPaymentPeriod == 12) {
                        $totalMonthFromCompleteDate = getNumberOfMonth($firstCompleteDate, $data->relocation_date);
                        if ($data->relocation_date < addMonth($firstCompleteDate, 12)) {
                            $inPeriod12 = true;
                        } else {
                            $inAfterPeriod = true;
                        }
                    } elseif ($firstPaymentPeriod == 6) {
                        $totalMonthFromCompleteDate = getNumberOfMonth($firstCompleteDate, $data->relocation_date);
                        if ($data->relocation_date < addMonth($firstCompleteDate, 12)) {
                            $inPeriod6 = true;
                        } else {
                            $inAfterPeriod = true;
                        }
                    } elseif ($firstPaymentPeriod == 3) {
                        $totalMonthFromCompleteDate = getNumberOfMonth($firstCompleteDate, $data->relocation_date);
                        if ($data->relocation_date < addMonth($firstCompleteDate, 12)) {
                            $inPeriod3 = true;
                        } else {
                            $inAfterPeriod = true;
                        }
                    }
                    $totalMonth = getNumberOfMonth($firstCompleteDate, $data->deadline);
                    $totalMonth = round($totalMonth);
                    $detailDate =  $firstCompleteDate;

                    for ($i = 1; $i <=  $totalMonth; $i++) {
                        $rentalPrice = $this->getPrice($data->customer_id, $data->pos_speed_id, $detailDate, $data->rental_price, $data->first_payment_period);
                        if ($i == 1) {
                            $dataFttx = Fttx::find($data->id);
                            if ($dataFttx) {
                                $dataFttx->update(['rental_price' => $rentalPrice]);
                            }
                        }
                        if ($inPeriod12) {
                            if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $data->deadline,
                                    'new_installation_fee'  => $data->new_installation_fee,
                                    'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                    'digging_fee'           => $data->digging_fee,
                                    'rental_unit_price'     => null,
                                    'ppcc'                  => ($data->ppcc ?? 0) * round(getNumberOfMonth($data->completed_time, $data->deadline)),
                                    'pole_rental_fee'       => ($data->rental_pole ?? 0) * round(getNumberOfMonth($data->completed_time, $data->deadline)),
                                    'other_fee'             => $data->other_fee,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + $data->other_fee + (($data->ppcc ?? 0) + ($data->rental_pole ?? 0)) * round(getNumberOfMonth($data->completed_time, $data->deadline)), 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                            if ($firstPaymentPeriod < $i) {
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        } elseif ($inPeriod6) {
                            if ($totalMonthFromCompleteDate < 7) {
                                if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($firstCompleteDate, 12),
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => $rentalPrice *  $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc,
                                        'pole_rental_fee'       => $data->rental_pole,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + (($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period) + $data->other_fee, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            } elseif ($totalMonthFromCompleteDate > 6) {
                                if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($firstCompleteDate, 12),
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => null,
                                        'ppcc'                  => null,
                                        'pole_rental_fee'       => null,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + $data->other_fee, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            }

                            if (12 < $i) {
                                if (12 < $i && 13 > $i) {
                                    $detailDate = addMonth($firstCompleteDate, 12);
                                }
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        } elseif ($inPeriod3) {
                            if ($totalMonthFromCompleteDate < 4) {
                                if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($firstCompleteDate, 12),
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => $rentalPrice * 9,
                                        'ppcc'                  => $data->ppcc,
                                        'pole_rental_fee'       => $data->rental_pole,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + (($rentalPrice + $data->ppcc + $data->rental_pole) * 9) + $data->other_fee, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            } elseif ($totalMonthFromCompleteDate > 3 && $totalMonthFromCompleteDate < 7) {
                                if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($firstCompleteDate, 12),
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => $rentalPrice * 6,
                                        'ppcc'                  => $data->ppcc,
                                        'pole_rental_fee'       => $data->rental_pole,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + (($rentalPrice + $data->ppcc + $data->rental_pole) * 6) + $data->other_fee, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            } elseif ($totalMonthFromCompleteDate > 6 && $totalMonthFromCompleteDate < 10) {
                                if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($firstCompleteDate, 12),
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => $rentalPrice * 3,
                                        'ppcc'                  => $data->ppcc,
                                        'pole_rental_fee'       => $data->rental_pole,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + (($rentalPrice + $data->ppcc + $data->rental_pole) * 6) + $data->other_fee, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            } elseif ($totalMonthFromCompleteDate > 9) {
                                if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($firstCompleteDate, 12),
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => null,
                                        'ppcc'                  => null,
                                        'pole_rental_fee'       => null,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + $data->other_fee, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            }
                            if (12 < $i) {
                                if (12 < $i && 13 > $i) {
                                    $detailDate = addMonth($firstCompleteDate, 12);
                                }
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        } elseif ($inAfterPeriod) {
                            if ($this->isSameMonth($data->relocation_date, $detailDate)) {
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => addMonth($detailDate, 1),
                                    'new_installation_fee'  => $data->new_installation_fee,
                                    'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                    'digging_fee'           => $data->digging_fee,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => $data->other_fee,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + ($rentalPrice + $data->ppcc + $data->rental_pole) + $data->other_fee, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            } else {
                                if ($detailDate > $data->relocation_date) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => addMonth($detailDate, 1),
                                        'new_installation_fee'  => null,
                                        'fiber_jumper_fee'      => null,
                                        'digging_fee'           => null,
                                        'rental_unit_price'     => $rentalPrice,
                                        'ppcc'                  => $data->ppcc,
                                        'pole_rental_fee'       => $data->rental_pole,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round(($rentalPrice + $data->ppcc + $data->rental_pole) + $data->other_fee, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            }
                        }
                        $detailDate = addMonth($detailDate, 1);
                        if (isset($fttxDetails) && $fttxDetails) {
                            if ($fttxDetails) {
                                $fttxDetailData = FttxDetail::create($fttxDetails);
                            }
                            $fttxDetails = [];
                        }
                    }
                } else {
                    $this->removeFttxDetail($data->completed_time, $data->work_order_isp, $data->work_order_cfocn, $data->subscriber_no, $data->deadline);
                    if ($data->status == 3) {
                        if ($data->dismantle_date > $data->deadline) {
                            $totalMonth =  $totalMonth + 1;
                        }
                    }
                    for ($i = 1; $i <=  $totalMonth; $i++) {
                        $rentalPrice = $this->getPrice($data->customer_id, $data->pos_speed_id, $detailDate, $data->rental_price, $data->first_payment_period);
                        $fttxDetails = [];
                        if ($i == 1) {
                            $dataFttx = Fttx::find($data->id);
                            if ($dataFttx) {
                                $dataFttx->update(['rental_price' => $rentalPrice]);
                            }
                        }
                        if ($data->first_payment_period == 12) {
                            if ($data->first_payment_period >= $i) {
                                if ($i == 1) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, $data->first_payment_period) : null,
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => $rentalPrice * $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc * $data->first_payment_period,
                                        'pole_rental_fee'       => $data->rental_pole * $data->first_payment_period,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => $data->discount,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + (($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period) + $data->other_fee + $data->discount, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            } else {
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        } elseif ($data->first_payment_period == 6) {
                            if ($i <= 12) {
                                if ($i == 1) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, $data->first_payment_period) : null,
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => $rentalPrice * $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc * $data->first_payment_period,
                                        'pole_rental_fee'       => $data->rental_pole * $data->first_payment_period,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => $data->discount,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + (($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period) + $data->other_fee + $data->discount, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                } elseif ($i == 7) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, $data->first_payment_period) : null,
                                        'new_installation_fee'  => null,
                                        'fiber_jumper_fee'      => null,
                                        'digging_fee'           => null,
                                        'rental_unit_price'     => $rentalPrice * $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc * $data->first_payment_period,
                                        'pole_rental_fee'       => $data->rental_pole * $data->first_payment_period,
                                        'other_fee'             => null,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round((($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period), 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            } else {
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        } elseif ($data->first_payment_period == 3) {
                            if ($i <= 12) {
                                if ($i == 1) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, $data->first_payment_period) : null,
                                        'new_installation_fee'  => $data->new_installation_fee,
                                        'fiber_jumper_fee'      => $data->fiber_jumper_fee,
                                        'digging_fee'           => $data->digging_fee,
                                        'rental_unit_price'     => $rentalPrice * $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc * $data->first_payment_period,
                                        'pole_rental_fee'       => $data->rental_pole * $data->first_payment_period,
                                        'other_fee'             => $data->other_fee,
                                        'discount'              => $data->discount,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round($data->new_installation_fee + $data->fiber_jumper_fee + $data->digging_fee + (($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period) + $data->other_fee + $data->discount, 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                } elseif ($i == 4) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, $data->first_payment_period) : null,
                                        'new_installation_fee'  => null,
                                        'fiber_jumper_fee'      => null,
                                        'digging_fee'           => null,
                                        'rental_unit_price'     => $rentalPrice * $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc * $data->first_payment_period,
                                        'pole_rental_fee'       => $data->rental_pole * $data->first_payment_period,
                                        'other_fee'             => null,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round((($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period), 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                } elseif ($i == 7) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, $data->first_payment_period) : null,
                                        'new_installation_fee'  => null,
                                        'fiber_jumper_fee'      => null,
                                        'digging_fee'           => null,
                                        'rental_unit_price'     => $rentalPrice * $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc * $data->first_payment_period,
                                        'pole_rental_fee'       => $data->rental_pole * $data->first_payment_period,
                                        'other_fee'             => null,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round((($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period), 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                } elseif ($i == 10) {
                                    $fttxDetails = [
                                        'fttx_id'               => $data->id,
                                        'customer_id'           => $data->customer_id,
                                        'date'                  => $detailDate,
                                        'expiry_date'           => $detailDate ? addMonth($detailDate, $data->first_payment_period) : null,
                                        'new_installation_fee'  => null,
                                        'fiber_jumper_fee'      => null,
                                        'digging_fee'           => null,
                                        'rental_unit_price'     => $rentalPrice * $data->first_payment_period,
                                        'ppcc'                  => $data->ppcc * $data->first_payment_period,
                                        'pole_rental_fee'       => $data->rental_pole * $data->first_payment_period,
                                        'other_fee'             => null,
                                        'discount'              => null,
                                        'remark'                => null,
                                        'invoice_number'        => null,
                                        'receipt_number'        => null,
                                        'total_amount'          => round((($rentalPrice + $data->ppcc + $data->rental_pole) * $data->first_payment_period), 2),
                                        'user_id'               => Auth::id(),
                                    ];
                                }
                            } else {
                                $fttxDetails = [
                                    'fttx_id'               => $data->id,
                                    'customer_id'           => $data->customer_id,
                                    'date'                  => $detailDate,
                                    'expiry_date'           => $detailDate ? addMonth($detailDate, 1) : null,
                                    'new_installation_fee'  => null,
                                    'fiber_jumper_fee'      => null,
                                    'digging_fee'           => null,
                                    'rental_unit_price'     => $rentalPrice,
                                    'ppcc'                  => $data->ppcc,
                                    'pole_rental_fee'       => $data->rental_pole,
                                    'other_fee'             => null,
                                    'discount'              => null,
                                    'remark'                => null,
                                    'invoice_number'        => null,
                                    'receipt_number'        => null,
                                    'total_amount'          => round($rentalPrice + $data->ppcc + $data->rental_pole, 2),
                                    'user_id'               => Auth::id(),
                                ];
                            }
                        }

                        $detailDate = addMonth($detailDate, 1);
                        if ($fttxDetails) {
                            $fttxDetailData = FttxDetail::create($fttxDetails);
                        }
                    }
                }
            }


            $dataFttx = Fttx::find($data->id);
            if ($dataFttx) {
                $dataFttx->update(['total' => Round($dataFttx->ppcc + $dataFttx->rental_pole + $dataFttx->rental_price, 2)]);
            }
        } catch (Exception $error) {
            dd($error);
            DB::rollback();
            return redirect()->back();
        }
    }
    public function onUpdateStatus(Request $request)
    {
        Log::info("Start: Admin/FttxController > onUpdateStatus | admin: " . auth()->id());
        DB::beginTransaction();

        try {
            $status = $request->status;
            Fttx::where('status', $status)
                ->where('status', '!=', 3)
                ->where('status', '!=', 2)
                ->update(['status' => 2]);

            DB::commit();
            return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (Exception $error) {
            DB::rollBack();
            Log::error("Error: Admin/FttxController > onUpdateStatus | message: " . $error->getMessage());
            return redirect()->back()->with('error', 'Failed to update status.');
        }
    }

    public function onDelete($id)
    {
        try {
            $fttx = Fttx::findOrFail($id);
            $fttx->fttxDetail()->forceDelete();
            $fttx->forceDelete();
            Session::flash('success', 'Deleted successfully!');
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function importExcel(ImportFttxRequest $request)
    {
        try {
            $fttxImport = new FttxImport();
            Excel::import($fttxImport, $request->file('fttx_file'));
            if ($fttxImport->message == 'invalid_column') {
                return response()->json([
                    'status' => 'danger',
                    'message' => 'File invalid column name!',
                ]);
            }
            if (count($fttxImport->validation) > 0) {
                $messageError = $fttxImport->validation;
                return response()->json([
                    'error' => true,
                    'status' => 'danger',
                    'message' => $messageError,
                ]);
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

    public function getPosSpeed($id)
    {
        $posSpeed = FttxPosSpeed::find($id);
        $data = [
            'rental_price' => $posSpeed->rental_price,
            'ppcc_price'   => $posSpeed->ppcc_price,
            'new_install_price' => $posSpeed->new_install_price,
        ];
        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }
    public function getFttxDetail(Request $request)
    {
        $query = FttxDetail::where('fttx_id', $request->id)
            ->orderBy('date', 'asc');
        $data['end_date'] = $request->end_date;
        $data['start_date'] = $request->start_date;
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$data['start_date'], $data['end_date']]);
        } else {
            $data['data'] = $query->get();
        }
        $data['data'] = $query->get();
        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error'
            ]);
        }
    }

    public function onDeleteFttxDetail(Request $request)
    {
        $id = $request->id;
        try {
            $fttxDetail = FttxDetail::findOrFail($id);
            $fttxDetail->delete();
            return response()->json(['status' => 'success', 'message' => 'deleted']);
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong!');
        }
        return back();
    }

    public function onStoreDetail(FttxDetailRequest $request)
    {
        Log::info("Start: Admin/FttxController > onStoreDetail | admin: " . $request);
        $items = [
            'fttx_id'               => $request->fttx_id,
            'customer_id'           => $request->customer_id,
            'date'                  => $request->date,
            'expiry_date'           => $request->expiry_date,
            'new_installation_fee'  => $request->new_installation_fee,
            'fiber_jumper_fee'      => $request->fiber_jumper_fee,
            'digging_fee'           => $request->digging_fee,
            'rental_unit_price'     => $request->rental_unit_price,
            'ppcc'                  => $request->ppcc,
            'pole_rental_fee'       => $request->pole_rental_fee,
            'other_fee'             => $request->other_fee,
            'discount'              => $request->discount,
            'remark'                => $request->remark,
            'invoice_number'        => $request->invoice_number,
            'receipt_number'        => $request->receipt_number,
            'total_amount'          => round($request->total_amount, 2),
            'user_id'               => Auth::id(),
        ];
        $id = $request->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = FttxDetail::find($id);
                $data->update($items);
                $status = "Update success.";
            } else {
                $data = FttxDetail::create($items);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/FttxController > onStoreDetail | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onRenewal(FttxRenewalRequest $request)
    {
        Log::info("Start: Admin/FttxController > onRenewal | admin: " . $request);
        $items = [
            'deadline'                                    => $request->new_deadline,
            'rental_price'                                => $request->rental_price,
            'user_id'                                     => Auth::id(),
        ];
        $id = $request->id;
        DB::beginTransaction();
        try {
            $status = "Create success.";
            if ($id) {
                $data = Fttx::find($id);
                $data->update($items);
                $this->renewalFttxDetail($data, $request->number_of_month);

                $status = "Success.";
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $status, 'data' => null]);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/FttxController > onRenewal | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function onRenewalAll($fttxExpire)
    {
        Log::info("Start: Admin/FttxController > onRenewal | admin: ");
        DB::beginTransaction();
        $status = 'Renewal success';
        try {
            foreach ($fttxExpire as $value) {
                $status = "Renewal success.";
                if ($value->id) {
                    $items = [
                        'deadline'                                    => addMonth($value->deadline, 1),
                        'user_id'                                     => Auth::id(),
                    ];
                    $data = Fttx::find($value->id);
                    $data->update($items);
                    $this->renewalFttxDetail($data, 1);
                }
            }
            DB::commit();
            return redirect()->back()->with('success',  $status);
        } catch (Exception $error) {
            DB::rollback();
            Session::flash('warning', 'Create unsuccessful!');
            Log::error("Error: Admin/FttxController > onRenewal | message: " . $error->getMessage());
            return redirect()->back();
        }
    }

    public function renewalFttxDetail($fttxData, $numberOfMonth)
    {
        try {
            $data = FttxDetail::where('fttx_id', $fttxData->id)
                ->orderBy('date', 'desc')
                ->first();
            $detailDate = addMonth($data->date, 1);
            $expiryDate = $data->expiry_date;
            for ($i = 1; $i <=  $numberOfMonth; $i++) {
                if ($i == 1) {
                    $fttxDetails = [
                        'fttx_id'               => $fttxData->id,
                        'customer_id'           => $fttxData->customer_id,
                        'date'                  => $detailDate,
                        'expiry_date'           => addMonth($expiryDate, $numberOfMonth),
                        'new_installation_fee'  => null,
                        'fiber_jumper_fee'      => null,
                        'digging_fee'           => null,
                        'rental_unit_price'     => $fttxData->rental_price * $numberOfMonth,
                        'ppcc'                  => $fttxData->ppcc,
                        'pole_rental_fee'       => $fttxData->rental_pole,
                        'other_fee'             => null,
                        'discount'              => null,
                        'remark'                => null,
                        'invoice_number'        => null,
                        'receipt_number'        => null,
                        'total_amount'          => round(($fttxData->rental_price * $numberOfMonth) + $fttxData->ppcc + $fttxData->rental_pole, 2),
                        'user_id'               => Auth::id(),
                    ];
                } else {
                    $fttxDetails = [
                        'fttx_id'               => $fttxData->id,
                        'customer_id'           => $fttxData->customer_id,
                        'date'                  => $detailDate,
                        'expiry_date'           => addMonth($expiryDate, $numberOfMonth),
                        'new_installation_fee'  => null,
                        'fiber_jumper_fee'      => null,
                        'digging_fee'           => null,
                        'rental_unit_price'     => null,
                        'ppcc'                  => null,
                        'pole_rental_fee'       => null,
                        'other_fee'             => null,
                        'discount'              => null,
                        'remark'                => null,
                        'invoice_number'        => null,
                        'receipt_number'        => null,
                        'total_amount'          => null,
                        'user_id'               => Auth::id(),
                    ];
                }
                $detailDate = addMonth($detailDate, 1);
                FttxDetail::create($fttxDetails);
            }
            $data = Fttx::find($fttxData->id);
            $data->update(['total' => $fttxDetails['total_amount']]);
        } catch (Exception $error) {
            DB::rollback();
        }
    }

    public function areArraysEqual($array1, $array2)
    {
        return empty(array_diff($array1, $array2)) && empty(array_diff($array2, $array1));
    }

    public function downloadTemplateUpload()
    {

        return Excel::download(new TemplateFttxUpload(null), 'fttx_template_upload' . '.xlsx');
    }

    public function getPrice($customerId, $posSpeedId, $payDate, $currentPrice, $firstPaymentPeriod)
    {
        try {
            if ($currentPrice) {
                return $currentPrice;
            } else {
                $customerPrice = FttxCustomerPrice::where('customer_id', $customerId)->first();
                if ($customerPrice) {
                    $posSpeed = $customerPrice ? json_decode($customerPrice->pos_speeds)[0] : [];
                    foreach ($posSpeed as $item) {
                        if ($item->pos_speed_id == $posSpeedId) {
                            $nextPrice = $this->getNextValidPrice($item->dataTable, $payDate);
                            if ($nextPrice !== null) {
                                return $nextPrice;
                            } else {
                                if ($item->rental_price !== null) {
                                    return $item->rental_price;
                                } else {
                                    return $currentPrice;
                                }
                            }
                        }
                    }
                } else {
                    $posSpeedPrice = FttxPosSpeed::find($posSpeedId);
                    if ($firstPaymentPeriod == 12) {
                        return $posSpeedPrice->priceByPosSpeed[0]->rental_price_twelve_month;
                    } else {
                        return $posSpeedPrice->priceByPosSpeed[0]->rental_price_six_month;
                    }
                }
                return 0;
            }
        } catch (Exception $error) {
            DB::rollback();
        }
    }
    public function getNextValidPrice($dataTable, $currentDate)
    {
        try {
            $dataTable = json_decode(json_encode($dataTable), true);

            usort($dataTable, function ($a, $b) {
                return strtotime($a['end_date']['value']) - strtotime($b['end_date']['value']);
            });

            foreach ($dataTable as $data) {
                if ($data['end_date']['value'] >= $currentDate) {
                    return $data['rental_price']['value'];
                }
            }

            return null;
        } catch (Exception $error) {
            dd($error);
            DB::rollback();
        }
    }

    public function removeFttxDetail($date, $ispNo = null, $cfocnNo = null, $subscribeNo = null)
    {
        try {
            $query = FttxDetail::where('date', '>=', $date)
                ->whereHas(
                    'fttx',
                    function ($q) use ($ispNo, $cfocnNo, $subscribeNo) {
                        $q->where('work_order_isp', $ispNo);
                        $q->orWhere('work_order_cfocn', $ispNo);
                        $q->orWhere('subscriber_no', $ispNo);
                    }
                );
            $query->delete();
        } catch (Exception $error) {
            dd($error);
            DB::rollback();
        }
    }

    public function getBiggestDate($reactiveDate, $changeSplitterDate, $relocationDate)
    {

        try {
            $timestamps = [];

            if ($reactiveDate) {
                $timestamps['reactive_date'] = strtotime($reactiveDate);
            }
            if ($changeSplitterDate) {
                $timestamps['change_splitter_date'] = strtotime($changeSplitterDate);
            }
            if ($relocationDate) {
                $timestamps['relocation_date'] = strtotime($relocationDate);
            }

            if (empty($timestamps)) {
                return null;
            }

            $maxKey = array_keys($timestamps, max($timestamps));
            return $maxKey[0];
        } catch (Exception $error) {
            dd($error);
            DB::rollback();
        }
    }

    public function getStatusCheck($deadline, $subscribeNo)
    {
        try {
            $allFttxs = Fttx::where('subscriber_no', $subscribeNo)->get();
            if ($allFttxs && $allFttxs->count() > 0) {
                foreach ($allFttxs as $item) {
                    if ($item->deadline <= $deadline) {
                        $data = Fttx::find($item->id);
                        $data->update(['check_status' => 'inactive']);
                    }
                }
            }

            $fttxs = Fttx::where('subscriber_no', $subscribeNo)->where('status', 3)->get();
            if ($fttxs && $fttxs->count() > 0) {
                foreach ($fttxs as $fttx) {
                    if ($fttx->deadline >= $deadline) {
                        return 'inactive';
                    }
                }
            }
            return 'active';
        } catch (Exception $error) {
            dd($error);
            DB::rollback();
        }
    }

    public function checkDataStoreOrNot($date, $dateType, $ispNo = null, $cfocnNo = null, $subscribeNo = null)
    {
        try {
            $fttx = Fttx::where(function ($q) use ($ispNo, $cfocnNo, $subscribeNo) {
                $q->where('work_order_isp', $ispNo)
                    ->orWhere('work_order_cfocn', $cfocnNo)
                    ->orWhere('subscriber_no', $subscribeNo);
            })->get();

            if ($fttx && $fttx->count() > 0) {
                if ($dateType == 'reactive_date') {
                    $checkSameDate = Fttx::where('reactive_date_check', $date)->first();
                } elseif ($dateType == 'change_splitter_date') {
                    $checkSameDate = Fttx::where('change_splitter_date_check', $date)->first();
                } elseif ($dateType == 'relocation_date') {
                    $checkSameDate = Fttx::where('relocation_date_check', $date)->first();
                }

                return $checkSameDate ? null : $date;
            }
            return $date;
        } catch (Exception $error) {
            dd($error);
            DB::rollback();
        }
    }

    function countMonthsIncludePartial($fromDate, $toDate)
    {
        if ($fromDate && $toDate) {
            $from = Carbon::parse($fromDate)->startOfMonth();
            $to = Carbon::parse($toDate)->startOfMonth();

            return $from->diffInMonths($to) + 1;
        } else {
            return 0;
        }
    }
    function isSameMonth($date1, $date2)
    {
        return date('Y-m', strtotime($date1)) === date('Y-m', strtotime($date2));
    }


    function adjustDayToMatch(Carbon|string $firstDate, Carbon|string $secondDate)
    {
        $first = Carbon::parse($firstDate);
        $second = Carbon::parse($secondDate);

        $first->day = $second->day;

        return $first->format('Y-m-d');
    }

    public function onSaveColumn(Request $req)
    {
        $column = $req->column ?? [0];

        FttxShowHideColumn::whereNotIn('id', $column)->update(['status' => 0]);
        FttxShowHideColumn::whereIn('id', $column)->update(['status' => 1]);

        return redirect()->back()->with('success', 'Columns updated successfully.');
    }
}
