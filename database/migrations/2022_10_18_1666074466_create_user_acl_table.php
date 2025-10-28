<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAclTable extends Migration
{
    public function up()
    {
        Schema::create('user_acl', function (Blueprint $table) {
            $table->id();
            $table->integer('user_acl_permission_id');
            $table->integer('user_acl_role_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_acl');
    }
}
