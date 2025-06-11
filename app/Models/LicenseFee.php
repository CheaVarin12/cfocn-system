<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseFee extends Model
{
    use HasFactory;
    protected $table = 'license_fees';

    protected $fillable = ['project_id','percentage','license_fee','year','status'];

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }

}
