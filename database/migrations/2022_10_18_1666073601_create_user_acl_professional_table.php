<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAclProfessionalTable extends Migration
{
    public function up()
    {
        Schema::create('user_acl_professions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('description', 1000)->nullable();
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_acl_professions');
    }
}