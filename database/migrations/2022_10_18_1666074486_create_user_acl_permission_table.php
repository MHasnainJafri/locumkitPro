<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAclPermissionTable extends Migration
{
    public function up()
    {
        Schema::create('user_acl_permission', function (Blueprint $table) {
            $table->id();
            $table->string('permission');
            $table->integer('user_acl_resource_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_acl_permission');
    }
}
