<?php

namespace App\Jobs;

use App\Models\EmailNotfiction;
use App\Models\EmailUserNotfiction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Notifications\SystemWideNotification;

class SendBatchEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        // Use provided data or default values
        $title = $this->data['title'] ?? "System Wide Notification";
        $body = $this->data['body'] ?? "This is a system wide notification for all users";

        // Create main notification record
        $emailNotfiction = EmailNotfiction::create([
            'title' => $title,
            'body' => $body,
        ]);

        Log::info("Starting broadcast notification process - ID: {$emailNotfiction->id}");

        $totalUsers = 0;
        $successCount = 0;
        $failureCount = 0;

        // Process users in chunks
        User::chunk(100, function ($users) use ($emailNotfiction, &$totalUsers, &$successCount, &$failureCount) {
            foreach ($users as $user) {
                $totalUsers++;
                // Create user notification record
                $notification = EmailUserNotfiction::create([
                    'email_id' => $emailNotfiction->id, // This line is correct!
                    'user_id' => $user->id,
                    'status' => "pending",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                try {
                    // Send notification to user
                    // $this->sendNotificationToUser($user, $emailNotfiction);
                    $user->notify(new SystemWideNotification(
                        $emailNotfiction->id,
                        $emailNotfiction->title,
                        $emailNotfiction->body
                    ));
                    // Update notification status
                    $notification->update(['status' => 'success']);
                    $successCount++;

                    Log::info("Notification sent successfully to user: {$user->id}");

                } catch (\Exception $e) {
                    $notification->update(['status' => 'failed']);
                    $failureCount++;
                    Log::error("Failed to send notification to user {$user->id}: " . $e->getMessage());
                }
            }
        });
        Log::info("Broadcast notification process completed - Total: {$totalUsers}, Success: {$successCount}, Failed: {$failureCount}");
    }
    protected function sendNotificationToUser($user, $emailNotfiction): void
    {
        $user->notify(new SystemWideNotification(
            $emailNotfiction->id,
            $emailNotfiction->title,
            $emailNotfiction->body
        ));
        // Simulate random failure (remove this in production)
        // if (rand(0, 10) < 2) {
        //     throw new \Exception("Simulated random failure");
        // }
    }
}
