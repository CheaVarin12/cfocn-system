<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FttxSettingPrice extends Model
{
    use HasFactory;
    protected $table = 'fttx_setting_prices';
    protected $fillable = [
        'price',
        'type',
        'user_id',
        'description',
        'status'
    ];

    public function getPriceAttribute($value)
    {
        return json_decode($value);
    }
}
