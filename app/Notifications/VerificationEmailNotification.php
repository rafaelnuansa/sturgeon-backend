<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Click the button below to verify your email address.')
            ->action('Verify Email', $this->verificationUrl)
            ->line('Thank you for using our application!');
    }
}

