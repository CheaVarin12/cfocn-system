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
        Schema::create('fttx_customer_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable();
            $table->longText('new_install_price')->nullable();
            $table->longText('pos_speeds')->nullable();
            $table->tinyInteger('user_id')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('fttx_customer_prices');
    }
};
