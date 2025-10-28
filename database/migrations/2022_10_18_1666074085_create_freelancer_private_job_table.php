<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreelancerPrivateJobTable extends Migration
{
	public function up()
	{
		Schema::dropIfExists('freelancer_private_jobs');
		Schema::create('freelancer_private_jobs', function (Blueprint $table) {
			$table->id();
			$table->foreignId('freelancer_id')->constrained("users");
			$table->string('emp_name');
			$table->string('emp_email')->nullable();
			$table->string('job_title');
			$table->decimal('job_rate', 7, 2);
			$table->string('job_location');
			$table->date('job_date');
			$table->unsignedSmallInteger('status')->default(0)->comment("0. New 1. Notify before day 2. Notify on day 3. Expense Notify 4. Complete 5.Attended");
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('freelancer_private_jobs');
	}
}