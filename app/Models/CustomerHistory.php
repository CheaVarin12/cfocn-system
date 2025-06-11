<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerHistory extends Model
{
    use HasFactory;
    protected $table = 'customer_histories';
    protected $fillable = [
        'customer_id',
        'data_customer',
        'status',
        'is_active',
        'user_id'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
}
