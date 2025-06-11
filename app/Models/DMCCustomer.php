<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DMCCustomer extends Model
{
    use HasFactory;
    protected $table = 'dmc_customers';
    protected $fillable = [
        'customer_code',
        'customer_name',
        'register_date',
        'customer_address',
        'status',
        'inactive_date',
    ];
}
