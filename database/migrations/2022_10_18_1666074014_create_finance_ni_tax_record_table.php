<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceNiTaxRecordTable extends Migration
{
	public function up()
	{
		Schema::create('finance_ni_tax_records', function (Blueprint $table) {
			$table->id();
			$table->string('finance_year');
			$table->float('c4_min_ammount_1')->default('0');
			$table->float('c4_min_ammount_tax_1')->default('0');
			$table->float('c4_min_ammount_2')->default('0');
			$table->float('c4_min_ammount_tax_2')->default('0');
			$table->float('c4_min_ammount_3')->default('0');
			$table->float('c4_min_ammount_tax_3')->default('0');
			$table->float('c2_min_amount')->default('0');
			$table->float('c2_tax')->default('0');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('finance_ni_tax_records');
	}
}