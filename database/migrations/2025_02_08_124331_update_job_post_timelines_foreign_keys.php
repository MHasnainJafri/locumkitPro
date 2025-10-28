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
        Schema::table('job_post_timelines', function (Blueprint $table) {
            // Drop the current foreign key constraint
            $table->dropForeign(['job_post_id']);
            
            // Re-add the foreign key with cascading deletes
            $table->foreign('job_post_id')
                  ->references('id')->on('job_posts')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_post_timelines', function (Blueprint $table) {
            // Drop the updated foreign key constraint
            $table->dropForeign(['job_post_id']);
    
            // Re-add the original foreign key constraint without cascading
            $table->foreign('job_post_id')
                  ->references('id')->on('job_posts');
        });
    }
};
