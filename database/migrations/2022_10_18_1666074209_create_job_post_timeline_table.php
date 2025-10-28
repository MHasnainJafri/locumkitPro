<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPostTimelineTable extends Migration
{
    public function up()
    {
        Schema::create('job_post_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId("job_post_id")->constrained();
            $table->date("job_date_new");
            $table->string('job_timeline_hrs')->default('10');
            $table->decimal('job_rate_new', 7, 2);
            $table->unsignedSmallInteger('job_timeline_status')->comment("1:current, 2:old, 3:new, 4:final");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_post_timelines');
    }
}