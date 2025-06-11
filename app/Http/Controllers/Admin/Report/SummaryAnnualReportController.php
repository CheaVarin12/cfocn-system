<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\InvoiceDetailAnnualSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Type;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoiceReceiptAnnualSummaryExport;
use App\Models\ChildCreditNote;
use App\Models\ChildInvoice;
use App\Models\CreditNote;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\WorkOrderCreditNote;
use App\Models\WorkOrderInvoice;
use App\Models\WorkOrderReceipt;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SummaryAnnualReportController extends Controller
{
    protected $layout = 'admin::pages.report.summary-annual.';
    public function __construct()
    {
        $this->middleware('permission:invoice-receipt-report', ['only' => ['invoiceReceiptIndex']]);
        $this->middleware('permission:invoice-detail-report', ['only' => ['invoiceDetailIndex']]);
    }

    public function invoiceReceiptIndex(Request $req)
    {
        $year = $req->year ?? now()->year;
        $shortYear = Carbon::createFromFormat('Y', $year)->format('y');

        $serviceTypes = Type::with('purchase', 'purchase.customer')
            ->when(request('service_type'), function ($q) {
                $q->where('id', request('service_type'));
            })
            ->where('status', 1)->get();
        $invoiceReceiptData = [];
        foreach ($serviceTypes as $serviceType) {
            $totalAmountInvoiceEachServiceTypeByMonth = collect([
                'Jan' => 0,
                'Feb' => 0,
                'Mar' => 0,
                'Apr' => 0,
                'May' => 0,
                'Jun' => 0,
                'Jul' => 0,
                'Aug' => 0,
                'Sep' => 0,
                'Oct' => 0,
                'Nov' => 0,
                'Dec' => 0,
                'Total' => 0,
            ]);
            $totalAmountReceiptEachServiceTypeByMonth = collect([
                'Jan' => 0,
                'Feb' => 0,
                'Mar' => 0,
                'Apr' => 0,
                'May' => 0,
                'Jun' => 0,
                'Jul' => 0,
                'Aug' => 0,
                'Sep' => 0,
                'Oct' => 0,
                'Nov' => 0,
                'Dec' => 0,
                'Total' => 0,
            ]);
            $serviceTypeName = $serviceType->name;
            $invoiceReceiptData[$serviceTypeName]['customer'] = [];
            $invoiceReceiptData[$serviceTypeName]['total_amount_invoice'] = [];
            $invoiceReceiptData[$serviceTypeName]['total_amount_receipt'] = [];
            if ($serviceType->id != 8) {
                foreach ($serviceType->purchase as $purchase) {
                    if ($purchase->customer) {
                        $invoiceReceiptData[$serviceTypeName]['customer'][] = [
                            'id' => $purchase->customer->id,
                            'name_en' => $purchase->customer->name_en,
                            'name_kh' => $purchase->customer->name_kh,
                            'service_type_id' => $serviceType->id,
                        ];
                    }
                }
            } else {
                foreach ($serviceType->order as $order) {
                    if ($order->customer) {
                        $invoiceReceiptData[$serviceTypeName]['customer'][] = [
                            'id' => $order->customer->id,
                            'name_en' => $order->customer->name_en,
                            'name_kh' => $order->customer->name_kh,
                            'service_type_id' => $serviceType->id,
                        ];
                    }
                }
            }


            $uniqueCustomers = [];
            foreach ($invoiceReceiptData[$serviceTypeName]['customer'] as $customer) {
                $uniqueCustomers[$customer['id']] = $customer;
            }
            $invoiceReceiptData[$serviceTypeName]['customer'] = array_values($uniqueCustomers);

            // Add the new column
            foreach ($invoiceReceiptData[$serviceTypeName]['customer'] as &$item) {
                $item['invoice'] = $this->invoiceAmountCustomerByYear($item['id'], $item['service_type_id'], $year);

                foreach ($totalAmountInvoiceEachServiceTypeByMonth as $key => $value) {
                    $totalAmountInvoiceEachServiceTypeByMonth[$key] += $item['invoice'][$key];
                }

                $item['receipt'] = $this->receiptAmountCustomerByYear($item['id'], $item['service_type_id'], $year);
                foreach ($totalAmountReceiptEachServiceTypeByMonth as $key => $value) {
                    $totalAmountReceiptEachServiceTypeByMonth[$key] += $item['receipt'][$key];
                }
            }
            unset($item);
            $invoiceReceiptData[$serviceTypeName]['total_amount_invoice'] = $totalAmountInvoiceEachServiceTypeByMonth;
            $invoiceReceiptData[$serviceTypeName]['total_amount_receipt'] = $totalAmountReceiptEachServiceTypeByMonth;
        }

        $data['data'] = $invoiceReceiptData;

        $totalAllInvoiceByMonth = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
            'Total' => 0,
        ]);
        $totalAllReceiptByMonth  = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
            'Total' => 0,
        ]);

        foreach ($data['data'] as $item) {
            foreach ($item['total_amount_invoice'] as $key => $value) {
                $totalAllInvoiceByMonth[$key] += $value;
            }
            foreach ($item['total_amount_receipt'] as $key => $value) {
                $totalAllReceiptByMonth[$key] += $value;
            }
        }

        $data['totalAllInvoiceByMonth'] = $totalAllInvoiceByMonth;
        $data['totalAllReceiptByMonth'] = $totalAllReceiptByMonth;
        $data['shortYear'] = $shortYear;
        $data['serviceTypes'] = Type::where('status', 1)->get();

        if ($req->check == "export") {
            return Excel::download(new InvoiceReceiptAnnualSummaryExport($data), 'invoice-receipt-summary' . $year . '.xlsx');
        }
        return view($this->layout . 'invoice-receipt.index', $data);
    }


    public function invoiceAmountCustomerByYear($customerId, $serviceTypeId, $year)
    {
        $allMonths = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ]);

        if ($serviceTypeId != 8) {
            $invoices = Invoice::where('customer_id', $customerId)
                ->whereYear('issue_date', $year)
                ->whereHas('purchase', function ($query) use ($serviceTypeId) {
                    $query->where('type_id', $serviceTypeId);
                })
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand) as total')
                ->groupBy('month')
                ->get();

            $creditNote = CreditNote::where('customer_id', $customerId)
                ->whereYear('issue_date', $year)
                ->where('status', 1)
                ->whereHas('purchase', function ($query) use ($serviceTypeId) {
                    $query->where('type_id', $serviceTypeId);
                })
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand) * -1 as total')
                ->groupBy('month')
                ->get();
        } else {
            $invoices = WorkOrderInvoice::where('customer_id', $customerId)
                ->whereYear('issue_date', $year)
                ->whereHas('order', function ($query) use ($serviceTypeId) {
                    $query->where('type_id', $serviceTypeId);
                })
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand) as total')
                ->groupBy('month')
                ->get();

            $creditNote = WorkOrderCreditNote::where('customer_id', $customerId)
                ->whereYear('issue_date', $year)
                ->where('status', 1)
                ->whereHas('order', function ($query) use ($serviceTypeId) {
                    $query->where('type_id', $serviceTypeId);
                })
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand) * -1 as total')
                ->groupBy('month')
                ->get();
        }

        $invoiceTotals = $invoices->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $creditNoteTotals = $creditNote->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $monthlyTotals = $invoiceTotals->mergeRecursive($creditNoteTotals)->map(function ($values) {
            return is_array($values) ? array_sum($values) : $values;
        });

        $result = $allMonths->merge($monthlyTotals);

        $totalSum = $result->sum();
        $result->put('Total', $totalSum);

        return $result;
    }


    public function receiptAmountCustomerByYear($customerId, $serviceTypeId, $year)
    {
        $allMonths = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ]);

        if ($serviceTypeId != 8) {
            $receipts = Receipt::where('customer_id', $customerId)
                ->whereYear('paid_date', $year)
                ->where('status', 2)
                ->where(function ($query) use ($serviceTypeId) {
                    $query->whereNotNull('type_id')
                        ->where('type_id', $serviceTypeId)
                        ->orWhere(function ($query) use ($serviceTypeId) {
                            $query->whereNull('type_id')
                                ->whereHas('invoices.purchase', function ($query) use ($serviceTypeId) {
                                    $query->where('type_id', $serviceTypeId);
                                });
                        });
                })
                ->selectRaw('MONTH(paid_date) as month, SUM(total_grand) as total')
                ->groupBy('month')
                ->get();
        } else {
            $receipts = WorkOrderReceipt::where('customer_id', $customerId)
                ->whereYear('paid_date', $year)
                ->where('status', 2)
                ->selectRaw('MONTH(paid_date) as month, SUM(total_grand) as total')
                ->groupBy('month')
                ->get();
        }

        $monthlyTotals = $receipts->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $result = $allMonths->merge($monthlyTotals);

        $totalSum = $result->sum();
        $result->put('Total', $totalSum);
        return $result;
    }

    public function invoiceDetailIndex(Request $req)
    {

        $year = $req->year ?? now()->year;
        $shortYear = Carbon::createFromFormat('Y', $year)->format('y');

        $pac = collect(Purchase::orderBy('id', 'desc')->get());
        $order = collect(Order::orderBy('id', 'desc')->get());

        $mergedData = $pac->merge($order);
        $sortedData = $mergedData->sortByDesc('id');
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $currentPageItems = $sortedData->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $data['data'] = new LengthAwarePaginator(
            $currentPageItems,
            $sortedData->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        foreach ($data['data'] as $purchase) {
            if ($purchase->childInvoice && count($purchase->childInvoice) > 0) {
                $purchase->totalInvoice = $this->getTotalInvoiceFromChildInvoiceByYear($purchase->id, $year);
            } else {
                $purchase->totalInvoice = $this->getTotalInvoiceByYear($purchase->id, $year, $purchase->order_number, $purchase->po_number);
            }
            if ($purchase->order_number) {
                $purchase->total_unit_price = $purchase?->orderDetail->sum('price') ?? 0;
            } else {
                $purchase->total_unit_price = $purchase?->purchaseDetail->sum('price') ?? 0;
            }
        }
        $data['shortYear'] = $shortYear;
        $data['totalAmountOfInvoiceDetail'] = $this->getTotalAmountOfInvoiceDetail($year)['total'];
        if ($req->check == "export") {

            $data['data'] = $this->getTotalAmountOfInvoiceDetail($year)['data'];
            foreach ($data['data'] as $purchase) {
                if ($purchase->childInvoice && count($purchase->childInvoice) > 0) {
                    $purchase->totalInvoice = $this->getTotalInvoiceFromChildInvoiceByYear($purchase->id, $year);
                } else {
                    $purchase->totalInvoice = $this->getTotalInvoiceByYear($purchase->id, $year, $purchase->order_number, $purchase->po_number);
                }
                if ($purchase->order_number) {
                    $purchase->total_unit_price = $purchase?->orderDetail->sum('price') ?? 0;
                } else {
                    $purchase->total_unit_price = $purchase?->purchaseDetail->sum('price') ?? 0;
                }
            }

            return Excel::download(new InvoiceDetailAnnualSummaryExport($data), 'invoice-detail' . $year . '.xlsx');
        }
        return view($this->layout . 'invoice-detail.index', $data);
    }

    public function getTotalInvoiceFromChildInvoiceByYear($purchaseID, $year)
    {
        $allMonths = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ]);

        $invoices = ChildInvoice::where('purchase_id', $purchaseID)->whereYear('issue_date', $year)
            ->selectRaw('MONTH(issue_date) as month, SUM(grand_total) as total')
            ->groupBy('month')
            ->get();

        $creditNote = ChildCreditNote::where('purchase_id', $purchaseID)->whereYear('issue_date', $year)
            ->selectRaw('MONTH(issue_date) as month, SUM(grand_total)* -1 as total')
            ->groupBy('month')
            ->get();


        $invoiceTotals = $invoices->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $creditNoteTotals = $creditNote->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $monthlyTotals = $invoiceTotals->mergeRecursive($creditNoteTotals)->map(function ($values) {
            return is_array($values) ? array_sum($values) : $values;
        });
        $result = $allMonths->merge($monthlyTotals);

        $totalSum = $result->sum();
        $result->put('Total', $totalSum);
        return $result;
    }

    public function getTotalInvoiceByYear($purchaseID, $year, $orderNumber, $poNumber)
    {
        $allMonths = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ]);

        if ($orderNumber) {
            $invoices = WorkOrderInvoice::where('order_id', $purchaseID)->whereYear('issue_date', $year)
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand) as total')
                ->groupBy('month')
                ->get();

            $creditNote = WorkOrderCreditNote::where('order_id', $purchaseID)->whereYear('issue_date', $year)
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand)* -1 as total')
                ->groupBy('month')
                ->get();
        } else {
            $invoices = Invoice::where('po_id', $purchaseID)->whereYear('issue_date', $year)
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand) as total')
                ->groupBy('month')
                ->get();

            $creditNote = CreditNote::where('po_id', $purchaseID)->whereYear('issue_date', $year)
                ->selectRaw('MONTH(issue_date) as month, SUM(total_grand)* -1 as total')
                ->groupBy('month')
                ->get();
        }

        $invoiceTotals = $invoices->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $creditNoteTotals = $creditNote->mapWithKeys(function ($item) {
            $monthName = date('M', mktime(0, 0, 0, $item->month, 10));
            return [$monthName => (float) $item->total];
        });

        $monthlyTotals = $invoiceTotals->mergeRecursive($creditNoteTotals)->map(function ($values) {
            return is_array($values) ? array_sum($values) : $values;
        });

        $result = $allMonths->merge($monthlyTotals);

        $totalSum = $result->sum();
        $result->put('Total', $totalSum);
        return $result;
    }

    public function getTotalAmountOfInvoiceDetail($year)
    {
        $totalAmountOfInvoiceDetailByMonth = collect([
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
            'Total' => 0,
        ]);
        $pac = collect(Purchase::orderBy('id', 'desc')->get());
        $order = collect(Order::orderBy('id', 'desc')->get());

        $data = $pac->merge($order);
        foreach ($data as $purchase) {
            if ($purchase->childInvoice && count($purchase->childInvoice) > 0) {
                $purchase->totalInvoice = $this->getTotalInvoiceFromChildInvoiceByYear($purchase->id, $year);
            } else {
                $purchase->totalInvoice = $this->getTotalInvoiceByYear($purchase->id, $year, $purchase->order_number, $purchase->po_number);
            }
        }
        foreach ($data as $item) {;
            foreach ($totalAmountOfInvoiceDetailByMonth as $key => $value) {
                $totalAmountOfInvoiceDetailByMonth[$key] += $item->totalInvoice[$key];
            }
        }
        return [
            'data' => $data,
            'total' => $totalAmountOfInvoiceDetailByMonth
        ];
    }
}
