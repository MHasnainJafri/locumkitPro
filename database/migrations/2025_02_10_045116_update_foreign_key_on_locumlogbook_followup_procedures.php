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
        Schema::table('locumlogbook_followup_procedures', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            // Re-add the foreign key with ON DELETE CASCADE
            $table->foreign('user_id')
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
        Schema::table('locumlogbook_followup_procedures', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            // Re-add the original foreign key without cascade
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
        });
    }
};
