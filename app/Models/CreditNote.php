<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;
    protected $table = 'credit_notes';
    protected $fillable = [
        'credit_note_number',
        'invoice_id',
        'invoice_number',
        'po_id',
        'customer_id',
        'data_customer',
        'total_price',
        'vat',
        'total_grand',
        'total_qty',
        'charge_number',
        'charge_type',
        'install_number',
        'status',
        'paid_status',
        'issue_date',
        'exchange_rate',
        'period_start',
        'period_end',
        'note',
        'remark',
        'paid_amount',
        'doc_status',
        'user_id'
    ];

    public function invoices()
    {
        return $this->belongsTo(Invoice::class,'invoice_id')->withTrashed();
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class,'po_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function creditNoteDetail()
    {
        return $this->hasMany(CreditNoteDetail::class,'credit_note_id');
    }

    public function invoiceDetail()
    {
        return $this->hasMany(CreditNoteDetail::class,'credit_note_id');
    }
}
