<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissingPersonNotification extends Notification
{
    use Queueable;

    protected $post;
    protected $type; // 'lost' or 'found'

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, $type = 'lost')
    {
        $this->post = $post;
        $this->type = $type;
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
        if ($this->type === 'found') {
            return (new MailMessage)
                        ->subject('📢 FOUND: Missing Person Located - ' . $this->post->title)
                        ->greeting('Hello ' . $notifiable->name . '!')
                        ->line('Great news! A missing person has been found .')
                        ->line('**Person:** ' . $this->post->title)
                        ->line('**Found at:** ' . $this->post->location)
                        ->line('**Date found:** ' . $this->post->date_lost_found->format('M d, Y'))
                        ->line('**Description:** ' . substr($this->post->description, 0, 200) . (strlen($this->post->description) > 200 ? '...' : ''))
                        ->action('View Details', route('posts.show', $this->post))
                        ->line('Thank you for being part of our community that helps reunite families.')
                        ->line('Please share this good news with others who might be concerned.');
        } else {
            return (new MailMessage)
                        ->subject('🚨 URGENT: Missing Person Alert - ' . $this->post->title)
                        ->greeting('Hello ' . $notifiable->name . '!')
                        ->line('URGENT: A person has been reported missing 😥')
                        ->line('**Missing Person:** ' . $this->post->title)
                        ->line('**Last seen at:** ' . $this->post->location)
                        ->line('**Date missing:** ' . $this->post->date_lost_found->format('M d, Y'))
                        ->line('**Description:** ' . substr($this->post->description, 0, 200) . (strlen($this->post->description) > 200 ? '...' : ''))
                        ->action('View Details & Help Search', route('posts.show', $this->post))
                        ->line('Please keep an eye out and contact authorities if you have any information.')
                        ->line('Your help could make the difference in bringing someone home safely.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->type === 'found') {
            return [
                'type' => 'missing_person_found',
                'title' => '🎉 Missing Person Found: ' . $this->post->title,
                'message' => 'Great news! ' . $this->post->title . ' has been found at ' . $this->post->location,
                'person_name' => $this->post->title,
                'location' => $this->post->location,
                'date' => $this->post->date_lost_found->format('M d, Y'),
                'action_url' => route('posts.show', $this->post),
                'post_id' => $this->post->id,
                'alert_type' => 'found'
            ];
        } else {
            return [
                'type' => 'missing_person_alert',
                'title' => '🚨 Missing Person Alert: ' . $this->post->title,
                'message' => 'URGENT: ' . $this->post->title . ' is missing 😥. Last seen at ' . $this->post->location,
                'person_name' => $this->post->title,
                'location' => $this->post->location,
                'date' => $this->post->date_lost_found->format('M d, Y'),
                'action_url' => route('posts.show', $this->post),
                'post_id' => $this->post->id,
                'alert_type' => 'missing'
            ];
        }
    }

}