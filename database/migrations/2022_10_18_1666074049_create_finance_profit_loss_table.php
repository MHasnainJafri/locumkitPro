<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceProfitLossTable extends Migration
{
    public function up()
    {
        Schema::create('finance_profit_loss', function (Blueprint $table) {
            $table->id();
            $table->integer('fre_id');
            $table->float('revenue')->nullable();
            $table->float('cos')->nullable();
            $table->float('othercost')->nullable();
            $table->float('income_tax')->nullable();
            $table->float('interest_income');
            $table->text('tax_calculation');
            $table->string('financial_year');
            $table->integer('starting_month')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('finance_profit_loss');
    }
}
