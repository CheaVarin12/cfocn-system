<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_number',
        'po_id',
        'multiple_po_id',
        'po_number',
        'customer_id',
        'data_customer',
        'total_price',
        'vat',
        'total_grand',
        'total_qty',
        'charge_number',
        'charge_type',
        'status',
        'paid_status',
        'issue_date',
        'paid_amount',
        'issue_date',
        'exchange_rate',
        'invoice_period',
        'note',
        'remark',
        'install_number',
        'period_start',
        'period_end',
        'doc_status',
        'user_id',
        'tax_status',
    ];

    protected $casts = [
        'total_grand' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'po_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function invoiceDetail()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }

    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'invoice_id');
    }
    public function creditNote()
    {
        return $this->hasMany(CreditNote::class, 'invoice_id');
    }
}
