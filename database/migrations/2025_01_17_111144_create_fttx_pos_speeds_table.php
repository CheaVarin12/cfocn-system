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
        Schema::create('fttx_pos_speeds', function (Blueprint $table) {
            $table->id();
            $table->string('split_pos',255);
            $table->string('key_search_import');
            $table->text('rental_price')->nullable();
            $table->text('ppcc_price')->nullable();
            $table->text('new_install_price')->nullable();
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
        Schema::dropIfExists('fttx_pos_speeds');
    }
};
