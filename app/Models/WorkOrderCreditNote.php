<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderCreditNote extends Model
{
    use HasFactory;
    protected $table = 'work_order_credit_notes';
    protected $fillable = [
        'credit_note_number',
        'invoice_id',
        'invoice_number',
        'order_id',
        'customer_id',
        'data_customer',
        'total_price',
        'vat',
        'total_grand',
        'total_qty',
        'exchange_rate',
        'status',
        'paid_status',
        'issue_date',
        'paid_amount',
        'note',
        'remark',
        'period_start',
        'period_end',
        'doc_status',
        'user_id',
    ];

    public function invoices()
    {
        return $this->belongsTo(WorkOrderInvoice::class, 'invoice_id')->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function creditNoteDetails()
    {
        return $this->hasMany(WorkOrderCreditNoteDetail::class, 'credit_note_id');
    }

    public function invoiceDetail()
    {
        return $this->hasMany(WorkOrderCreditNoteDetail::class, 'credit_note_id');
    }
}
