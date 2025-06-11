<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = ['name','ordering', 'status', 'type_id'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
