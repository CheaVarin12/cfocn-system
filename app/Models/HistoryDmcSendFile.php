<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryDmcSendFile extends Model
{
    use HasFactory;
    protected $table = 'history_dmc_send_files';
    protected $fillable = [
        'invoice_id',
        'credit_note_id',
        'year',
        'month',
        'day',
        'file_name',
        'file_path',
        'file_type',
        'extension_type',
        'from_date',
        'to_date',
        'user_id',
        'is_ftth'
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id','id');
    }
    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class,'credit_note_id','id');
    }
}
