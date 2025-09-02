<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SystemWideNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $title;
    public $message;

    public function __construct($id, $title, $message)
    {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
    }
    public function via($notifiable)
    {
        return ['mail'];
    }
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->message);
    }
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
        ]);
    }
}
