<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployerStoreListTable extends Migration
{
	public function up()
	{
		Schema::create('employer_store_lists', function (Blueprint $table) {
			$table->id();
			$table->foreignId("employer_id")->constrained("users", "id");
			$table->string('store_name');
			$table->string('store_address');
			$table->string('store_region');
			$table->string('store_zip');
			$table->string('store_start_time', 1000)->nullable();
			$table->string('store_end_time', 1000)->nullable();
			$table->string('store_lunch_time', 1000)->nullable();
			$table->string('language')->nullable();
			$table->integer('status')->default('0');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('employer_store_lists');
	}
}