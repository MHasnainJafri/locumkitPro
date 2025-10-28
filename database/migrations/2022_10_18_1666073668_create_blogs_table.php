<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
	public function up()
	{
		Schema::create('blogs', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->string('slug');
			$table->longText('description');
			$table->string('image_path');
			$table->foreignId("user_acl_role_id")->nullable()->constrained();
			$table->foreignId("blog_category_id")->constrained("blog_categories", "id");
			$table->integer('status')->default('1');
			$table->string('metatitle')->default('Locumkit');
			$table->string('metadescription')->default('Locumkit');
			$table->string('metakeywords')->default('Locumkit');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('blogs');
	}
}