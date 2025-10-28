<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRegisterAdminNotification extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
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
            ->subject('New User Registration')
            ->greeting('Hello Admin,')
            ->line('A new user has registered on the platform.')
            ->line('**Name:** ' . $this->user->firstname . ' ' . $this->user->lastname)
            ->line('**Email:** ' . $this->user->email)
            ->line('**Role:** ' . $this->getRoleName($this->user->user_acl_role_id))
            ->line('Thank you for using our application!');
            // ->action('View User', url('/admin/users/' . $this->user->id)); // Adjust the URL as per your routing
    }

    /**
     * Helper to get the role name.
     */
    protected function getRoleName($roleId)
    {
        $roles = [
            1 => 'Employer',
            2 => 'Freelancer',
            3 => 'Admin',
        ];
        return $roles[$roleId] ?? 'Unknown Role';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'name' => $this->user->firstname . ' ' . $this->user->lastname,
            'email' => $this->user->email,
            'role' => $this->user->user_acl_role_id,
        ];
    }
}
