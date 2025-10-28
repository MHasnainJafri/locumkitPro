<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStoreListTable extends Migration
{
    public function up()
    {
        /* Schema::create('user_store_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->text('store_list');
            $table->timestamps();
        }); */
    }

    public function down()
    {
        /* Schema::dropIfExists('user_store_lists'); */
    }
}