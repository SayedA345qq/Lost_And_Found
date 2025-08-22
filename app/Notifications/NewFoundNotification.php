<?php

namespace App\Notifications;

use App\Models\FoundNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFoundNotification extends Notification
{
    use Queueable;

    protected $foundNotification;

    /**
     * Create a new notification instance.
     */
    public function __construct(FoundNotification $foundNotification)
    {
        $this->foundNotification = $foundNotification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $messagePreview = strlen($this->foundNotification->message) > 200 
            ? substr($this->foundNotification->message, 0, 200) . '...'
            : $this->foundNotification->message;

        return (new MailMessage)
                    ->subject('Great News! Your "' . $this->foundNotification->post->title . '" was found!')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Excellent news! Someone has found your lost item.')
                    ->line('**Item:** ' . $this->foundNotification->post->title)
                    ->line('**Found by:** ' . $this->foundNotification->finder->name)
                    ->line('**Found at:** ' . $this->foundNotification->found_location)
                    ->line('**Their message:** ' . $messagePreview)
                    ->line('**Contact info:** ' . ($this->foundNotification->contact_info ?: 'Not provided'))
                    ->action('View Details & Contact Finder', route('posts.show', $this->foundNotification->post))
                    ->line('Please contact the finder to arrange pickup of your item.')
                    ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $messagePreview = strlen($this->foundNotification->message) > 100 
            ? substr($this->foundNotification->message, 0, 100) . '...'
            : $this->foundNotification->message;

        return [
            'type' => 'found_notification',
            'title' => 'Your "' . $this->foundNotification->post->title . '" was found!',
            'message' => 'Your "' . $this->foundNotification->post->title . '" was found by ' . $this->foundNotification->finder->name,
            'finder_name' => $this->foundNotification->finder->name,
            'post_title' => $this->foundNotification->post->title,
            'message_preview' => $messagePreview,
            'found_location' => $this->foundNotification->found_location,
            'contact_info' => $this->foundNotification->contact_info,
            'action_url' => route('posts.show', $this->foundNotification->post),
            'found_notification_id' => $this->foundNotification->id
        ];
    }
}