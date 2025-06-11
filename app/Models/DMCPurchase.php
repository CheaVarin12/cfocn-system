<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DMCPurchase extends Model
{
    use HasFactory;
    protected $table = 'dmc_purchases';
    protected $fillable = [
        'dmc_customer_id',
        'project_id',
        'po_number',
        'pac_number',
        'service_type',
        'description',
        'type',
        'qty_cores',
        'length',
        'pac_date',
        'register_date',
        'customer_code',
        'customer_name',
        'customer_address',
        'status',
        'inactive_date',
        'location',
        'po_date'
    ];

    public function dmcCustomer()
    {
        return $this->belongsTo(DMCCustomer::class, 'dmc_customer_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
