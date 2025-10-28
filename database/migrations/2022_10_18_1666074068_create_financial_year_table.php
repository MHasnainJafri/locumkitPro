<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialYearTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('financial_years');
        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->enum('user_type', ["soletrader", "limitedcompany"])->nullable();
            $table->integer('month_start')->comment("financial year start from which month");
            $table->integer('month_end')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_years');
    }
}