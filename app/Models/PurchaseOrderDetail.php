<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;
    protected $table = 'purchase_order_details';
    protected $fillable = [
        'purchase_order_id',
        'service_id',
        'name',
        'des',
        'core',
        'length',
        'qty',
        'price',
        'uom',
        'rate',
        'amount',
        'status',
    ];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
