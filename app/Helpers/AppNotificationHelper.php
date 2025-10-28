<?php

namespace App\Helpers;

use App\Models\MobileNotification;
use Exception;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AppNotificationHelper
{
    
    // protected $firebaseService;

    // public function __construct(FirebaseService $firebaseService)
    // {
    //     $this->firebaseService = $firebaseService;
    // }
    
    
    /****Send Push Notification on Mobile using Firebase Cloud Messaging ***/
    public function old_notification($job_id, $message, $title, $user_id, $types, $token_id = null)
    {
        if (!$token_id) {
            $token_id = $this->getTokenByID($user_id);
            if (is_null($token_id)) {
                return;
            }
        }
        if (is_null($title) || $title == "") {
            $title = ucwords(config('app.name') . " Alert");
        }

        $apiKey = config('app.firebase_cm_api_key');

        $fields = array(
            'to'  => $token_id,
            "token" => $token_id,
            "notification" => [
                "title" => $title,
                "body" => $message,
                "sound" => "default",
                "click_action" => "FCM_PLUGIN_ACTIVITY"
            ],
            'data' => array(
                "message" => $message,
                "title" => $title,
                "jobid" => $job_id,
                "userid" => $user_id,
                "type" => $types
            ),
            "android" => [
                "notification" => [
                    "icon" => "fcm_push_icon",
                    "click_action" => "FCM_PLUGIN_ACTIVITY"
                ]
            ]
        );
        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$apiKey}",
                'Content-Type' => 'application/json'
            ])->withoutVerifying()->post('https://fcm.googleapis.com/fcm/send', $fields);

            $this->saveResultLog($response->body(), $user_id, $token_id, $fields);
            $status = 0;
            if ($response->successful()) {
                if ($response->json("success") == 1) {
                    $status = 1;
                } elseif ($response->json("failure") == 1) {
                    $status = 2;
                }
            }

            //update status notification
            $this->updateTokenStatus($status, $token_id);
        } catch (Exception) {
        }
    }
    
    
    
    /****Send Push Notification on Mobile using Firebase Cloud Messaging ***/
    public function notification($job_id, $message, $title, $user_id, $types, $token_id = null)
    {
        if (!$token_id) {
            $token_id = $this->getTokenByID($user_id);
            if (is_null($token_id)) {
                return;
            }
        }
        if (is_null($title) || $title == "") {
            $title = ucwords(config('app.name') . " Alert");
        }

        $field = [
            'message' => $message,
            'title' => $title,
            'jobid' => $job_id,
            'userid' => $user_id,
            'type' => $types
        ];

        try {
            Log::info('Sending FCM to token: ' . $token_id);
            
            $firebaseService = app(FirebaseService::class);

            $sendResult = $firebaseService->sendNotification(
                $token_id,
                $title,
                $message,
                $field
            );

            // Log the result
            Log::info('FCM response: ' . json_encode($sendResult));
            $this->saveResultLog(json_encode($sendResult), $user_id, $token_id, $data);

            $status = 0;
            if ($sendResult) {
                $status = 1;
            }

            // Update status notification
            $this->updateTokenStatus($status, $token_id);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
    
    /****Send Push Notification on Mobile using Firebase Cloud Messaging ***/
    public function Privatenotification($job_id, $message, $title, $user_id, $types, $token_id = null,  $job_title, $employer_name, $job_rate, $job_location, $job_type)
    {
        if (!$token_id) {
            $token_id = $this->getTokenByID($user_id);
            if (is_null($token_id)) {
                return;
            }
        }
        if (is_null($title) || $title == "") {
            $title = ucwords(config('app.name') . " Alert");
        }

        $apiKey = config('app.firebase_cm_api_key');

        $fields = array(
            'to'  => $token_id,
            "token" => $token_id,
            "notification" => [
                "title" => $title,
                "body" => $message,
                "sound" => "default",
                "click_action" => "FCM_PLUGIN_ACTIVITY"
            ],
            'data' => array(
                "message" => $message,
                "title" => $title,
                "jobid" => $job_id,
                "userid" => $user_id,
                "type" => $types,
                "job_title" => $job_title,
                "employer_name" => $employer_name,
                "job_rate" => $job_rate,
                "location" => $job_location,
                "job_type" => $job_type
            ),
            "android" => [
                "notification" => [
                    "icon" => "fcm_push_icon",
                    "click_action" => "FCM_PLUGIN_ACTIVITY"
                ]
            ]
        );
        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$apiKey}",
                'Content-Type' => 'application/json'
            ])->withoutVerifying()->post('https://fcm.googleapis.com/fcm/send', $fields);

            $this->saveResultLog($response->body(), $user_id, $token_id, $fields);
            $status = 0;
            if ($response->successful()) {
                if ($response->json("success") == 1) {
                    $status = 1;
                } elseif ($response->json("failure") == 1) {
                    $status = 2;
                }
            }

            //update status notification
            $this->updateTokenStatus($status, $token_id);
        } catch (Exception) {
        }
    }
    
    /****Insert Mobile Token in Mobile Notification table for  Mobile push notification ***/
    public function insert_notification_data($userId, $token_id)
    {
        $this->deleteToken($token_id);

        $insert_user_data = MobileNotification::create([
            "user_id" => $userId,
            "token_id" => $token_id,
            "status" => 0,
        ]);

        return $insert_user_data;
    }
    /****Get Token By User Id ***/
    public function getTokenByID($user_id)
    {
        $tokenID = MobileNotification::where("user_id", $user_id)->latest()->first();
        if ($tokenID) {
            return $tokenID->token_id;
        }
        return null;
    }
    /****Delete Token AND User Id If User is logged out From mobile***/
    public function deleteToken($token)
    {
        MobileNotification::where("token_id", $token)->delete();
    }
    /****Update Token AND User Id If User is logged out From mobile***/
    public function updateTokenStatus($status, $token)
    {
        MobileNotification::where("token_id", $token)->update([
            "status" => $status
        ]);
    }

    private function saveResultLog(string|bool $response, $user_id, $token_id, array $fields)
    {
        $fields_string = json_encode($fields);
        Storage::append("logs/notifications.log", now()->toDateTimeString() . ": " . $response . "| user_id={$user_id}, token_id={$token_id}, fields={$fields_string}");
    }
}
