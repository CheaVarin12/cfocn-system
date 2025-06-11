<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldCustomerInfo extends Model
{
    use HasFactory;
    protected $table = 'old_customer_info';
    protected $fillable = [
        'customer_code',
        'customer_name',
        'register_date',
        'po_number',
        'pac_number',
        'customer_address',
        'service_type',
        'description',
        'type',
        'qty_cores',
        'length',
        'status',
        'inactive_date',
        'user_id',
    ];
}
