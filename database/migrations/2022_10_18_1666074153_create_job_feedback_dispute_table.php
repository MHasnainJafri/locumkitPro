<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobFeedbackDisputeTable extends Migration
{
    public function up()
    {
        Schema::create('job_feedback_disputes', function (Blueprint $table) {
            $table->id();
            $table->integer('feedback_id');
            $table->enum('user_type', ["employer", "freelancer"]);
            $table->string('comment')->nullable();
            $table->integer('status')->default('0');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_feedback_disputes');
    }
}