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
        Schema::create('fttx_price_by_pos_speeds', function (Blueprint $table) {
            $table->id();
            $table->integer('pos_speed_id');
            $table->double('rental_price_six_month');
            $table->double('rental_price_twelve_month');
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
        Schema::dropIfExists('fttx_price_by_pos_speeds');
    }
};
