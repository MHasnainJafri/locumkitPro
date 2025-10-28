<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

 class AddUniqueConstraintToResourceKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_acl_package_resources', function (Blueprint $table) {
            $table->unique('resource_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_acl_package_resources', function (Blueprint $table) {
            $table->unique('resource_key');
        });
    }
};
