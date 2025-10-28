<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatatypeTable extends Migration
{
    public function up()
    {
        Schema::create('datatype', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('prevalue_value');
            $table->string('model');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('datatype');
    }
}