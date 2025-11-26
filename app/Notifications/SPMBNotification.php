<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SPMBNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;
    public $title;
    public $message;
    public $actionUrl;
    public $actionText;

    public function __construct($type, $title, $message, $actionUrl = null, $actionText = 'Lihat Detail')
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function via($notifiable)
    {
        // Email only mode - always use database and mail
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
                    ->subject($this->title)
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line($this->message);

        if ($this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }

        return $mail->line('Terima kasih telah menggunakan sistem SPMB kami.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
        ];
    }
}