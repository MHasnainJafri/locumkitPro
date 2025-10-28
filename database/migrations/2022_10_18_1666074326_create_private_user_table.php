<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateUserTable extends Migration
{
    public function up()
    {
        Schema::create('private_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained("users");
            $table->string('name');
            $table->string('email');
            $table->string('mobile', 100)->nullable();
            $table->integer('status')->default(0)->comment("0.Private User 1.Normal User 2. Deleted User");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_users');
    }
}