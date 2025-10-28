<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteTownTable extends Migration
{
	public function up()
	{
		Schema::create('site_towns', function (Blueprint $table) {
			$table->id();
			$table->string('town', 100);
			$table->decimal('lat', 8, 5);
			$table->decimal('lon', 8, 5);
			$table->string('country', 100);
			$table->string('region', 100);
			$table->string('grid_reference', 8);
			$table->integer('easting');
			$table->integer('northing');
			$table->string('postcode', 10);
			$table->string('nuts_region', 50);
			$table->string('type', 50);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('site_towns');
	}
}