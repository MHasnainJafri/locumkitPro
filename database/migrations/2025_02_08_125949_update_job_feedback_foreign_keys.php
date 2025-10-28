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
        Schema::table('job_feedback', function (Blueprint $table) {
        // Drop the existing foreign key
        $table->dropForeign(['employer_id']);

        // Re-add the foreign key with cascading deletes
        $table->foreign('employer_id')
              ->references('id')->on('users')
              ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_feedback', function (Blueprint $table) {
        // Drop the updated foreign key
        $table->dropForeign(['employer_id']);

        // Re-add the original foreign key without cascading deletes
        $table->foreign('employer_id')
              ->references('id')->on('users');
    });
    }
};
