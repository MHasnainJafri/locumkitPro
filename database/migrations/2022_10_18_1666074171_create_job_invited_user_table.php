<?php

use App\Models\JobInvitedUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobInvitedUserTable extends Migration
{
    public function up()
    {
        Schema::create('job_invited_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId("job_post_id")->constrained()->onDelete('cascade');
            $table->bigInteger("invited_user_id")->onDelete('cascade');
            $table->enum("invited_user_type", [JobInvitedUser::USER_TYPE_LIVE, JobInvitedUser::USER_TYPE_PRIVATE]);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_invited_users');
    }
}