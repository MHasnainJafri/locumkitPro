<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLastLoginUserTable extends Migration
{
    public function up()
    {
        Schema::create('last_login_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->dateTime('login_time');
            $table->string('ip_address')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('last_login_users');
    }
}