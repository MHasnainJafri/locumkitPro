<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCardInformationTable extends Migration
{
    public function up()
    {
        Schema::create('user_card_information', function (Blueprint $table) {

            $table->integer('id');
            $table->string('card_type');
            $table->integer('card_number');
            $table->string('card_expiry');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_card_information');
    }
}
