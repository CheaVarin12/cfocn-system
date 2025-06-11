<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FttxCustomerType extends Model
{
    use HasFactory;
    protected $table = 'fttx_customer_types';
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'status'
    ];


}
