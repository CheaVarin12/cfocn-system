<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildCreditNote extends Model
{
    use HasFactory;
    protected $table = 'child_credit_notes';
    protected $fillable = [
        'credit_noted_id',
        'purchase_id',
        'total_qty',
        'vat',
        'sub_total',
        'grand_total',
        'issue_date',
    ];
}
