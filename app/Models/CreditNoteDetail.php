<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteDetail extends Model
{
    use HasFactory;
    protected $table = 'credit_note_details';
    protected $fillable = ['credit_note_id', 'service_id', 'des', 'qty', 'price', 'uom', 'rate_first', 'rate_second', 'amount','purchase_id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
}
