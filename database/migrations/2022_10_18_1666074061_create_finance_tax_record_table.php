<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceTaxRecordTable extends Migration
{
	public function up()
	{
		Schema::dropIfExists('finance_tax_record');

		Schema::create('finance_tax_records', function (Blueprint $table) {
			$table->id();
			$table->string('finance_year')->nullable();
			$table->float('personal_allowance_rate')->nullable();
			$table->integer('personal_allowance_rate_tax')->nullable();
			$table->float('basic_rate')->nullable();
			$table->integer('basic_rate_tax')->nullable();
			$table->float('higher_rate');
			$table->integer('higher_rate_tax');
			$table->float('additional_rate');
			$table->integer('additional_rate_tax');
			$table->integer('company_limited_tax')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('finance_tax_records');
	}
}