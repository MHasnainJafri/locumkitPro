<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckInvitationTable extends Migration
{
    /* I think don't need this table */
    public function up()
    {
        Schema::create('check_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained();
            $table->integer('invi_status')->default('0');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('check_invitations');
    }
}