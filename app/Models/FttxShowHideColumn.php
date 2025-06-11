<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FttxShowHideColumn extends Model
{
    use HasFactory;
    protected $table = 'fttx_show_hide_columns';
    protected $fillable = [
        'name',
        'column',
        'status'
    ];
}
