<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogUrlInfoTable extends Migration
{
    public function up()
    {
        Schema::create('log_url_info', function (Blueprint $table) {
            $table->bigInteger('id', 20)->unsigned();
            $table->string('url')->default('');
            $table->string('referer')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_url_info');
    }
}
