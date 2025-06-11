<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryInvoiceSendDoc extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'history_invoice_send_docs';
    protected $fillable = [
        'invoice_id',
        'file',
        'file_type'
    ];
}
