<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FttxCustomerPrice extends Model
{
    use HasFactory;
    protected $table = 'fttx_customer_prices';
    protected $fillable = [
        'customer_id',
        'new_install_price',
        'pos_speeds',
        'user_id',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
