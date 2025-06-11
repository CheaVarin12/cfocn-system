<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FttxDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'fttx_details';

    protected $fillable = [
        'fttx_id',
        'customer_id',
        'date',
        'expiry_date',
        'new_installation_fee',
        'fiber_jumper_fee',
        'digging_fee',
        'rental_unit_price',
        'ppcc',
        'pole_rental_fee',
        'other_fee',
        'discount',
        'remark',
        'invoice_number',
        'receipt_number',
        'total_amount',
        'user_id',
    ];

    public function fttx()
    {
        return $this->belongsTo(Fttx::class, 'fttx_id', 'id');
    }

}
