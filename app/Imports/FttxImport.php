<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Fttx;
use App\Models\FttxCustomerPrice;
use App\Models\FttxDetail;
use App\Models\FttxPosSpeed;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

class FttxImport implements ToCollection, WithGroupedHeadingRow, WithCalculatedFormulas
{
    use Importable;

    public $message = null;
    public $work_order_cfocn = null;
    public $validation = [];
    public $arrColumnNames = [
        'customer_id',
        'work_order_isp',
        'work_order_cfocn',
        'subscriber_no',
        'isp_ex_work_order_isp',
        'status',
        'name',
        'phone',
        'address',
        'zone',
        'city',
        'port',
        'pos_speed_id',
        'applicant_team_install',
        'team_install',
        'create_time',
        'completed_time',
        'date_ex_complete_old_order',
        'dismantle_date',
        'dismantle_order_cfocn',
        'lay_fiber',
        'remark_first',
        'reactive_date',
        'reactive_payment_period',
        'change_splitter_date',
        'relocation_date',
        'start_payment_date',
        'last_payment_date',
        'initial_installation_order_complete_time',
        'first_relocation_order_complete_date',
        'payment_date',
        'payment_status',
        'deadline',
        'customer_type',
        'new_installation_fee',
        'fiber_jumper_fee',
        'digging_fee',
        'first_payment_period',
        'initial_payment_period',
        'rental_price',
        'ppcc',
        'number_of_pole',
        'rental_pole',
        'other_fee',
        'discount',
        'remark_second',
    ];
    public function collection(Collection $rowsExcel)
    {
        // Helper to safely convert Excel dates
        $excelDateToCarbon = fn($value) => is_numeric($value)
            ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d')
            : null;
        $rows = $rowsExcel->filter(function ($row) {
            $filtered = collect($row)->filter(function ($value) {
                return !is_null($value) && trim($value) !== '';
            });

            if ($filtered->count() === 1 && $filtered->has('customer_id')) {
                return false;
            }

            return $filtered->isNotEmpty();
        });
        foreach ($this->arrColumnNames as $column) {
            if (!array_key_exists($column, $rows->toArray()[0])) {
                $this->message = 'invalid_column';
                return;
            }
        }

        $duplicates = $rows->groupBy(function ($item) {
            return implode('|', [
                $item['work_order_isp'],
                $item['work_order_cfocn'],
                $item['subscriber_no'],
                $item['isp_ex_work_order_isp'],
                $this->checkStatus($item['status']),
                $this->checkPosSeed($item['pos_speed_id']),
                $item['deadline'],
            ]);
        })->filter(function ($group) {
            return $group->count() > 1;
        });

        $duplicateKeys = $duplicates->keys()->toArray();

        foreach ($rows as $key => $row) {
            if ($row->filter()->isEmpty()) {
                continue;
            }
            // 💥 Customer check added here
            $customer = Customer::where('customer_code', $row['customer_id'])->first();
            $customerId = $customer ? $customer->id : null;

            if (!$customerId) {
                $this->validation[] = [
                    'row_index' => $key + 2,
                    'duplicate_row' => '',
                    'work_order_cfocn_value' => $row['work_order_cfocn'] ? "Work Order CFOCN({$row['work_order_cfocn']})" : null,
                    'isp_ex_work_order_isp_value' => $row['isp_ex_work_order_isp'] ? "Isp ex work order isp ({$row['isp_ex_work_order_isp']})" : null,
                    'customer_id' => 'Customer ID (' . $row['customer_id'] . ') does not exist in the system',
                    'work_order_isp' => '',
                    'work_order_cfocn' => '',
                    'status' => '',
                    'pos_speed_id' => '',
                    'team_install' => '',
                    'completed_time' => '',
                    'deadline' => '',
                    'first_payment_period' => '',
                    'lay_fiber' => '',
                    'check_reactive_date_and_dismantle' => '',
                    'check_completed_time_and_start_payment_date' => '',
                    'dismantle_date' => '',
                    'reactive_date' => '',
                    'relocation_date' => '',
                    'subscriber_no' => '',
                    'fttx_data_validate_unique' => '',
                    'relocation_or_change_splitter_date',
                ];
                continue;
            }

            // continue validation logic
            $row['status'] = $this->checkStatus($row['status']);
            $row['pos_speed_id'] = $this->checkPosSeed($row['pos_speed_id']);
            $reactiveDate = $excelDateToCarbon($row['reactive_date']);
            $reactiveDateData = $reactiveDate ? $this->checkDataStoreOrNot($reactiveDate, 'reactive_date', $row['work_order_isp'], $row['work_order_cfocn'], $row['subscriber_no']) : null;

            $status = $row['status'];
            $isStatus2 = $status == 2;
            $isStatus3 = $status == 3;
            $isStatus4 = $status == 4;
            $isStatus5 = $status == 5;
            $isStatusIn = $status && in_array($status, [3, 4]);
            $reactivePeriod = $row['reactive_payment_period'] && in_array($row['reactive_payment_period'], [1, 3, 6, 12]);
            $firstPaymentPeriod = $row['first_payment_period'] && in_array($row['first_payment_period'], [3, 6, 12]);

            $duplicateKey = implode('|', [
                $row['work_order_isp'],
                $row['work_order_cfocn'],
                $row['subscriber_no'],
                $row['isp_ex_work_order_isp'],
                $row['status'],
                $row['pos_speed_id'],
                $row['deadline'],
            ]);
            //check unique
            $fttxDataValidate = Fttx::where('work_order_isp', $row['work_order_isp'])
                ->where('work_order_cfocn', $row['work_order_cfocn'])
                ->where('subscriber_no', $row['subscriber_no'])
                ->where('isp_ex_work_order_isp', $row['isp_ex_work_order_isp'])
                ->where('status', $row['status'])
                ->where('pos_speed_id', $row['pos_speed_id'])
                ->where('deadline', $excelDateToCarbon($row['deadline']))
                ->get();
            //check reactive or not
            $lastFttx = Fttx::where('subscriber_no', $row['subscriber_no'])
                ->orderBy('id', 'desc')
                ->first();
            $hasError =
                in_array($duplicateKey, $duplicateKeys) ||
                count($fttxDataValidate) > 0 ||
                !$row['customer_id'] ||
                (!$isStatus3 && !$row['work_order_isp']) ||
                (!$isStatusIn && !$row['first_payment_period']) ||
                !$firstPaymentPeriod ||
                (!$isStatus3 && !$row['work_order_cfocn']) ||
                ($isStatus3 && !$row['dismantle_date']) ||
                ($isStatus4 && !$row['reactive_date']) ||
                ($isStatus4 && !$row['reactive_payment_period']) ||
                ($row['reactive_date'] && !$row['reactive_payment_period']) ||
                ($isStatus4 && $row['reactive_payment_period'] && !$reactivePeriod)  ||
                ($isStatus2 && $row['reactive_date'] && $row['reactive_payment_period'] && !$reactivePeriod)  ||
                ($isStatus5 && !$row['relocation_date']) ||
                !$status || !$row['pos_speed_id'] || !$row['subscriber_no'] ||
                !$row['team_install'] || !$row['completed_time'] ||
                $row['lay_fiber'] === '' || !$row['deadline'] ||
                ($row['reactive_date'] && $row['dismantle_date'] && $row['reactive_date'] < $row['dismantle_date']) ||
                ($row['completed_time'] && $row['start_payment_date'] && $row['completed_time'] > $row['start_payment_date']) ||
                // ($row['status'] == 2 && $reactiveDateData && !$excelDateToCarbon($row['dismantle_date'])) ||
                // ($row['status'] == 4 && !$excelDateToCarbon($row['dismantle_date'])) ||
                ((($row['status'] == 2 && $row['date_ex_complete_old_order'] && !$row['dismantle_date'])) && !$row['change_splitter_date'] && !$row['relocation_date']) ||
                ($lastFttx && $lastFttx->status == 3 && $row['status'] == 2 && !$row['reactive_date'] && $row['subscriber_no'] == $lastFttx->subscriber_no) ||
                (isset($oldData) && $oldData && $oldData['status'] == 3 && $row['status'] == 2 && !$row['reactive_date'] && $row['subscriber_no'] == $oldData['subscriber_no']) ||
                ($lastFttx && $lastFttx->status == 2 && $row['status'] == 2 && !$row['change_splitter_date'] && $row['pos_speed_id'] != $lastFttx->pos_speed_id && $row['subscriber_no'] == $lastFttx->subscriber_no) ||
                (isset($oldData) && $oldData && $oldData['status'] == 2 && $row['status'] == 2 && !$row['change_splitter_date'] && $row['pos_speed_id'] != $oldData['pos_speed_id'] && $row['subscriber_no'] == $oldData['subscriber_no']);

            if ($hasError) {
                $this->validation[] = [
                    'row_index' => $key + 2,
                    'duplicate_row' => in_array($duplicateKey, $duplicateKeys) ? 'Duplicate record found in file,' : '',
                    'work_order_cfocn_value' => $row['work_order_cfocn'] ? "Work Order CFOCN({$row['work_order_cfocn']})" : null,
                    'isp_ex_work_order_isp_value' => $row['isp_ex_work_order_isp'] ? "Isp ex work order isp ({$row['isp_ex_work_order_isp']})" : null,
                    'customer_id' => !$row['customer_id'] ? 'Customer ID is required ,' : '',
                    'work_order_isp' => (!$isStatus3 && !$row['work_order_isp']) ? 'Work order isp is required ,' : '',
                    'work_order_cfocn' => (!$isStatus3 && !$row['work_order_cfocn']) ? 'Work order cfocn is required ,' : '',
                    'status' => !$status ? 'Status is required ,' : '',
                    'pos_speed_id' => !$row['pos_speed_id'] ? 'Pos speed is required ,' : '',
                    'team_install' => !$row['team_install'] ? 'Team install is required ,' : '',
                    'completed_time' => !$row['completed_time'] ? 'Completed time is required ,' : '',
                    'deadline' => !$row['deadline'] ? 'Deadline is required ,' : '',
                    'first_payment_period' => (!$isStatusIn && !$row['first_payment_period']) ? 'First payment period is required ,' : '',
                    'check_validate_first_payment_period' => !$firstPaymentPeriod ? 'First payment period must be 3 , 6 or 12' : '',
                    'lay_fiber' => $row['lay_fiber'] === '' ? 'Lay fiber is required ,' : '',
                    'check_reactive_date_and_dismantle' => ($row['reactive_date'] && $row['dismantle_date'] && $row['reactive_date'] < $row['dismantle_date'])
                        ? 'Reactive date can not be later than dismantle date,' : '',
                    'check_completed_time_and_start_payment_date' => ($row['completed_time'] && $row['start_payment_date'] && $row['completed_time'] > $row['start_payment_date'])
                        ? 'Complete date can not be later than start payment date,' : '',
                    'dismantle_date' => ($isStatus3 && !$row['dismantle_date']) ? 'Dismantle date is required ,' : '',
                    // 'dismantle_date' => ($isStatus3 && !$row['dismantle_date']) || ($row['status'] == 2 && $reactiveDateData && !$excelDateToCarbon($row['dismantle_date'])) || ($row['status'] == 4 && !$excelDateToCarbon($row['dismantle_date'])) ? 'Dismantle date is required ,' : '',
                    'reactive_date' => ($isStatus4 && !$row['reactive_date']) || ($lastFttx && $lastFttx->status == 3 && $row['status'] == 2 && !$row['reactive_date'] && $row['subscriber_no'] == $lastFttx->subscriber_no) ||  (isset($oldData) && $oldData && $oldData['status'] == 3 && $row['status'] == 2 && !$row['reactive_date'] && $row['subscriber_no'] == $oldData['subscriber_no']) ? 'Reactive date is required ,' : '',
                    'relocation_date' => ($isStatus5 && !$row['relocation_date']) ? 'Relocation  date is required ,' : '',
                    'subscriber_no' => !$row['subscriber_no'] ? 'Subscriber no is required ,' : '',
                    'fttx_data_validate_unique' => count($fttxDataValidate) > 0 ? 'Work order already in system' : '',
                    'relocation_or_change_splitter_date' => ((($row['status'] == 2 && $row['date_ex_complete_old_order'] && !$row['dismantle_date'])) && !$row['change_splitter_date'] && !$row['relocation_date'])   ? 'If Isp relocation please enter relocation_date , If Isp change splitter please enter change_splitter_date' : '',
                    'change_splitter_date' => ($lastFttx && $lastFttx->status == 2 && $row['status'] == 2 && !$row['change_splitter_date'] && $row['pos_speed_id'] != $lastFttx->pos_speed_id && $row['subscriber_no'] == $lastFttx->subscriber_no) || (isset($oldData) && $oldData && $oldData['status'] == 2 && $row['status'] == 2 && !$row['change_splitter_date'] && $row['pos_speed_id'] != $oldData['pos_speed_id'] && $row['subscriber_no'] == $oldData['subscriber_no']) ? 'Change splitter date is required ,' : '',
                    'reactive_payment_period' => ($isStatus4 && !$row['reactive_payment_period']) || ($row['reactive_date'] && !$row['reactive_payment_period']) ? 'Reactive payment period is required ,' : '',
                    'valid_reactive_payment_period' => ($isStatus4 && $row['reactive_payment_period'] && !$reactivePeriod) || ($isStatus2 && $row['reactive_date'] && $row['reactive_payment_period'] && !$reactivePeriod) ? 'Reactive payment period must be 1, 3 , 6 or 12' : '',
                ];
            }
            $oldData = $row;
        }

        if ($this->validation && count($this->validation) > 0) {
            return $this->validation;
        }

        foreach ($rows as $key => $row) {
            DB::beginTransaction();
            try {
                $customer = Customer::where('customer_code', $row['customer_id'])->first();
                $customerId = $customer ? $customer->id : null;

                if (!$customerId) {
                    $this->validation[] = [
                        'customer_id' => 'Customer ID (' . $row['customer_id'] . ') does not exist in the system',
                        'work_order_cfocn_value' => '',
                        'work_order_isp' => '',
                        'work_order_cfocn' => '',
                        'status' => '',
                        'pos_speed_id' => '',
                        'team_install' => '',
                        'completed_time' => '',
                        'deadline' => '',
                        'first_payment_period' => '',
                        'lay_fiber' => '',
                        'check_reactive_date_and_dismantle' => '',
                        'check_completed_time_and_start_payment_date' => '',
                        'dismantle_date' => '',
                        'relocation_date' => '',
                        'reactive_date' => '',
                        'subscriber_no' => '',
                    ];
                    return;
                }

                $startPaymentDate = $excelDateToCarbon($row['start_payment_date']);
                $completedTime = $excelDateToCarbon($row['completed_time']);
                $deadline = $excelDateToCarbon($row['deadline']);
                $reactiveDate = $excelDateToCarbon($row['reactive_date']);
                $changeSplitterDate = $excelDateToCarbon($row['change_splitter_date']);
                $relocationDate = $excelDateToCarbon($row['relocation_date']);

                $totalMonth = getNumberOfMonth($startPaymentDate ?? $completedTime, $deadline);

                $firstPaymentPeriod = $row['first_payment_period'] && $row['first_payment_period'] > 0 ? $row['first_payment_period'] : 0;
                $totalMonthCalculateAmount = $firstPaymentPeriod ?: $totalMonth;

                $totalAmount = floatval($row['new_installation_fee']) +
                    floatval($row['fiber_jumper_fee']) +
                    floatval($row['digging_fee']) +
                    (floatval($row['rental_price']) + floatval($row['ppcc']) + floatval($row['rental_pole'])) * $totalMonthCalculateAmount +
                    floatval($row['other_fee']) +
                    floatval($row['discount']);

                $reactiveDateCheck                = $reactiveDate ? $this->checkDataStoreOrNot($reactiveDate, 'reactive_date', $row['work_order_isp'], $row['work_order_cfocn'], $row['subscriber_no']) : null;
                $changeSplitterDateCheck          = $changeSplitterDate ? $this->checkDataStoreOrNot($changeSplitterDate, 'change_splitter_date', $row['work_order_isp'], $row['work_order_cfocn'], $row['subscriber_no']) : null;
                $relocationDateCheck              = $relocationDate ? $this->checkDataStoreOrNot($relocationDate, 'relocation_date', $row['work_order_isp'], $row['work_order_cfocn'], $row['subscriber_no']) : null;
                if ($reactiveDateCheck  ||  $changeSplitterDateCheck  ||  $relocationDateCheck) {
                    $getNewInstallPrice = $row['new_installation_fee'];
                } else {
                    $getNewInstallPrice = $this->getNewInstallPrice($row['lay_fiber'], $row['new_installation_fee'], $customerId);
                }

                $items = [
                    'customer_id'                                               => $customerId,
                    'work_order_isp'                                            => $row['work_order_isp'],
                    'work_order_cfocn'                                          => $row['work_order_cfocn'],
                    'subscriber_no'                                             => $row['subscriber_no'],
                    'isp_ex_work_order_isp'                                     => $row['isp_ex_work_order_isp'],
                    'status'                                                    => $row['status'],
                    'name'                                                      => $row['name'],
                    'phone'                                                     => $row['phone'],
                    'address'                                                   => $row['address'],
                    'zone'                                                      => $row['zone'],
                    'city'                                                      => $row['city'],
                    'port'                                                      => $row['port'],
                    'pos_speed_id'                                              => $row['pos_speed_id'],
                    'applicant_team_install'                                    => strtolower($row['applicant_team_install']),
                    'team_install'                                              => strtolower($row['team_install']),
                    'create_time'                                               => $excelDateToCarbon($row['create_time']),
                    'completed_time'                                            => $completedTime,
                    'date_ex_complete_old_order'                                => $excelDateToCarbon($row['date_ex_complete_old_order']),
                    'dismantle_date'                                            => $excelDateToCarbon($row['dismantle_date']),
                    'dismantle_order_cfocn'                                     => $row['dismantle_order_cfocn'],
                    'lay_fiber'                                                 => $row['lay_fiber'],
                    'remark_first'                                              => $row['remark_first'],
                    'reactive_date'                                             => $reactiveDate,
                    'reactive_payment_period'                                   => $row['reactive_payment_period'],
                    'change_splitter_date'                                      => $changeSplitterDate,
                    'relocation_date'                                           => $relocationDate,
                    'start_payment_date'                                        => $startPaymentDate,
                    'last_payment_date'                                         => $excelDateToCarbon($row['last_payment_date']),
                    'initial_installation_order_complete_time'                  => $excelDateToCarbon($row['initial_installation_order_complete_time']),
                    'first_relocation_order_complete_date'                      => $excelDateToCarbon($row['first_relocation_order_complete_date']),
                    'payment_date'                                              => $excelDateToCarbon($row['payment_date']),
                    'payment_status'                                            => $row['payment_status'],
                    'deadline'                                                  => $deadline,
                    'customer_type'                                             => $row['customer_type'],
                    'new_installation_fee'                                      => $getNewInstallPrice,
                    'fiber_jumper_fee'                                          => $row['fiber_jumper_fee'],
                    'digging_fee'                                               => $row['digging_fee'],
                    'first_payment_period'                                      => $row['first_payment_period'],
                    'initial_payment_period'                                    => $row['initial_payment_period'],
                    'rental_price'                                              => $row['rental_price'],
                    'ppcc'                                                      => $row['ppcc'],
                    'number_of_pole'                                            => $row['number_of_pole'],
                    'rental_pole'                                               => $row['rental_pole'],
                    'other_fee'                                                 => $row['other_fee'],
                    'discount'                                                  => $row['discount'],
                    'total'                                                     => $totalAmount,
                    'remark_second'                                             => $row['remark_second'],
                    'check_status'                                              => $this->getStatusCheck($deadline, $row['subscriber_no']),
                    'reactive_date_check'                                       => $reactiveDateCheck,
                    'change_splitter_date_check'                                => $changeSplitterDateCheck,
                    'relocation_date_check'                                     => $relocationDateCheck,
                    'user_id'                                                   => Auth::id(),
                ];

                $fttx = Fttx::create($items);
                $this->createFttxDetail($fttx);
                DB::commit();
            } catch (Exception $error) {
                DB::rollback();
                Log::error('Fttx import failed', ['row' => $key, 'error' => $error->getMessage()]);
            }
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
    public function checkStatus($sentence)
    {
        if ($sentence) {
            $sentence = strtolower($sentence);
            $newInstall     = 'install';
            $active         = 'active';
            $dismantle      = 'dismantle';
            $reactive       = 'reactive';
            $relocation     = 'relocation';
            $replace        = 'replace';
            $chargeOnlyNew  = 'charge';

            if ($this->searchWord($sentence, strtolower($newInstall))) {
                return 1;
            } elseif ($this->searchWord($sentence, $active)) {
                return 2;
            } elseif ($this->searchWord($sentence, $dismantle)) {
                return 3;
            } elseif ($this->searchWord($sentence, $reactive)) {
                return 4;
            } elseif ($this->searchWord($sentence, $relocation)) {
                return 5;
            } elseif ($this->searchWord($sentence, $replace)) {
                return 6;
            } elseif ($this->searchWord($sentence, $chargeOnlyNew)) {
                return 7;
            }
        } else {
            return null;
        }
    }
    public function searchWord($sentence, $word)
    {
        if (preg_match('/\b' . preg_quote($word, '/') . '\b/', $sentence)) {
            return true;
        } else {
            return false;
        }
    }
    public function checkPosSeed($sentence)
    {
        $posSpeed = FttxPosSpeed::where('status', 1)->get();
        if ($sentence) {
            $sentence = strtolower($sentence);
            foreach ($posSpeed as $value) {
                if (str_contains($sentence, $value->key_search_import)) {
                    return $value->id;
                }
            }
        } else {
            return null;
        }
    }
    public function getPrice($customerId, $posSpeedId, $payDate, $currentPrice, $firstPaymentPeriod)
    {
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
    }
    public function getNextValidPrice($dataTable, $currentDate)
    {
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
    }
    public function getNewInstallPrice($layFiber, $newInstallPrice, $customerId)
    {
        try {
            if ($newInstallPrice && $newInstallPrice > 0) {
                return $newInstallPrice;
            } else {
                $customerPrice = FttxCustomerPrice::where('customer_id', $customerId)->first();
                if (!$customerPrice) {
                    foreach (config('dummy.fttx_new_install_price') as $value) {
                        if ($layFiber >= $value['start_unit'] && (!empty($value['end_unit']) ? $layFiber <= $value['end_unit'] : true)) {
                            if ($layFiber > 800) {
                                return round(($value['price_over_calculate'] * ($layFiber - $value['end_unit_third_level'])) + $value['price'], 2);
                            } else {
                                return $value['price'];
                            }
                        }
                    }
                    return $newInstallPrice ?? 0;
                } else {
                    $newInstallPriceList = json_decode($customerPrice->new_install_price);
                    if ($layFiber >= 0 && $layFiber <= 350) {
                        return $newInstallPriceList->first;
                    } elseif ($layFiber >= 351 && $layFiber <= 500) {
                        return $newInstallPriceList->second;
                    } elseif ($layFiber >= 501 && $layFiber <= 800) {
                        return $newInstallPriceList->third;
                    } elseif ($layFiber > 800) {
                        return round(($newInstallPriceList->fourth * ($layFiber - 800)) + $newInstallPriceList->third, 2);
                    }
                    return $newInstallPrice ?? 0;
                }
            }
        } catch (Exception $error) {
        }
    }
    public function getStatusCheck($deadline, $subscribeNo)
    {
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
    }
    public function removeFttxDetail($date, $ispNo = null, $cfocnNo = null, $subscribeNo = null, $deadline = null)
    {
        try {
            $query = FttxDetail::where('date', '>=', $date)
                ->whereHas(
                    'fttx',
                    function ($q) use ($ispNo, $cfocnNo, $subscribeNo, $deadline) {
                        $q->where('work_order_isp', $ispNo);
                        $q->where('deadline', '<', $deadline);
                        $q->orWhere('work_order_cfocn', $cfocnNo);
                        $q->orWhere('subscriber_no', $subscribeNo);
                    }
                );
            $query->delete();
        } catch (Exception $e) {
            DB::rollback();
        }
    }
    public function checkDataStoreOrNot($date, $dateType, $ispNo = null, $cfocnNo = null, $subscribeNo = null)
    {
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
    }
    function getBiggestDate($reactiveDate, $changeSplitterDate, $relocationDate): ?string
    {
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
}
