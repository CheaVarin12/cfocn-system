<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $table = 'purchases';
    protected $fillable = [
        'pac_number',
        'po_number',
        'customer_id',
        'project_id',
        'serviceType_id',
        'service_id',
        'type_id',
        'type_data',
        'total_price',
        'total_rate',
        'total_qty',
        'status',
        'pac_type',
        'issue_date',
        'end_date',
        'cores',
        'length',
        'user_id',
        'contract_number',
        'location',
        'po_date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }
    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'po_id');
    }
    //scheduleInvoice
    public function invoiceHasOneSchedule()
    {
        return $this->hasOne(Invoice::class, 'po_id')->orderBy('created_at', 'desc');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function purchaseDetail()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function childInvoice()
    {
        return $this->hasMany(ChildInvoice::class, 'purchase_id');
    }

    public function childCreditNote(){
        return $this->hasMany(ChildCreditNote::class, 'purchase_id');
    }
}
