<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOnDayTable extends Migration
{
    public function up()
    {
        Schema::create('job_on_days', function (Blueprint $table) {
        $table->id();
        $table->foreignId("job_post_id")->constrained()->onDelete('cascade');
        $table->foreignId("employer_id")->constrained("users", "id")->onDelete('cascade');    
        $table->foreignId("freelancer_id")->constrained("users", "id")->onDelete('cascade');
        $table->date("job_date");
        $table->integer('status')->comment("0. Not attend 1. Freelancer Attend 2. Employer verified attendance 3. Feedback Notification Sent 4. Feedback Week notification sent");
        $table->boolean("is_notified")->default(false);
        $table->timestamps();
    });
    }

    public function down()
    {
        Schema::dropIfExists('job_on_days');
    }
}
