<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayoutTable extends Migration
{
    public function up()
    {
        Schema::create('layout', function (Blueprint $table) {
            $table->id();
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->string('name');
            $table->string('identifier');
            $table->string('description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('layout');
    }
}
