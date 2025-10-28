<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManageSmsTable extends Migration
{
    public function up()
    {
        Schema::create('manage_sms', function (Blueprint $table) {

            $table->integer('id');
            $table->integer('userid')->nullable();
            $table->string('contactno', 250)->nullable();
            $table->string('sendfor')->nullable();
            $table->string('msgstatus')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('manage_sms');
    }
}
