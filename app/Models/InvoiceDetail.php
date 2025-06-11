<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'invoice_details';
    protected $fillable = ['invoice_id', 'service_id', 'des', 'qty', 'price', 'uom', 'rate_first', 'rate_second', 'amount','purchase_id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
