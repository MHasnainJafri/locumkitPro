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
        Schema::create('log_url', function (Blueprint $table) {
            $table->id();
            $table->dateTime('visit_at')->nullable();
            $table->foreignId('log_url_info_id')->nullable();
            $table->foreignId('log_visitor_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_url');
    }
};
