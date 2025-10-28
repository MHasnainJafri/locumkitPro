<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateUser extends Model
{
    use HasFactory;

    public function job_invitations()
    {
        return $this->morphMany(JobInvitedUser::class, 'invited_user');
    }

    public function private_user_job_actions()
    {
        return $this->hasMany(PrivateUserJobAction::class, "private_user_id", "id");
    }
}