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
        Schema::create('fttx_setting_prices', function (Blueprint $table) {
            $table->id();
            $table->text('price');
            $table->integer('type');
            $table->text('description')->nullable();
            $table->tinyInteger('user_id')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('fttx_setting_prices');
    }
};
