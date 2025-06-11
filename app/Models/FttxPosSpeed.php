<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FttxPosSpeed extends Model
{
    use HasFactory;
    protected $table = 'fttx_pos_speeds';
    protected $fillable = [
        'split_pos',
        'key_search_import',
        'rental_price',
        'ppcc_price',
        'new_install_price',
        'description',
        'user_id',
        'status'
    ];

    public function getRentalPriceAttribute($value)
    {
        return json_decode($value);
    }
    public function getPpccPriceAttribute($value)
    {
        return json_decode($value);
    }
    public function getNewInstallPriceAttribute($value)
    {
        return json_decode($value);
    }

    public function fttx()
    {
        return $this->hasMany(Fttx::class, 'pos_speed_id');
    }

    public function priceByPosSpeed()
    {
        return $this->hasMany(FttxPriceByPosSpeed::class, 'pos_speed_id');
    }
}
