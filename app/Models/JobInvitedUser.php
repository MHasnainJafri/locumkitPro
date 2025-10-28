<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobInvitedUser extends Model
{
    use HasFactory;

    const USER_TYPE_LIVE = "live_user";
    const USER_TYPE_PRIVATE = "private_user";

    /**
     * Get the parent invited user model (User or PrivateUser).
     */
    public function invited_user()
    {
        return $this->morphTo();
    }

    public function job()
    {
        return $this->belongsTo(JobPost::class, "job_post_id", "id");
    }
}