<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;
    protected $table = 'types';
    protected $fillable = ['name','status','code'];

    public function purchase()
    {
        return $this->hasMany(Purchase::class,'type_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'type_id');
    }
}
