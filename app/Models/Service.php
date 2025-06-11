<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = ['name','status','description','type_id'];
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id','id');
    }
}
