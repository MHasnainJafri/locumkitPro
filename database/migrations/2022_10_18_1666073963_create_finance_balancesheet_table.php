<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceBalancesheetTable extends Migration
{
    public function up()
    {
        Schema::create('finance_balancesheets', function (Blueprint $table) {
            $table->id();
            $table->integer('fre_id');
            $table->float('income_tax')->nullable();
            $table->text('input_data');
            $table->string('financial_year')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('finance_balancesheets');
    }
}