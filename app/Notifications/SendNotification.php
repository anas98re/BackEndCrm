<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class SendNotification extends Notification
{
    use Queueable;
    protected $title;
    protected $body;
    protected $data;
    protected $fcmTokens;
    // $arratoken,$data,$title,$body
    public function __construct($title,$data,$body,$fcmTokens)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->fcmTokens = $fcmTokens;
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        return (new FirebaseMessage)
                    ->withTitle($this->title)
                    ->withBody($this->body)
                    ->asNotification($this->fcmTokens);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
