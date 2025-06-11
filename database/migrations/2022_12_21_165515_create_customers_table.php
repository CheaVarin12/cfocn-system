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
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company-name_en')->nullable()->comment('english');
            $table->string('company_name_kh')->nullable()->comment('khmer');
            $table->string('vat_tin',100);
            $table->string('phone');
            $table->longText('address_en')->nullable();
            $table->longText('address_kh')->nullable();
            $table->string('position',50);
            $table->string('email',100);
            $table->enum('gender',['Male','FeMale','Other']);
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
