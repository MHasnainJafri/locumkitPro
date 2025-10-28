<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPostTable extends Migration
{
	public function up()
	{
		Schema::create('job_posts', function (Blueprint $table) {
			$table->id();
			$table->foreignId("employer_id")->constrained("users", "id")->onDelete('cascade');
			$table->foreignId("user_acl_profession_id")->nullable()->constrained();
			$table->string('job_title');
			$table->date('job_date');
			$table->string('job_start_time');
			$table->text('job_post_desc')->nullable();
			$table->double('job_rate');
			$table->unsignedSmallInteger('job_type')->comment("1. First come First 2. Build list");
			$table->string('job_address');
			$table->string('job_region');
			$table->string('job_zip');
			$table->foreignId('employer_store_list_id')->constrained()->onDelete('cascade');
			$table->unsignedSmallInteger('job_status')->comment("1.Open/Waiting 2.Close/Expired 3.Disable 4.Accept 5.Done/Completed 6.Freeze 7.Delete 8.Cancel");
			$table->boolean("is_invitation_sent")->default(false);
			$table->integer('job_relist')->default(0);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('job_posts');
	}
}