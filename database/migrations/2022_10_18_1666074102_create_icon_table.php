<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIconTable extends Migration
{
    public function up()
    {
        Schema::create('icon', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
        });
    }

    public function down()
    {
        Schema::dropIfExists('icon');
    }
}
