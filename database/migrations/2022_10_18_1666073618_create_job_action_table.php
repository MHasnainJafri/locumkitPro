<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobActionTable extends Migration
{
    public function up()
    {
        Schema::create('job_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("job_post_id")->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained("users", "id")->onDelete('cascade');
            $table->unsignedSmallInteger('action')->default(0)->comment("0.None 1.Freeze 2.Apply 3.Accept 4.Done 5.Waiting For unfreeze 6.Cancel Job by freelancer 7. Cancel Acceptedjob by emp 8. Cancel Open job by emp");
            $table->integer('freeze_notification_count')->default(0);
            $table->boolean("is_negotiated")->default(false);
            $table->double("negotiation_rate")->nullable();
            $table->text("negotiation_message")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_actions');
    }
}