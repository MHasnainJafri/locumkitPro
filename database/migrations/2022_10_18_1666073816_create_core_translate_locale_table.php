<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreTranslateLocaleTable extends Migration
{
    public function up()
    {
        Schema::create('core_translate_locale', function (Blueprint $table) {
            $table->id();
            $table->text('destination');
            $table->string('locale');
            $table->integer('core_translate_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('core_translate_locale');
    }
}