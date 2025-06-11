<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceScheduleService
{
    //invoiceOnlySaleInstall
    public function getDataSaleInstall()
    {
        return Purchase::where('type_id', 2)->whereHas('invoiceHasOneSchedule', function ($q) {
            $q->whereRaw('charge_number > install_number');
        })->with(['invoiceHasOneSchedule' => function ($q) {
            $q->whereRaw('charge_number > install_number');
        }])->orderBy('created_at', 'desc')->select('id', 'type_id', 'po_number')->get();
    }

    public function createInvoiceBySaleInstall()
    {
        $currentMonthGetDate = Carbon::now()->format('Y-m-d');
        $currentEndOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        DB::beginTransaction();
        try {
            $data = $this->getDataSaleInstall();
            foreach ($data as $item) {
                if (isset($item->invoiceHasOneSchedule) && $item->invoiceHasOneSchedule) {
                    $invoice = $item->invoiceHasOneSchedule;
                    $installNumber = (!$invoice->install_number || $invoice->install_number <= 0) ? 1 : $invoice->install_number;

                    if ($invoice->charge_number > $installNumber) {

                        $invoiceItem = [
                            'invoice_number' => null,
                            'po_id' => $item->id,
                            'customer_id' => $invoice->customer_id,
                            'total_price' => $invoice->total_price,
                            'vat' => $invoice->vat,
                            'total_grand' => $invoice->total_grand,
                            'total_qty' => $invoice->total_qty,
                            'charge_number' => $invoice->charge_number,
                            'charge_type' => $invoice->charge_type,
                            'install_number' => $installNumber +  1,
                            'paid_status' => 'Pending',
                            'issue_date' => $currentMonthGetDate,
                            'exchange_rate' => $invoice->exchange_rate,
                            'invoice_period' => $invoice->invoice_period,
                            'period_start' => $currentMonthGetDate,
                            'period_end' => $currentEndOfMonth,
                            'note' => $invoice->note,
                            'status' => 5,
                        ];
                        $dataInvoice = Invoice::create($invoiceItem);
                        foreach ($invoice->invoiceDetail as $detail) {
                            $invoiceDetail = [
                                "invoice_id" => $dataInvoice->id,
                                "service_id" => $detail->service_id,
                                "des" => $detail->des,
                                "qty" => $detail->qty,
                                "price" => $detail->price,
                                "uom" => $detail->uom,
                                "rate_first" => $detail->rate_first,
                                "rate_second" => $detail->rate_second,
                                "amount" => $detail->amount,
                            ];
                            InvoiceDetail::create($invoiceDetail);
                        }
                    }
                }
            }
            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    //endInvoiceOnlySaleInstall

    public function getPeriodEnd()
    {
        $fromDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        $selectField = [
            'id',
            'po_id',
            'customer_id',
            'total_price',
            'vat',
            'total_grand',
            'total_qty',
            'charge_number',
            'charge_type',
            'install_number',
            'exchange_rate',
            'invoice_period',
            'period_end',
            'note',
            'status'
        ];
        return Invoice::where(function ($q) use ($fromDate, $endDate) {
            $q->whereBetween(DB::raw('date(period_end)'), [$fromDate, $endDate]);
            $q->where('status', '!=', 5);
        })->orderBy('period_end', 'asc')->select($selectField)->get();
    }

    public function createInvoiceByPeriodEnd()
    {
        $currentMonthGetDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentEndOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        DB::beginTransaction();
        try {
            $data = $this->getPeriodEnd();
            foreach ($data as $item) {
                $chargeNumber = $item->charge_number ?? null;
                $installNumber = (!$item->install_number || $item->install_number <= 0) ? null : $item->install_number + 1;
                $invoiceItem = [
                    'invoice_number' => null,
                    'po_id' => $item->po_id,
                    'customer_id' => $item->customer_id,
                    'total_price' => $item->total_price,
                    'vat' => $item->vat,
                    'total_grand' => $item->total_grand,
                    'total_qty' => $item->total_qty,
                    'charge_number' => $chargeNumber,
                    'charge_type' => $item->charge_type,
                    'install_number' => $chargeNumber ? $installNumber : null,
                    'paid_status' => 'Pending',
                    'issue_date' => $currentMonthGetDate,
                    'exchange_rate' => $item->exchange_rate,
                    'invoice_period' => $item->invoice_period,
                    'period_start' => $currentMonthGetDate,
                    'period_end' => $currentEndOfMonth,
                    'note' => $item->note,
                    'status' => 5,
                ];
                $dataInvoice = Invoice::create($invoiceItem);
                foreach ($item->invoiceDetail as $detail) {
                    $invoiceDetail = [
                        "invoice_id" => $dataInvoice->id,
                        "service_id" => $detail->service_id,
                        "des" => $detail->des,
                        "qty" => $detail->qty,
                        "price" => $detail->price,
                        "uom" => $detail->uom,
                        "rate_first" => $detail->rate_first,
                        "rate_second" => $detail->rate_second,
                        "amount" => $detail->amount,
                    ];
                    InvoiceDetail::create($invoiceDetail);
                }
            }
            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
