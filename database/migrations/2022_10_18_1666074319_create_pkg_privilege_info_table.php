<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkgPrivilegeInfoTable extends Migration
{
    public function up()
    {
        Schema::create('pkg_privilege_infos', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->boolean('bronze')->default(false);
            $table->boolean('silver')->default(false);
            $table->boolean('gold')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pkg_privilege_infos');
    }
}