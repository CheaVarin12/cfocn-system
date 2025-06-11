<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderInvoice extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'work_order_invoices';
    protected $fillable = [
        'invoice_number',
        'order_id',
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

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function invoiceDetail()
    {
        return $this->hasMany(WorkOrderInvoiceDetail::class, 'invoice_id');
    }

    public function receipt()
    {

        return $this->hasMany(Receipt::class, 'invoice_id');
    }
}
