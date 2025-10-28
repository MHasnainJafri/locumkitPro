<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLeaversTableTable extends Migration
{
    public function up()
    {
        Schema::create('user_leavers_table', function (Blueprint $table) {

            $table->integer('lid');
            $table->integer('uid');
            $table->string('user_email');
            $table->string('user_name');
            $table->string('user_reason_to_leave')->default('Not mention');
            $table->datetime('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_leavers_table');
    }
}
