<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocumkitTeamInfoTable extends Migration
{
    public function up()
    {
        Schema::create('locumkit_team_info', function (Blueprint $table) {

            $table->integer('team_id');
            $table->string('name');
            $table->string('designation');
            $table->string('image');
            $table->integer('position')->default('0');
        });
    }

    public function down()
    {
        Schema::dropIfExists('locumkit_team_info');
    }
}
