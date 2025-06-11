<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderInvoiceDetail extends Model
{
    use HasFactory;
    protected $table = 'work_order_invoice_details';
    protected $fillable = [
        'invoice_id', 
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