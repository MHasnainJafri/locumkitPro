<?php

// app/Services/NotificationService.php

namespace App\Services;

use App\Models\JobFeedback;
use App\Models\SendNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class NotificationService
{
    public static function sendNotification($job_post_id, $recipientId, $message, $sendDate)
    {

        SendNotification::create([
            'job_post_id' => $job_post_id,
            'recipient_id' => $recipientId,
            'message' => $message,
            'send_date' => $sendDate,
        ]);
    }
    public static function send_feedback_Notification()
    {



    }

}
