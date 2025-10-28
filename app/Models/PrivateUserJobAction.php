<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateUserJobAction extends Model
{
    use HasFactory;

    const ACTION_WAITING = 1;
    const ACTION_APPLY = 2;
    const ACTION_ACCEPT = 3;
    const ACTION_DONE = 4;
    const ACTION_CANCEL = 5;

    protected $with = ['private_user'];

    public function private_user()
    {
        return $this->belongsTo(PrivateUser::class, "private_user_id");
    }
    public function employer()
    {
        return $this->belongsTo(User::class, "employer_id");
    }

    public function job()
    {
        return $this->belongsTo(JobPost::class, "job_post_id", "id");
    }
}