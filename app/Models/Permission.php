<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    public function ModelHasPermission()
    {
        return $this->hasMany(ModelHasPermission::class, 'permission_id');
    }
}
