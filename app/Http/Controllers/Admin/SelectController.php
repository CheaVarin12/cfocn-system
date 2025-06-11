<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Fttx;
use App\Models\FttxCustomerPrice;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Purchase;
use App\Models\Type;
use App\Models\WorkOrderCreditNote;
use App\Models\WorkOrderInvoice;
use Illuminate\Database\Eloquent\Builder;

class SelectController extends Controller
{

    public function selectCustomer(Request $req)
    {
        $data = Customer::where(function (Builder $q) use ($req) {
            if ($req->search) {
                $q->where('name_en', 'LIKE', '%' . $req->search . '%');
                $q->orWhere('name_kh', 'LIKE', '%' . $req->search . '%');
                $q->orWhere('phone', 'LIKE', '%' . $req->search . '%');
            }
        })->orderBy('created_at', 'desc')->take(12)->get();

        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }
    public function selectProject(Request $req)
    {
        $data = Project::where('status', 1)->where(function (Builder $q) use ($req) {
            if ($req->search) {
                $q->where('name', 'LIKE', '%' . $req->search . '%');
                $q->orWhere('phone', 'LIKE', '%' . $req->search . '%');
            }
        })->orderBy('created_at', 'desc')->take(12)->get();

        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }
    public function selectType(Request $req)
    {
        $data = Type::where('status', 1)->where(function (Builder $q) use ($req) {
            if ($req->search) {
                $q->where('name', 'LIKE', '%' . $req->search . '%');
            }
        })->orderBy('created_at', 'asc')->take(12)->get();

        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }
    public function selectInvoice(Request $req)
    {
        $data = Invoice::withTrashed()->with(["purchase" => function ($q) {
            $q->with(["project"]);
        }])->where(function (Builder $q) use ($req) {
            if ($req->search) {
                $q->where('invoice_number', 'LIKE', '%' . $req->search . '%');
            }
        })->orderBy('created_at', 'asc')->take(12)->get();
        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }

    public function selectCreditNote(Request $req)
    {
        try {
            $data = CreditNote::with('purchase.project')
                ->where(function (Builder $q) use ($req) {
                    if ($req->search) {
                        $q->where('credit_note_number', 'LIKE', '%' . $req->search . '%');
                    }
                })->orderBy('created_at', 'asc')->take(12)->get();
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error' . $e->getMessage()
            ]);
        }
    }

    public function selectWorkOrderInvoice(Request $req)
    {
        $data = WorkOrderInvoice::withTrashed()->with('order.project')
            ->where(function (Builder $q) use ($req) {
                if ($req->search) {
                    $q->where('invoice_number', 'LIKE', '%' . $req->search . '%');
                }
            })
            ->orderBy('created_at', 'desc')
            ->take(12)->get();
        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }

    public function selectWorkOrderCreditNote(Request $req)
    {
        $data = WorkOrderCreditNote::with('order.project')
            ->where(function (Builder $q) use ($req) {
                if ($req->search) {
                    $q->where('credit_note_number', 'LIKE', '%' . $req->search . '%');
                }
            })
            ->orderBy('created_at', 'desc')
            ->take(12)->get();
        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }

    public function selectPAC(Request $req)
    {
        $data = Purchase::with('project', 'customer', 'type', 'purchaseDetail', 'purchaseDetail.service')
            ->where(function (Builder $q) use ($req) {
                if ($req->search) {
                    $q->where('pac_number', 'LIKE', '%' . $req->search . '%');
                }
            })->where('status', 1)
            ->where('project_id', $req->project_id)
            ->where('customer_id', $req->customer_id)
            ->where('type_id', $req->type_id)
            ->orderBy('created_at', 'desc')
            ->take(15)->get();
        try {
            return response()->json(['data' => $data, 'message' => 200]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'error'
            ]);
        }
    }

    public function selectFttx($id)
    {
        $fttx = Fttx::with('customer')->find($id);
        return $fttx;
    }

    public function selectStandardPriceFttx(Request $request)
    {
        $fttxCustomerPrice = FttxCustomerPrice::where('customer_id', $request->customerId)->first();
        $data['rental_price'] = 0;
        $data['ppcc'] = $request->currentPpcc && $request->currentPpcc != 'null' ? $request->currentPpcc : 0;
        $data['rental_pole'] = $request->currentRentalPole && $request->currentRentalPole != 'null' ? $request->currentRentalPole : 0;
        if ($fttxCustomerPrice) {
            $posSpeedPrice = $fttxCustomerPrice->pos_speeds ?? [];
            if ($posSpeedPrice && json_decode($posSpeedPrice)[0]) {
                foreach (json_decode($posSpeedPrice)[0] as $value) {
                    if ($value->pos_speed_id == $request->posSpeedId) {
                        if ($value->rental_price !== null) {
                            $data['rental_price'] = $value->rental_price;
                        } else {
                            $data['rental_price'] = $request->currentRentalPrice && $request->currentRentalPrice != 'null' ? $request->currentRentalPrice : 0;
                        }
                    }
                }
            } else {
                $data['rental_price'] = $request->currentRentalPrice && $request->currentRentalPrice != 'null' ? $request->currentRentalPrice : 0;
            }
        } else {
            $data['rental_price'] = $request->currentRentalPrice && $request->currentRentalPrice != 'null' ? $request->currentRentalPrice : 0;
        }
        return $data;
    }
}
