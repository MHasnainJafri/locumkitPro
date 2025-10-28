<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScriptTable extends Migration
{
    public function up()
    {
        Schema::create('script', function (Blueprint $table) {
            $table->id();
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
            $table->string('name')->nullable();
            $table->string('identifier')->nullable();
            $table->string('description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('script');
    }
}
