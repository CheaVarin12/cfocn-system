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
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_number')->default(0);
            $table->tinyInteger('customer_id');
            $table->tinyInteger('project_id');
            $table->tinyInteger('serviceType_id');
            $table->tinyInteger('service_id');
            $table->enum('amount',['Send','Pending']);
            $table->string('item');
            $table->text('description');
            $table->Integer('quatity');
            $table->string('month')->default('Month');
            $table->decimal('price', 5, 2);
            $table->decimal('rate', 5, 2)->nullable();
            $table->decimal('totalAmount', 5, 2)->nullable();
            $table->timestamp('issue_date');
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
        Schema::dropIfExists('invoices');
    }
};
