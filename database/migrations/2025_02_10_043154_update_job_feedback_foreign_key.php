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
            $table->dropForeign(['freelancer_id']);

            // Re-add the foreign key with ON DELETE CASCADE
            $table->foreign('freelancer_id')
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
        Schema::table('job_feedback', function (Blueprint $table) {
            $table->dropForeign(['freelancer_id']);

            // Re-add the original foreign key
            $table->foreign('freelancer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
        });
    }
};
