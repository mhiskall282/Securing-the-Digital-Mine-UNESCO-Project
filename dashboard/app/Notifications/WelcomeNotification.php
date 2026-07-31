<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a newly-created user account.
 *
 * Contains their login email, temporary password, and a link to log in.
 * The admin who created the account sees a flash confirming the email was sent.
 */
class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $temporaryPassword,
        private readonly string $loginUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Npontu Support Tracker — Your Account is Ready')
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line('Your account on the **Npontu Technologies Support Activity Tracker** has been created.')
            ->line('Here are your login credentials:')
            ->line('**Email:** '.$notifiable->email)
            ->line('**Temporary Password:** `'.$this->temporaryPassword.'`')
            ->action('Log In Now', $this->loginUrl)
            ->line('> ⚠️ Please change your password immediately after logging in via **Settings → Change Password**.')
            ->line('If you did not expect this email, please contact your system administrator.')
            ->salutation('— Npontu Technologies Support Team');
    }
}
