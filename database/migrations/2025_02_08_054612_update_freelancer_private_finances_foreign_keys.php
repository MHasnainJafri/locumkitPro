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
        Schema::table('freelancer_private_finances', function (Blueprint $table) {
        // Drop existing foreign key constraints
        $table->dropForeign(['freelancer_id']);
        $table->dropForeign(['freelancer_private_job_id']);
        
        // Re-add foreign key constraints with cascading deletes
        $table->foreign('freelancer_id')
              ->references('id')->on('users')
              ->onDelete('cascade');
        $table->foreign('freelancer_private_job_id')
              ->references('id')->on('freelancer_private_jobs') // Assuming the table name
              ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancer_private_finances', function (Blueprint $table) {
        // Drop the updated foreign keys
        $table->dropForeign(['freelancer_id']);
        $table->dropForeign(['freelancer_private_job_id']);
        
        // Re-add the original foreign key constraints without cascading
        $table->foreign('freelancer_id')
              ->references('id')->on('users');
        $table->foreign('freelancer_private_job_id')
              ->references('id')->on('freelancer_private_jobs');
    });
    }
};
