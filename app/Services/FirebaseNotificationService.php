<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class FirebaseNotificationService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send a notification to multiple device tokens.
     *
     * @param array $tokens
     * @param string $title
     * @param string $body
     *
     * @return array
     */
    public function sendToTokens(array $tokens, string $title_ar, string $body_ar ,string $title_en, string $body_en ,$notification_id)
    {
        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No device tokens provided.'];
        }
        $auth = Auth::user();
        $firebaseNotification = FirebaseNotification::create($title_en, $body_en);

        foreach (array_chunk($tokens, 500) as $tokenChunk) {
            $message = CloudMessage::new()
                ->withNotification($firebaseNotification)
                ->withData([
                    'title_en' => $title_en,
                    'body_en' => $body_en,
                    'title_ar' => $title_ar,
                    'body_ar' => $body_ar,
                    'sender_name' => $auth->name,
                    'notification_id' => $notification_id,
                ]);
            $this->messaging->sendMulticast($message, $tokenChunk);
        }

        return ['success' => true, 'message' => 'Notification sent.'];
    }
}
