<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPackageDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('user_package_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->foreignId("user_acl_package_id")->constrained();
            $table->date('package_active_date');
            $table->date('package_expire_date');
            $table->integer('package_status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_package_details');
    }
}