<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fttx extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'fttxs';

    protected $fillable = [
        'work_order_isp',
        'customer_id',
        'work_order_cfocn',
        'subscriber_no',
        'isp_ex_work_order_isp',
        'status',
        'name',
        'phone',
        'address',
        'zone',
        'city',
        'port',
        'pos_speed_id',
        'applicant_team_install',
        'team_install',
        'create_time',
        'completed_time',
        'date_ex_complete_old_order',
        'dismantle_date',
        'dismantle_order_cfocn',
        'lay_fiber',
        'remark_first',
        'reactive_date',
        'reactive_payment_period',
        'change_splitter_date',
        'relocation_date',
        'start_payment_date',
        'last_payment_date',
        'initial_installation_order_complete_time',
        'first_relocation_order_complete_date',
        'payment_date',
        'payment_status',
        'online_days',
        'deadline',
        'day_remaining',
        'customer_type',
        'new_installation_fee',
        'fiber_jumper_fee',
        'digging_fee',
        'first_payment_period',
        'initial_payment_period',
        'rental_price',
        'ppcc',
        'number_of_pole',
        'rental_pole',
        'other_fee',
        'discount',
        'total',
        'remark_second',
        'check_status',
        'reactive_date_check',
        'change_splitter_date_check',
        'relocation_date_check',
        'user_id',
    ];

    public function customerType()
    {
        return $this->belongsTo(FttxCustomerType::class, 'customer_type');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function posSpeed()
    {
        return $this->belongsTo(FttxPosSpeed::class, 'pos_speed_id', 'id');
    }

    public function fttxDetail(){

        return $this->hasMany(FttxDetail::class,'fttx_id');
        
    }
}
