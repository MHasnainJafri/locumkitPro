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
        Schema::table('private_user_job_actions', function (Blueprint $table) {
        // Drop the existing foreign key
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
        Schema::table('private_user_job_actions', function (Blueprint $table) {
        // Drop the updated foreign key
        $table->dropForeign(['job_post_id']);

        // Re-add the original foreign key without cascading
        $table->foreign('job_post_id')
              ->references('id')->on('job_posts');
    });
    }
};
