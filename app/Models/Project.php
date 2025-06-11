<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vat_tin',
        'phone',
        'status',
    ];
    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }
    public function licenseFee()
    {
        return $this->hasOne(LicenseFee::class);
    }
}
