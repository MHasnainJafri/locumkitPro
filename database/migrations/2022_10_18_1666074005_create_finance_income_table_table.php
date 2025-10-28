<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceIncomeTableTable extends Migration
{
	public function up()
	{
		Schema::dropIfExists('finance_incomes');
		Schema::create('finance_incomes', function (Blueprint $table) {
			$table->id();
			$table->integer('job_id')->nullable();
			$table->unsignedSmallInteger('job_type')->default(1)->comment("1. Website Job 2. Private Job 3. Other job");
			$table->integer('freelancer_id')->nullable();
			$table->integer('employer_id')->nullable();
			$table->float('job_rate');
			$table->date('job_date')->nullable();
			$table->unsignedSmallInteger('income_type')->default(1)->comment("1. Income 2.Bonus 3.Other");
			$table->boolean('is_bank_transaction_completed')->default(false);
			$table->date('bank_transaction_date')->nullable();
			$table->string('store')->nullable();
			$table->string('location')->nullable();
			$table->string('supplier')->nullable();
			$table->unsignedSmallInteger('status')->comment("1. Pending 2. Paid 3. Aprroved");
			$table->foreignId('invoice_id')->nullable()->constrained()->comment("Income Invoice ID");
			$table->boolean('is_invoice_required')->default(true)->comment("1 : Yes, 0 No");
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('finance_incomes');
	}
}
