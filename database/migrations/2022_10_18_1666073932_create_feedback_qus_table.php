<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackQusTable extends Migration
{
    public function up()
    {
        Schema::create('feedback_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_freelancer')->nullable()->comment("Feedback Question for Feelancer");
            $table->string('question_employer')->nullable()->comment("Feedback Question for Employer");
            $table->integer('question_cat_id')->nullable();
            $table->integer('question_sort_order')->default(0);
            $table->integer('question_status')->default('0')->comment("Feedback Question Status 0. Deactive 1. Active 2. Delete");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback_questions');
    }
}