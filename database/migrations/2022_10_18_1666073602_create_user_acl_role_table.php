<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAclRoleTable extends Migration
{
    public function up()
    {
        Schema::create('user_acl_roles', function (Blueprint $table) {
            $table->id("id")->comment("1=Administrator,2=Locum,3=Employer,...");
            $table->string("name")->nullable()->comment('Administrator, Locum, Employer, ...');
            $table->string('description')->nullable();
            $table->boolean("is_public")->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_acl_roles');
    }
}