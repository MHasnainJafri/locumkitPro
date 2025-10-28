<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreTranslateTable extends Migration
{
    public function up()
    {
        Schema::create('core_translate', function (Blueprint $table) {
            $table->id();
            $table->text('source');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('core_translate');
    }
}