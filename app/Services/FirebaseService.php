<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $serviceAccountPath = storage_path('firebase-credentials.json');

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($token, $title, $body, $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification)
            ->withData($data)
            ->withAndroidConfig([
                'notification' => [
                    'sound' => 'default',
                    'click_action' => 'FCM_PLUGIN_ACTIVITY',
                    'icon' => 'fcm_push_icon',
                ],
            ]);

        return $this->messaging->send($message);
    }
}
