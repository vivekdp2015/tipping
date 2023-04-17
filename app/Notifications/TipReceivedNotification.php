<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Benwilkins\FCM\FcmMessage;
use App\User;

class TipReceivedNotification extends Notification
{
    use Queueable;

    public $notification, $sound, $img;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
        $this->notificationType = User::with('notificationType.sound')->findOrFail($this->notification['notification']['user_id']);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line($this->notification['notification']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toFcm($notifiable)
    {
        $message = new FcmMessage();
        $message->content([
            'title'        => 'Transaction',
            'body'         => $this->notification['notification']['notification'],
            'sound'        => '',
            'icon'         => '',
            'click_action' => ''
        ])->data([
            'user_id' => $this->notification['notification']['user_id'],
            'msg' => $this->notification['notification']['notification'],
            'title' => 'Congratulations',
        ])->priority(FcmMessage::PRIORITY_HIGH);

        return $message;
    }
}
