<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Services\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\{Notification, DeviceToken};
use Illuminate\Http\Request;

class SendPaymentNotification implements ShouldQueue
{
    protected FirebaseNotificationService $firebase;

    public function __construct(FirebaseNotificationService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function handle(PaymentCompleted $event): void
    {
        $invoice = $event->invoice;
        $user = $invoice->user;

        $deviceTokens = DeviceToken::where('user_id', 1)->pluck('token')->toArray();
        if (!empty($deviceTokens)) {

            // $auth = Auth::user();
            $notification = Notification::create([
                'user_id' => 1,
                'sender_id' => 3,
                'title_ar' => 'تم الدفع بنجاح',
                'body_ar' => 'تم دفع الفاتورة رقم #' . $invoice->id . ' بقيمة ' . $invoice->amount . ' ' . $invoice->currency->symbol,
                'title_en' => 'Payment Successful',
                'body_en' => 'Invoice #' . $invoice->id . ' has been paid successfully: ' . $invoice->amount . ' ' . $invoice->currency->symbol,
            ]);

            $this->firebase->sendToTokens(
                $deviceTokens,
                title_ar: 'تم الدفع بنجاح',
                body_ar: 'تم دفع الفاتورة رقم #' . $invoice->id . ' بقيمة ' . $invoice->amount . ' ' . $invoice->currency->symbol,
                title_en: 'Payment Successful',
                body_en: 'Invoice #' . $invoice->id . ' has been paid successfully: ' . $invoice->amount . ' ' . $invoice->currency->symbol,
                notification_id: $notification->id,
            );
        }
    }
}
