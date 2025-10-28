<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerPrivateJob extends Model
{
    use HasFactory;

    const STATUS_NEW_JOB = 0;
    const STATUS_NOTIFIED_BEFORE_DAY = 1;
    const STATUS_NOTIFIED_ON_JOB_DAY = 2;
    const STATUS_EXPENSE_NOTIFICATION_SEND = 3;
    const STATUS_JOB_COMPLETED = 4;
    const STATUS_JOB_ATTENDED = 5;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'job_date' => 'datetime',
    ];

    public function freelancer()
    {
        return $this->belongsTo(User::class, "freelancer_id");
    }
}