<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildInvoice extends Model
{
    use HasFactory;
    protected $table = 'child_invoices';
    protected $fillable = [
        'invoice_id',
        'purchase_id',
        'total_qty',
        'vat',
        'sub_total',
        'grand_total',
        'issue_date',
    ];


}
