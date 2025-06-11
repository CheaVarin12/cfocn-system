<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fttxs', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_isp',255)->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('work_order_cfocn',255)->nullable();
            $table->string('subscriber_no',255)->nullable();
            $table->string('isp_ex_work_order_isp',255)->nullable();
            $table->integer('status')->nullable()->nullable();
            $table->string('name',255)->nullable();
            $table->string('phone',255)->nullable();
            $table->text('address')->nullable();
            $table->string('zone',255)->nullable();
            $table->string('city',255)->nullable();
            $table->string('port',255)->nullable();
            $table->integer('pos_speed_id')->nullable();
            $table->string('applicant_team_install',255)->nullable();
            $table->string('team_install',255)->nullable();
            $table->date('create_time')->nullable();
            $table->date('completed_time')->nullable();
            $table->date('date_ex_complete_old_order')->nullable();
            $table->date('dismantle_date')->nullable();
            $table->string('dismantle_order_cfocn',255)->nullable();
            $table->string('lay_fiber',255)->nullable();
            $table->text('remark_first')->nullable();
            $table->date('reactive_date')->nullable();
            $table->integer('reactive_payment_period')->nullable();
            $table->date('change_splitter_date')->nullable();
            $table->date('relocation_date')->nullable();
            $table->date('start_payment_date')->nullable();
            $table->date('last_payment_date')->nullable();
            $table->date('initial_installation_order_complete_time')->nullable();
            $table->date('first_relocation_order_complete_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_status')->nullable();
            $table->text('online_days')->nullable()->comment('month and day');
            $table->date('deadline')->nullable();
            $table->integer('day_remaining');
            $table->integer('customer_type')->nullable();
            $table->double('new_installation_fee')->nullable();
            $table->double('fiber_jumper_fee')->nullable();
            $table->double('digging_fee')->nullable();
            $table->integer('first_payment_period')->nullable();
            $table->integer('initial_payment_period')->nullable();
            $table->double('rental_price')->nullable();
            $table->double('ppcc')->nullable();
            $table->integer('number_of_pole')->nullable();
            $table->double('rental_pole')->nullable();
            $table->double('other_fee')->nullable();
            $table->double('discount')->nullable();
            $table->text('remark_second')->nullable();
            $table->integer('user_id')->nullable();
            $table->double('payment_next_month')->nullable();
            $table->double('total')->nullable();
            $table->string('check_status')->nullable();
            $table->date('dismantle_date_check')->nullable();
            $table->date('reactive_date_check')->nullable();
            $table->date('change_splitter_date_check')->nullable();
            $table->date('relocation_date_check')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. 
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fttxs');
    }
};
