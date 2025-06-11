<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;
    protected $table = 'purchase_details';
    protected $fillable = [
        'purchase_id',
        'service_id',
        'project_id',
        'name',
        'des',
        'qty',
        'price',
        'uom',
        'rate',
        'amount',
        'core',
        'length'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
