<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExtraInfoTable extends Migration
{
	public function up()
	{
		Schema::create('user_extra_infos', function (Blueprint $table) {
			$table->id();
			$table->foreignId("user_id")->constrained();
			$table->string('aoc_id')->nullable();
			$table->string('gender')->nullable();
			$table->date('dob')->nullable();
			$table->string('mobile')->nullable();
			$table->string('address')->nullable();
			$table->string('city')->nullable();
			$table->string('zip')->nullable();
			$table->string('telephone')->nullable();
			$table->string('company')->nullable();
			$table->string('profile_image')->nullable();
			$table->string('max_distance')->nullable();
			$table->string('minimum_rate', 1000)->default('0.00')->nullable();
			$table->text('site_town_ids')->nullable();
			$table->string('cet')->default('0')->nullable();
			$table->string('goc')->nullable();
			$table->string('aop')->nullable();
			$table->string('store_type_name')->nullable();
			$table->string('inshurance_company')->nullable();
			$table->string('inshurance_no')->nullable();
			$table->string('inshurance_renewal_date')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('user_extra_infos');
	}
}