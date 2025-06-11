<?php

namespace App\Services;

use App\Models\CreditNote;
use App\Models\HistoryDmcSendFile;
use App\Models\Invoice;
use App\Models\UploadFile;
use App\Models\WorkOrderCreditNote;
use App\Models\WorkOrderInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DMCFileManagerService
{
    public function year($req)
    {
        $data = HistoryDmcSendFile::where(function ($q) use ($req) {
            if ($req->search) {
                $q->where('year', 'like', '%' . $req->search . '%');
            }
            $q->where('file_type', $req->doc_type);
            $q->select('id', 'year');
            $q->orderBy('year', 'desc');
        })->groupBy(DB::raw('year'))->paginate(25);
        return $data;
    }
    public function fetchDataYearOfMonth($req)
    {
        $data = HistoryDmcSendFile::where(function ($q) use ($req) {
            $q->where('file_type', $req->doc_type);
            $q->where('year', $req->year);
            $q->select('id', 'year', 'month');
            $q->orderBy('month', 'asc');
        })->groupBy(DB::raw('month'))->paginate(25);
        return $data;
    }
    public function fetchData($req)
    {
        $data = HistoryDmcSendFile::where('file_type', $req->doc_type)->where(function ($q) use ($req) {
            if ($req->year) {
                $q->where('year', $req->year);
            }
            if ($req->month && $req->month != "all") {
                $q->where('month', (int) $req->month);
            }
            if ($req->search) {
                $q->whereHas('invoice', function ($query) use ($req) {
                    $query->where('invoice_number', 'like', '%' . $req->search . '%');
                });
                $q->orWhereHas('creditNote', function ($query) use ($req) {
                    $query->where('credit_note_number', 'like', '%' . $req->search . '%');
                });
            }
        })->paginate(25);
        if (isset($data) && count($data) > 0) {
            foreach ($data as $item) {
                if (isset($item->is_ftth) && $item->is_ftth == 1) {
                    $invoice = $item->invoice_id ? WorkOrderInvoice::withTrashed()->find($item->invoice_id) : '';
                    $item->credit_note = $item->file_type == "credit_note" ? WorkOrderCreditNote::where('invoice_id', $item->invoice_id)->select('id', 'credit_note_number', 'invoice_number', 'invoice_id')->first() : null;
                } else {
                    $invoice = $item->invoice_id ? Invoice::withTrashed()->find($item->invoice_id) : '';
                    $item->credit_note = $item->file_type == "credit_note" ? CreditNote::where('invoice_id', $item->invoice_id)->select('id', 'credit_note_number', 'invoice_number', 'invoice_id')->first() : null;
                }
                $item->invoice_number = $invoice ? $invoice->invoice_number : null;
            }
        }
        return $data;
    }
}
