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
        Schema::create('log_visitor', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->string('http_user_agent')->nullable();
            $table->string('http_accept_CHARset')->nullable();
            $table->string('http_accept_language')->nullable();
            $table->bigInteger("server_addr")->nullable();
            $table->bigInteger("remote_addr")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_visitor');
    }
};
