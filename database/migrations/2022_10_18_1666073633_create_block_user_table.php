<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockUserTable extends Migration
{
    public function up()
    {
        Schema::create('block_users', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('freelancer_id')->constrained("users")->nullable();
            $table->foreignId('employer_id')->constrained("users")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('block_users');
    }
}