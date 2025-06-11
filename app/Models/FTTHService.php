<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FTTHService extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'ftth_services';
    protected $fillable = ['name', 'description', 'status'];
}
