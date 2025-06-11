<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $table = 'purchase_orders';
    protected $fillable = [
        'po_number',
        'customer_id',
        'project_id',
        'issue_date',
        'duration',
        'duration_type',
        'end_date',
        'po_type',
        'cores',
        'length',
        'type_id',
        'contract_number',
        'location',
        'type_data',
        'type',
        'total_price',
        'total_rate',
        'total_qty',
        'status',
        'user_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function serviceType()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function purchaseOrderDetail()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function file(){
        return $this->hasMany(Document::class,'po_id','id');
    }
}
