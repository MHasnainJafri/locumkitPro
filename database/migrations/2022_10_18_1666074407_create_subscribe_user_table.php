<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribeUserTable extends Migration
{
    public function up()
    {
        Schema::create('subscribe_users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 250)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscribe_users');
    }
}