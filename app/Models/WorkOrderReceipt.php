<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderReceipt extends Model
{
    use HasFactory;
    protected $table = 'work_order_receipts';

    protected $fillable = [
        'receipt_number',
        'receipt_from', //invoice, credit_note
        'invoice_id',
        'customer_id',
        'type_id',
        'data_customer',
        'total_qty',
        'total_price',
        'vat',
        'partial_payment',
        'total_grand',
        'paid_amount',
        'debt_amount',
        'payment_status',
        'payment_method',
        'payment_des',
        'status',
        'paid_date',
        'issue_date',
        'note',
        'user_id',
        'status_type',
    ];

    public function invoices()
    {
        return $this->belongsTo(WorkOrderInvoice::class, 'invoice_id');
    }

    public function creditNote()
    {
        return $this->belongsTo(WorkOrderCreditNote::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function receiptDetail()
    {
        return $this->hasMany(WorkOrderReceiptDetail::class,'receipt_id');
    }
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }
}
