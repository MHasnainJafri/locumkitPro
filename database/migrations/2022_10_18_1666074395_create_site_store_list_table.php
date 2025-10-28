<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteStoreListTable extends Migration
{
	public function up()
	{
		Schema::create('site_store_list', function (Blueprint $table) {

			$table->integer('st_id');
			$table->string('owner_name');
			$table->string('address');
			$table->string('teelphone');
			$table->string('store_name');
			$table->string('post_code');
			$table->string('region');
			$table->integer('cat_id');
		});
	}

	public function down()
	{
		Schema::dropIfExists('site_store_list');
	}
}