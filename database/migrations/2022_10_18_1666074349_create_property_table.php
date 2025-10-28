<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyTable extends Migration
{
    public function up()
    {
        Schema::create('property', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('identifier')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('required')->default(0)->nullable();
            $table->integer('sort_order')->default(0)->nullable();
            $table->integer('tab_id')->nullable();
            $table->integer('datatype_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property');
    }
}
