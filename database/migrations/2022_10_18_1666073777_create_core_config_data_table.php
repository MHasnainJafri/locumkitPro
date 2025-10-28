<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreConfigDataTable extends Migration
{
    public function up()
    {
        Schema::create('core_config_data', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('core_config_data');
    }
}