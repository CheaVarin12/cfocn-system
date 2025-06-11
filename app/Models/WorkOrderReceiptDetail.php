<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderReceiptDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'work_order_receipt_details';
    protected $fillable = [
        'receipt_id',
        'service_id',
        'des',
        'qty',
        'price',
        'uom',
        'rate_first',
        'rate_second',
        'amount'
    ];

    public function service()
    {
        return $this->belongsTo(FTTHService::class);
    }
}
