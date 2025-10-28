<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceEmployerTable extends Migration
{
	public function up()
	{
		Schema::create('finance_employers', function (Blueprint $table) {
			$table->id();
			$table->integer('job_id');
			$table->foreignId('employer_id')->constrained("users");
			$table->integer('freelancer_id')->nullable();
			$table->integer('freelancer_type')->nullable()->comment("1. Website Freelancer 2. Private Freelancer");
			$table->date('job_date');
			$table->float('job_rate')->nullable();
			$table->float('bonus')->nullable();
			$table->boolean('is_paid')->nullable();
			$table->date('paid_date')->nullable();
			$table->integer('status')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('finance_employers');
	}
}