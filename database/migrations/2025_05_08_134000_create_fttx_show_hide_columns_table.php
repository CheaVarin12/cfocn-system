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
        Schema::create('fttx_show_hide_columns', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->nullable();
            $table->string('column',255)->nullable();
            $table->integer('status')->nullable()->comment('1 is show ,0 is hide');
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
        Schema::dropIfExists('fttx_show_hide_columns');
    }
};
