<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAclResourceTable extends Migration
{
    public function up()
    {
        Schema::create('user_acl_resource', function (Blueprint $table) {
            $table->id();
            $table->string('resource')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_acl_resource');
    }
}
