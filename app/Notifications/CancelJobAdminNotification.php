<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelJobAdminNotification extends Notification
{
    use Queueable;

    protected $job;
    protected $freelancerName;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @param object $job
     * @param string $freelancerName
     * @param string $reason
     */
    public function __construct($job, $freelancerName, $reason)
    {
        $this->job = $job;
        $this->freelancerName = $freelancerName;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Job Cancellation Notification')
            ->greeting('Hello Admin,')
            ->line('A job has been canceled.')
            ->line('**Job Title:** ' . $this->job->title)
            ->line('**Freelancer:** ' . $this->freelancerName)
            ->line('**Reason for Cancellation:** ' . $this->reason)
            ->line('Please review the details and take any necessary action.')
            ->line('Thank you for managing the platform effectively!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'freelancer_name' => $this->freelancerName,
            'reason' => $this->reason,
        ];
    }
}
