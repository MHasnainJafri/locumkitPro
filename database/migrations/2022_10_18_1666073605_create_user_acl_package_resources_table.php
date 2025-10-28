<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAclPackageResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('user_acl_package_resources', function (Blueprint $table) {
            $table->id();
            $table->string('resource_key')->nullable();
            $table->string('resource_value')->nullable();
            $table->boolean('allow_count')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_acl_package_resources');
    }
}
