<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendBatchEmailsJob;

class SendBroadcastNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-broadcast-notification
                            {--title= : The notification title}
                            {--body= : The notification body}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a system-wide broadcast notification to all users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $title = $this->option('title');
        $body = $this->option('body');

        // If title or body not provided, ask user for input
        if (!$title) {
            $title = $this->ask('Enter notification title:');
        }

        if (!$body) {
            $body = $this->ask('Enter notification body:');
        }

        // Confirm with user before sending
        $this->info("The following notification will be sent:");
        $this->info("Title: {$title}");
        $this->info("Body: {$body}");

        if (!$this->confirm('Do you want to continue?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        // Dispatch the job with data
        $data = [
            'title' => $title,
            'body' => $body,
        ];

        SendBatchEmailsJob::dispatch($data);

        $this->info('Broadcast notification job dispatched successfully to queue.');
        $this->info('You can monitor the sending progress in the log files.');

        return Command::SUCCESS;
    }
}