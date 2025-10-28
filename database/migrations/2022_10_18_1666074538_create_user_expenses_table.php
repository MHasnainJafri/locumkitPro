<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('user_expenses', function (Blueprint $table) {

            $table->integer('id');
            $table->integer('fre_id');
            $table->integer('emp_id');
            $table->integer('job_id');
            $table->string('expense_cost', 755);
            $table->integer('status')->default('0');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_expenses');
    }
}