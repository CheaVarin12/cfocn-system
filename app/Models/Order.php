<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'order_number',
        'customer_id',
        'project_id',
        'issue_date',
        'end_date',
        'type_id',
        'total_qty',
        'total_price',
        'status',
        'user_id',
        'contract_number',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function invoice()
    {
        return $this->hasMany(WorkOrderInvoice::class, 'order_id');
    }

    //scheduleInvoice
    public function invoiceHasOneSchedule()
    {
        return $this->hasOne(WorkOrderInvoice::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
