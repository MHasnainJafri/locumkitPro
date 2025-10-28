<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewTable extends Migration
{
    public function up()
    {
        Schema::create('view', function (Blueprint $table) {
            $table->id();
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->string('name');
            $table->string('identifier')->nullable();
            $table->string('description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('view');
    }
}
