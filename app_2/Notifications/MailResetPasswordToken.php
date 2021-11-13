<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Constants\FrontendRoutes;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MailResetPasswordToken extends Notification
{
    use Queueable;

    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param mixed $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject('Reset your password')
            ->line('Hello, you have request password reset, Click the button to reset it.')
            ->action('Reset Password', FrontendRoutes::buildUrl(FrontendRoutes::RESET_PASSWORD, ['token' => $this->token]));
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
}
