<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobReminderTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('job_reminder');
        Schema::create('job_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained();
            $table->foreignId('employer_id')->constrained("users");
            $table->foreignId('freelancer_id')->constrained("users");
            $table->date("job_date");
            $table->date("job_reminder_date");
            $table->integer('job_reminder_status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_reminders');
    }
}