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
        Schema::create('fttx_details', function (Blueprint $table) {
            $table->id();
            $table->integer('fttx_id');
            $table->integer('customer_id')->nullable();
            $table->date('date');
            $table->date('expiry_date');
            $table->double('new_installation_fee')->nullable();
            $table->double('fiber_jumper_fee')->nullable();
            $table->double('digging_fee')->nullable();
            $table->double('rental_unit_price')->nullable();
            $table->double('ppcc')->nullable();
            $table->double('pole_rental_fee')->nullable();
            $table->double('other_fee')->nullable();
            $table->double('discount')->nullable();
            $table->text('remark')->nullable();
            $table->string('invoice_number',255)->nullable();
            $table->string('receipt_number',255)->nullable();
            $table->double('total_amount')->nullable();
            $table->tinyInteger('user_id')->nullable();
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
        Schema::dropIfExists('fttx_details');
    }
};
