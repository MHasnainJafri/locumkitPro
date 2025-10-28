<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobFeedbackTable extends Migration
{
	public function up()
	{

		Schema::create('job_feedback', function (Blueprint $table) {
			$table->id();
			$table->foreignId('employer_id')->constrained("users")->nullable();
			$table->foreignId('freelancer_id')->constrained("users")->nullable();
			$table->integer('job_id')->nullable();
			$table->float('rating');
			$table->string('feedback', 1000);
			$table->string('comments', 1000);
			$table->enum('user_type', ["employer", "freelancer"])->nullable();
			$table->integer('cat_id')->nullable();
			$table->integer('status')->default('0')->comment("0. Pending 1. Approve 2.Dispute Pending 3. Dispute Approved");
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('job_feedback');
	}
}