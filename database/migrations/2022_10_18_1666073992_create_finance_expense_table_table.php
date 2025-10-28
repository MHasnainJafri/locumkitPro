<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceExpenseTableTable extends Migration
{
	public function up()
	{
		Schema::dropIfExists('finance_expense_tables');

		Schema::create('finance_expenses', function (Blueprint $table) {
			$table->id();
			$table->integer('job_id')->nullable();
			$table->unsignedSmallInteger('job_type')->default(1)->comment("1. Website Job 2. Private Job 3. Other job");
			$table->integer('freelancer_id')->nullable();
			$table->float('job_rate');
			$table->date('job_date')->nullable();
			$table->foreignId('expense_type_id')->constrained()->nullable();
			$table->string('description')->nullable();
			$table->boolean('is_bank_transaction_completed')->default(false);
			$table->date('bank_transaction_date')->nullable();
			$table->string("receipt")->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('finance_expenses');
	}
}