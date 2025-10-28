<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobCancelTable extends Migration
{
    public function up()
    {

        Schema::create('job_cancelations', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('reason', 1000)->nullable();
            $table->enum('cancel_by_user_type', ["live_freelancer", "employer", "private_freelancer"]);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_cancelations');
    }
}