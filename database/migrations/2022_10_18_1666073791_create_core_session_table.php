<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreSessionTable extends Migration
{
    public function up()
    {
        Schema::create('core_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('lifetime');
            $table->text('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('core_sessions');
    }
}