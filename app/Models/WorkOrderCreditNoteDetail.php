<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderCreditNoteDetail extends Model
{
    use HasFactory;
    protected $table = 'work_order_credit_note_details';
    protected $fillable = [
        'credit_note_id',
        'service_id',
        'des', 
        'qty', 
        'price', 
        'uom', 
        'amount'
    ];

    public function service()
    {
        return $this->belongsTo(FTTHService::class, 'service_id');
    }
}
