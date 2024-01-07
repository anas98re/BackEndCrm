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
    public function __construct($title,$body,$data,$fcmTokens)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->fcmTokens = [$fcmTokens];
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        info('Firebase notification: ' . json_encode($this->fcmTokens));
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
