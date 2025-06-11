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
        Schema::create('child_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('credit_noted_id')->nullable();
            $table->integer('purchase_id')->nullable();
            $table->double('total_qty')->nullable();
            $table->double('vat')->nullable();
            $table->double('sub_total')->nullable();
            $table->double('grand_total')->nullable();
            $table->date('issue_date')->nullable();
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
        Schema::dropIfExists('child_credit_notes');
    }
};
