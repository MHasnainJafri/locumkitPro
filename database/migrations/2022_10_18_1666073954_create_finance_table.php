<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('finances');
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->integer('trans_type_id')->comment("Income ID / Expense ID");
            $table->unsignedSmallInteger('trans_type')->comment("1. Income 2. Expense");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('finances');
    }
}
