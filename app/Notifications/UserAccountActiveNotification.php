<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAccountActiveNotification extends Notification
{
    use Queueable;

    protected $isActive;

    /**
     * Create a new notification instance.
     *
     * @param bool $isActive
     */
    public function __construct(bool $isActive)
    {
        $this->isActive = $isActive;
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
        if ($this->isActive) {
            return (new MailMessage)
                ->subject('Your Account is Active')
                ->greeting('Hello ' . $notifiable->firstname . '!')
                ->line('We are pleased to inform you that your account is now active.')
                ->line('You can now access all the features of our platform.')
                ->action('Login to Your Account', url('/login'))
                ->line('Thank you for being with us!');
        } else {
            return (new MailMessage)
                ->subject('Your Account is Deactivated')
                ->greeting('Hello ' . $notifiable->firstname . '!')
                ->line('We regret to inform you that your account has been deactivated.')
                ->line('If you believe this is a mistake, please contact our support team.')
                ->line('Thank you for understanding.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'isActive' => $this->isActive,
        ];
    }
}