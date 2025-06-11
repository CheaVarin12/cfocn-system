<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'customers';
    protected $fillable = [
        'name_en',
        'name_kh',
        'vat_tin',
        'phone',
        'address_en',
        'address_kh',
        'email',
        'gender',
        'status',
        'type',
        'customer_code',
        'register_date',
        'in_active_date',
        'user_id',
        'attention'
    ];

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }
    public function latestPurchase()
    {
        return $this->hasOne(Purchase::class)->latest();
    }
    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }
}
