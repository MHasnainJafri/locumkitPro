<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAclPackagesTable extends Migration
{
    public function up()
    {
        Schema::create('user_acl_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->double('price');
            $table->string('description');
            $table->string('user_acl_package_resources_ids_list');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_acl_packages');
    }
}
