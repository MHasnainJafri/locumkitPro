<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileNotificationTable extends Migration
{
    public function up()
    {
        Schema::create('mobile_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->mediumText("token_id");
            $table->smallInteger("status")->default(0)->comment("0:Not Send,1:Success,2:Failed");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mobile_notifications');
    }
}