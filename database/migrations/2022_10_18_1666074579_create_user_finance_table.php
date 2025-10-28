<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFinanceTable extends Migration
{
	public function up()
	{
		Schema::create('user_finance', function (Blueprint $table) {

			$table->integer('id');
			$table->integer('job_id');
			$table->integer('fre_id');
			$table->integer('emp_id');
			$table->integer('job_rate');
			$table->integer('invoice_id')->nullable();
			$table->integer('expenses_id')->nullable();
			$table->integer('status')->default('0');
			$table->string('job_date')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('user_finance');
	}
}
