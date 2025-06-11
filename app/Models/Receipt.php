<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'receipts';

    protected $fillable = [
        'receipt_number',
        'invoice_id',
        'customer_id',
        'type_id',
        'data_customer',
        'total_qty',
        'total_price',
        'vat',
        'total_grand',
        'partial_payment',
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
        'receipt_from', //invoice, credit_note
    ];

    public function invoices()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function receiptDetail()
    {
        return $this->hasMany(ReceiptDetail::class,'receipt_id');
    }
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }
}
