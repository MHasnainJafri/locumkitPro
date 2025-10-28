<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTownlistTable extends Migration
{
    public function up()
    {
        Schema::create('townlist', function (Blueprint $table) {

            $table->integer('tid');
            $table->string('town', 29)->nullable();
            $table->string('ceremonial', 24)->nullable();
            $table->string('status', 33)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('townlist');
    }
}
