<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabTable extends Migration
{
    public function up()
    {
        Schema::create('tab', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default('0')->nullable();
            $table->integer('document_type_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tab');
    }
}
