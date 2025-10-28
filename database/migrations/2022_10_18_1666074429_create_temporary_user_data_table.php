<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemporaryUserDataTable extends Migration
{
    public function up()
    {
        Schema::create('temporary_user_data', function (Blueprint $table) {

            $table->integer('tm_id');
            $table->integer('uid');
            $table->string('fname');
            $table->string('lname');
            $table->string('uemail');
            $table->string('upassword');
            $table->datetime('date_added');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temporary_user_data');
    }
}
