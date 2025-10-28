<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LocumlogbookNotification extends Notification
{
    use Queueable;

    protected $locumlogbook;
    
   
    public function __construct($locumlogbook)
    {
        $this->locumlogbook = $locumlogbook;
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
            ->subject('Reminder: Internal Referrals and Investigation Requests')
            ->greeting('Hello Admin,')
            ->line('You have a new reminder.')
            ->line('**Practice Name:** ' . $this->locumlogbook['practice_name'])
            ->line('**Date:** ' . $this->locumlogbook['date'])
            ->line('**Patient ID:** ' . ($this->locumlogbook['patient_id'] ?? 'N/A'))
            ->line('**Referred To:** ' . ($this->locumlogbook['referred_to'] ?? 'N/A'))
            ->line('**Issue Hand:** ' . ($this->locumlogbook['issue_hand'] ?? 'N/A'))
            ->line('**Action Required:** ' . ($this->locumlogbook['action_required'] ?? 'N/A'))
            ->line('**Reminder DateTime:** ' . ($this->locumlogbook['reminder_datetime'] ?? 'N/A'))
            ->line('**Notes:** ' . ($this->locumlogbook['notes'] ?? 'N/A'))
            ->line('**Is Completed:** ' . ($this->locumlogbook['is_compeleted'] ? 'Yes' : 'No'))
            ->line('Thank you for managing the platform effectively!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         'job_id' => $this->job->id,
    //         'job_title' => $this->job->title,
    //         'freelancer_name' => $this->freelancerName,
    //         'reason' => $this->reason,
    //     ];
    // }
}
