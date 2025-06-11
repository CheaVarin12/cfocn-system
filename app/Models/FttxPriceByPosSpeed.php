<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FttxPriceByPosSpeed extends Model
{
    use HasFactory;
    protected $table = 'fttx_price_by_pos_speeds';
    protected $fillable = [
        'pos_speed_id',
        'rental_price_six_month',
        'rental_price_twelve_month',
    ];
}
