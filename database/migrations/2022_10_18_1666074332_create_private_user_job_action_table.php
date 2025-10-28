<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateUserJobActionTable extends Migration
{
    public function up()
    {
        Schema::create('private_user_job_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained("users");
            $table->foreignId('private_user_id')->constrained();
            $table->foreignId('job_post_id')->constrained();
            $table->integer('status')->default('1')->comment("1. waiting 2.Apply 3.Accept 4.Done 5. Cancel");
            $table->integer('notify')->default('0')->comment("1: Reminder , 2: Onday Notification");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_user_job_actions');
    }
}