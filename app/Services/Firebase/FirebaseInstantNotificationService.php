<?php

namespace App\Services\Firebase;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;

class FirebaseInstantNotificationService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send notification to ALL active devices of a user
     */
    public function sendToUser(
        int $userId,
        string $title_en,
        string $body_en,
        string $title_ar,
        string $body_ar,
        array $additionalData = []
    ): array {
        // Get ALL active tokens for this user
        $tokens = DeviceToken::where('user_id', $userId)
            ->active()
            ->recent()
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::warning('No active devices found for user', ['user_id' => $userId]);
            return ['success' => false, 'message' => 'No active devices found for user.'];
        }

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        // Send to EACH token individually
        foreach ($tokens as $token) {
            $result = $this->sendToToken($token, $title_en, $body_en, $title_ar, $body_ar, $additionalData);
            $results[$this->maskToken($token)] = $result;

            if ($result['success']) {
                $successCount++;
                // Update last used timestamp for successful tokens
                DeviceToken::where('token', $token)->update(['last_used_at' => now()]);
            } else {
                $failureCount++;
            }
        }

        Log::info('Notification sent to user devices', [
            'user_id' => $userId,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'total_tokens' => count($tokens)
        ]);

        return [
            'success' => $successCount > 0,
            'message' => "Sent to {$successCount} devices, failed for {$failureCount} devices.",
            'results' => $results
        ];
    }

    /**
     * Send to specific token (internal use only)
     */
    protected function sendToToken(
        string $token,
        string $title_en,
        string $body_en,
        string $title_ar,
        string $body_ar,
        array $additionalData = []
    ): array {
        if (empty($token)) {
            return ['success' => false, 'message' => 'No device token provided.'];
        }

        try {
            $notification = Notification::create($title_en, $body_en)
                ->withTitle($title_ar)
                ->withBody($body_ar);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData(array_merge([
                    'title_en' => $title_en,
                    'body_en' => $body_en,
                    'title_ar' => $title_ar,
                    'body_ar' => $body_ar,
                    'timestamp' => now()->toISOString(),
                    'type' => 'instant_notification',
                ], $additionalData));

            $this->messaging->send($message);

            return ['success' => true, 'message' => 'Notification sent successfully.'];

        } catch (\Exception $e) {
            Log::warning('Failed to send to token', [
                'token' => $this->maskToken($token),
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => 'Failed to send notification.'];
        }
    }

    /**
     * Mask token for secure logging
     */
    protected function maskToken(string $token): string
    {
        if (strlen($token) <= 20) return $token;
        return substr($token, 0, 10) . '...' . substr($token, -10);
    }

    // ============ NOTIFICATION METHODS (UPDATED FOR USER ID) ============

    /**
     * TRIP ACCEPTANCE NOTIFICATIONS
     */
    public function sendTripAcceptedToDriver(int $userId, string $tripId, string $passengerName)
    {
        $title_en = "Trip Accepted";
        $body_en = "You have accepted trip #{$tripId} for {$passengerName}";
        
        $title_ar = "تم قبول الرحلة";
        $body_ar = "لقد قبلت الرحلة رقم #{$tripId} للراكب {$passengerName}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_accepted_driver',
            'trip_id' => $tripId,
            'passenger_name' => $passengerName
        ]);
    }

    public function sendTripAcceptedToPassenger(int $userId, string $tripId, string $driverName, string $vehicleInfo)
    {
        $title_en = "Driver Assigned";
        $body_en = "Driver {$driverName} with {$vehicleInfo} has accepted your trip #{$tripId}";
        
        $title_ar = "تم تعيين سائق";
        $body_ar = "السائق {$driverName} ب{$vehicleInfo} قبل رحلتك رقم #{$tripId}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_accepted_passenger',
            'trip_id' => $tripId,
            'driver_name' => $driverName,
            'vehicle_info' => $vehicleInfo
        ]);
    }

    /**
     * TRIP START NOTIFICATIONS
     */
    public function sendTripStartedToDriver(int $userId, string $tripId, float $deductionAmount)
    {
        $amountFormatted = number_format($deductionAmount, 2);
        
        $title_en = "Trip Started";
        $body_en = "Trip #{$tripId} has started. {$amountFormatted} deducted from wallet";
        
        $title_ar = "بدأت الرحلة";
        $body_ar = "بدأت الرحلة رقم #{$tripId}. تم خصم {$amountFormatted} من المحفظة";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_started_driver',
            'trip_id' => $tripId,
            'deduction_amount' => $amountFormatted
        ]);
    }

    public function sendTripStartedToPassenger(int $userId, string $tripId, string $driverName)
    {
        $title_en = "Trip Started";
        $body_en = "Driver {$driverName} has started your trip #{$tripId}";
        
        $title_ar = "بدأت الرحلة";
        $body_ar = "بدأ السائق {$driverName} رحلتك رقم #{$tripId}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_started_passenger',
            'trip_id' => $tripId,
            'driver_name' => $driverName
        ]);
    }

    /**
     * TRIP CANCELLATION NOTIFICATIONS
     */
    public function sendTripCancelledByDriver(int $userId, string $tripId, string $cancelledBy)
    {
        $title_en = "Trip Cancelled";
        $body_en = "Trip #{$tripId} has been cancelled by {$cancelledBy}";
        
        $title_ar = "تم إلغاء الرحلة";
        $body_ar = "تم إلغاء الرحلة رقم #{$tripId} بواسطة {$cancelledBy}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_cancelled',
            'trip_id' => $tripId,
            'cancelled_by' => $cancelledBy
        ]);
    }

    public function sendTripCancellationRefund(int $userId, string $tripId, float $refundAmount)
    {
        $amountFormatted = number_format($refundAmount, 2);
        
        $title_en = "Refund Processed";
        $body_en = "{$amountFormatted} has been refunded to your wallet for cancelled trip #{$tripId}";
        
        $title_ar = "تم استرداد المبلغ";
        $body_ar = "تم إعادة {$amountFormatted} إلى محفظتك للرحلة الملغاة رقم #{$tripId}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_refund',
            'trip_id' => $tripId,
            'refund_amount' => $amountFormatted
        ]);
    }

    /**
     * TRIP COMPLETION NOTIFICATIONS
     */
    public function sendTripCompletedToDriver(int $userId, string $tripId, float $finalFare, float $earnings)
    {
        $fareFormatted = number_format($finalFare, 2);
        $earningsFormatted = number_format($earnings, 2);
        
        $title_en = "Trip Completed";
        $body_en = "Trip #{$tripId} completed! Fare: {$fareFormatted}, Your earnings: {$earningsFormatted}";
        
        $title_ar = "تم إنهاء الرحلة";
        $body_ar = "تم إنهاء الرحلة رقم #{$tripId}! الأجرة: {$fareFormatted}, أرباحك: {$earningsFormatted}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_completed_driver',
            'trip_id' => $tripId,
            'final_fare' => $fareFormatted,
            'earnings' => $earningsFormatted
        ]);
    }

    public function sendTripCompletedToPassenger(int $userId, string $tripId, float $finalFare, string $driverName)
    {
        $fareFormatted = number_format($finalFare, 2);
        
        $title_en = "Trip Completed";
        $body_en = "Your trip #{$tripId} with {$driverName} is complete. Fare: {$fareFormatted}";
        
        $title_ar = "تم إنهاء الرحلة";
        $body_ar = "رحلتك رقم #{$tripId} مع {$driverName} اكتملت. الأجرة: {$fareFormatted}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_completed_passenger',
            'trip_id' => $tripId,
            'final_fare' => $fareFormatted,
            'driver_name' => $driverName
        ]);
    }

    /**
     * WALLET NOTIFICATIONS
     */
    public function sendWalletUpdateNotification(int $userId, float $amount, string $transactionType, string $tripId = null)
    {
        $amountFormatted = number_format($amount, 2); 

        if ($transactionType === 'deduction') {
            $title_en = "Wallet Deduction";
            $body_en = "{$amountFormatted} deducted from your wallet for the trip";
            $title_ar = "خصم من المحفظة";
            $body_ar = "تم خصم {$amountFormatted} من محفظتك للرحلة";
        } else {
            $title_en = "Wallet Recharge";
            $body_en = "{$amountFormatted} added to your wallet";
            $title_ar = "إعادة شحن المحفظة";
            $body_ar = "تم إضافة {$amountFormatted} إلى محفظتك";
        }

        if ($tripId) {
            $body_en .= " (Trip ID: {$tripId})";
            $body_ar .= " (رقم الرحلة: {$tripId})";
        }

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'wallet_update',
            'transaction_type' => $transactionType,
            'amount' => $amountFormatted,
            'trip_id' => $tripId
        ]);
    }

    /**
     * DRIVER PROXIMITY NOTIFICATIONS
     */
    public function sendDriverProximityAlert(int $userId, string $driverName, string $distance, string $eta)
    {
        $title_en = "Driver Nearby";
        $body_en = "Driver {$driverName} is {$distance} away. ETA: {$eta}";
        
        $title_ar = "السائق قريب";
        $body_ar = "السائق {$driverName} على بعد {$distance}. وقت الوصول: {$eta}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'driver_proximity',
            'driver_name' => $driverName,
            'distance' => $distance,
            'eta' => $eta
        ]);
    }

    /**
     * DRIVER ARRIVAL NOTIFICATIONS
     */
    public function sendDriverArrivalTime(int $userId, string $driverName, int $minutes, string $vehicleInfo)
    {
        $title_en = "Driver Arrival";
        $body_en = "Driver {$driverName} with {$vehicleInfo} will arrive in {$minutes} minutes";
        
        $title_ar = "وصول السائق";
        $body_ar = "سوف يصل السائق {$driverName} ب{$vehicleInfo} خلال {$minutes} دقائق";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'driver_arrival',
            'driver_name' => $driverName,
            'minutes' => $minutes,
            'vehicle_info' => $vehicleInfo
        ]);
    }

    public function sendDriverArrived(int $userId, string $driverName, string $vehiclePlate)
    {
        $title_en = "Driver Arrived";
        $body_en = "Driver {$driverName} has arrived with vehicle {$vehiclePlate}";
        
        $title_ar = "وصل السائق";
        $body_ar = "وصل السائق {$driverName} بالمركبة {$vehiclePlate}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'driver_arrived',
            'driver_name' => $driverName,
            'vehicle_plate' => $vehiclePlate
        ]);
    }

    /**
     * PAYMENT NOTIFICATIONS
     */
    public function sendPaymentSuccess(int $userId, string $tripId, float $amount, string $paymentMethod)
    {
        $amountFormatted = number_format($amount, 2);
        
        $title_en = "Payment Successful";
        $body_en = "Payment of {$amountFormatted} for trip #{$tripId} via {$paymentMethod} was successful";
        
        $title_ar = "تم الدفع بنجاح";
        $body_ar = "تم دفع {$amountFormatted} للرحلة رقم #{$tripId} عبر {$paymentMethod} بنجاح";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'payment_success',
            'trip_id' => $tripId,
            'amount' => $amountFormatted,
            'payment_method' => $paymentMethod
        ]);
    }

    public function sendPaymentFailed(int $userId, string $tripId, float $amount, string $reason)
    {
        $amountFormatted = number_format($amount, 2);
        
        $title_en = "Payment Failed";
        $body_en = "Payment of {$amountFormatted} for trip #{$tripId} failed: {$reason}";
        
        $title_ar = "فشل في الدفع";
        $body_ar = "فشل دفع {$amountFormatted} للرحلة رقم #{$tripId}: {$reason}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'payment_failed',
            'trip_id' => $tripId,
            'amount' => $amountFormatted,
            'reason' => $reason
        ]);
    }

    /**
     * RATING NOTIFICATIONS
     */
    public function sendRatingReceived(int $userId, string $tripId, int $rating, string $comment = '')
    {
        $title_en = "New Rating Received";
        $body_en = "You received {$rating} stars for trip #{$tripId}" . ($comment ? ": {$comment}" : "");
        
        $title_ar = "تقييم جديد";
        $body_ar = "لقد حصلت على {$rating} نجوم للرحلة رقم #{$tripId}" . ($comment ? ": {$comment}" : "");

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'rating_received',
            'trip_id' => $tripId,
            'rating' => $rating,
            'comment' => $comment
        ]);
    }

    /**
     * GENERAL SYSTEM NOTIFICATIONS
     */
    public function sendSystemAlert(int $userId, string $title, string $message, string $alertType = 'info')
    {
        $title_ar = $title;
        $body_ar = $message;

        return $this->sendToUser($userId, $title, $message, $title_ar, $body_ar, [
            'type' => 'system_alert',
            'alert_type' => $alertType
        ]);
    }

    /**
     * CUSTOM NOTIFICATION
     */
    public function sendCustomInstantNotification(
        int $userId,
        string $title_en,
        string $body_en,
        string $title_ar,
        string $body_ar,
        string $type = 'custom',
        array $customData = []
    ) {
        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, array_merge([
            'type' => $type,
        ], $customData));
    }

    /**
     * LEGACY METHOD: Send to single token (for backward compatibility)
     */
    public function sendToSingleToken(
        string $token,
        string $title_en,
        string $body_en,
        string $title_ar,
        string $body_ar,
        array $additionalData = []
    ) {
        return $this->sendToToken($token, $title_en, $body_en, $title_ar, $body_ar, $additionalData);
    }


    public function sendTripRequestToDriver(int $userId, string $tripId, string $pickupName, string $dropoffName)
    {
        $title_en = "New Trip Request";
        $body_en = "New trip request from {$pickupName} to {$dropoffName}";
        
        $title_ar = "طلب رحلة جديد";
        $body_ar = "طلب رحلة جديد من {$pickupName} إلى {$dropoffName}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_request',
            'trip_id' => $tripId,
            'pickup_name' => $pickupName,
            'dropoff_name' => $dropoffName,
            'action' => 'accept_trip'
        ]);
    }


    public function sendTripCancelledByUser(int $userId, string $tripId, string $userName)
    {
        $title_en = "Trip Cancelled";
        $body_en = "Trip #{$tripId} has been cancelled by {$userName}";
        
        $title_ar = "تم إلغاء الرحلة";
        $body_ar = "تم إلغاء الرحلة رقم #{$tripId} بواسطة {$userName}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_cancelled_by_user',
            'trip_id' => $tripId,
            'user_name' => $userName
        ]);
    }


     /**
     * TRIP STATUS UPDATES FOR USERS
     */
    public function sendTripStatusUpdateToUser(int $userId, string $tripId, string $status, string $driverName = null)
    {
        $statusMessages = [
            'pending' => ['Trip Request Sent', 'تم إرسال طلب الرحلة'],
            'accepted' => ['Trip Accepted', 'تم قبول الرحلة'],
            'in_progress' => ['Trip Started', 'بدأت الرحلة'],
            'completed' => ['Trip Completed', 'تم إنهاء الرحلة'],
            'cancelled' => ['Trip Cancelled', 'تم إلغاء الرحلة']
        ];

        $message = $statusMessages[$status] ?? ['Status Updated', 'تم تحديث الحالة'];
        
        $title_en = $message[0];
        $title_ar = $message[1];

        $body_en = "Trip #{$tripId} status updated to: {$status}";
        $body_ar = "تم تحديث حالة الرحلة رقم #{$tripId} إلى: {$status}";

        if ($driverName && $status === 'accepted') {
            $body_en .= " by driver {$driverName}";
            $body_ar .= " بواسطة السائق {$driverName}";
        }

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'trip_status_update',
            'trip_id' => $tripId,
            'status' => $status,
            'driver_name' => $driverName
        ]);
    }


     /**
     * DRIVER LOCATION UPDATES FOR USERS
     */
    public function sendDriverLocationUpdate(int $userId, string $tripId, string $driverName, string $distance, string $eta)
    {
        $title_en = "Driver Update";
        $body_en = "Driver {$driverName} is {$distance} away. ETA: {$eta}";
        
        $title_ar = "تحديث السائق";
        $body_ar = "السائق {$driverName} على بعد {$distance}. وقت الوصول: {$eta}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'driver_location_update',
            'trip_id' => $tripId,
            'driver_name' => $driverName,
            'distance' => $distance,
            'eta' => $eta
        ]);
    }

    /**
     * PAYMENT REMINDERS
     */
    public function sendPaymentReminder(int $userId, string $tripId, float $amount)
    {
        $amountFormatted = number_format($amount, 2);
        
        $title_en = "Payment Pending";
        $body_en = "Please complete payment of {$amountFormatted} for trip #{$tripId}";
        
        $title_ar = "دفع معلق";
        $body_ar = "يرجى إكمال دفع {$amountFormatted} للرحلة رقم #{$tripId}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'payment_reminder',
            'trip_id' => $tripId,
            'amount' => $amountFormatted
        ]);
    }

     /**
     * RATING REMINDERS
     */
    public function sendRatingReminder(int $userId, string $tripId, string $driverName)
    {
        $title_en = "Rate Your Trip";
        $body_en = "How was your trip with {$driverName}? Rate your experience!";
        
        $title_ar = "قيم رحلتك";
        $body_ar = "كيف كانت رحلتك مع {$driverName}؟ قيم تجربتك!";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'rating_reminder',
            'trip_id' => $tripId,
            'driver_name' => $driverName
        ]);
    }

     /**
     * PROMOTIONAL NOTIFICATIONS
     */
    public function sendPromoNotification(int $userId, string $promoCode, float $discount, string $expiryDate)
    {
        $discountFormatted = number_format($discount, 2);
        
        $title_en = "New Promotion!";
        $body_en = "Use code {$promoCode} for {$discountFormatted} off your next ride. Valid until {$expiryDate}";
        
        $title_ar = "عرض جديد!";
        $body_ar = "استخدم الرمز {$promoCode} للحصول على خصم {$discountFormatted} على رحلتك القادمة. ساري حتى {$expiryDate}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'promo_notification',
            'promo_code' => $promoCode,
            'discount' => $discountFormatted,
            'expiry_date' => $expiryDate
        ]);
    }


      /**
     * WALLET LOW BALANCE
     */
    public function sendLowBalanceWarning(int $userId, float $currentBalance, float $minimumRequired)
    {
        $currentFormatted = number_format($currentBalance, 2);
        $requiredFormatted = number_format($minimumRequired, 2);
        
        $title_en = "Low Wallet Balance";
        $body_en = "Your wallet balance is {$currentFormatted}. Please recharge to continue using our services. Minimum required: {$requiredFormatted}";
        
        $title_ar = "رصيد منخفض";
        $body_ar = "رصيد محفظتك هو {$currentFormatted}. يرجى إعادة الشحن لمواصلة استخدام خدماتنا. الحد الأدنى المطلوب: {$requiredFormatted}";

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'low_balance_warning',
            'current_balance' => $currentFormatted,
            'minimum_required' => $requiredFormatted
        ]);
    }

     /**
     * SECURITY NOTIFICATIONS
     */
    public function sendSecurityAlert(int $userId, string $alertType, string $deviceInfo = null)
    {
        $alerts = [
            'new_device' => [
                'en' => 'New device login detected',
                'ar' => 'تم اكتشاف تسجيل دخول من جهاز جديد'
            ],
            'suspicious_activity' => [
                'en' => 'Suspicious activity detected',
                'ar' => 'تم اكتشاف نشاط مشبوه'
            ],
            'password_changed' => [
                'en' => 'Password changed successfully',
                'ar' => 'تم تغيير كلمة المرور بنجاح'
            ]
        ];

        $alert = $alerts[$alertType] ?? $alerts['suspicious_activity'];
        
        $title_en = "Security Alert";
        $body_en = $alert['en'];
        $title_ar = "تنبيه أمني";
        $body_ar = $alert['ar'];

        if ($deviceInfo) {
            $body_en .= " from {$deviceInfo}";
            $body_ar .= " من {$deviceInfo}";
        }

        return $this->sendToUser($userId, $title_en, $body_en, $title_ar, $body_ar, [
            'type' => 'security_alert',
            'alert_type' => $alertType,
            'device_info' => $deviceInfo
        ]);
    }
}