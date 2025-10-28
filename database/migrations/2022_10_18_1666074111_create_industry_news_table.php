<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustryNewsTable extends Migration
{
	public function up()
	{
		Schema::create('industry_news', function (Blueprint $table) {
			$table->id();
			$table->string('title', 300);
			$table->string('slug');
			$table->longText('description');
			$table->string('image_path')->default('public/media/files/industry_news/logo.png');
			$table->string('user_type')->nullable();
			$table->string('category_id')->nullable();
			$table->boolean('status')->default(true)->comment("0. unactive, 1. active");
			$table->string('metatitle')->default('Locumkit');
			$table->string('metadescription')->default('Locumkit');
			$table->string('metakeywords')->default('Locumkit');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('industry_news');
	}
}