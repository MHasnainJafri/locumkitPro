<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBankDetailTable extends Migration
{
    public function up()
    {
        Schema::create('user_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->string('acccount_name');
            $table->integer('acccount_number');
            $table->string('acccount_sort_code', 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_bank_details');
    }
}