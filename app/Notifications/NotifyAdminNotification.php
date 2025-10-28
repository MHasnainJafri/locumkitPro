<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyAdminNotification extends Notification
{
    use Queueable;

    protected $jobPost;

    /**
     * Create a new notification instance.
     */
    public function __construct($jobPost)
    {
        $this->jobPost = $jobPost; // Pass the job post details
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
            ->subject('New Job Posted')
            ->greeting('Hello Admin,')
            ->line('A new job has been posted with the following details:')
            ->line('**Job Title:** ' . $this->jobPost['job_title'])
            ->line('**Job Date:** ' . $this->jobPost['job_date'])
            ->line('**Start Time:** ' . $this->jobPost['job_start_time'])
            ->line('**Description:** ' . $this->jobPost['job_post_desc'])
            ->line('**Rate:** $' . $this->jobPost['job_rate'])
            ->line('**Location:** ' . $this->jobPost['job_address'])
            ->line('**Region:** ' . $this->jobPost['job_region'])
            ->line('**ZIP Code:** ' . $this->jobPost['job_zip'])
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'job_title' => $this->jobPost['job_title'],
            'job_date' => $this->jobPost['job_date'],
            'job_start_time' => $this->jobPost['job_start_time'],
            'job_rate' => $this->jobPost['job_rate'],
        ];
    }
}
