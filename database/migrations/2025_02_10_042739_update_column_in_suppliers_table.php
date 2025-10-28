<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Example: Changing the column to nullable
            $table->unsignedBigInteger('created_by_user_id')->nullable()->change();

            // Example: Adding a cascade on delete for the foreign key
            $table->dropForeign(['created_by_user_id']);
            $table->foreign('created_by_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_user_id')->nullable(false)->change();

            // Revert the foreign key constraint
            $table->dropForeign(['created_by_user_id']);
            $table->foreign('created_by_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
        
        });
    }
};
