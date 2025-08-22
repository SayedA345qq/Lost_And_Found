<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClaimNotification extends Notification
{
    use Queueable;

    protected $claim;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Claim $claim, $status = 'new')
    {
        $this->claim = $claim;
        $this->status = $status;
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
        if ($this->status === 'accepted') {
            return (new MailMessage)
                        ->subject('Your claim has been accepted!')
                        ->greeting('Hello ' . $notifiable->name . '!')
                        ->line('Great news! Your claim has been accepted.')
                        ->line('**Item:** ' . $this->claim->post->title)
                        ->line('**Posted by:** ' . $this->claim->post->user->name)
                        ->line('The item owner has accepted your claim. You can now coordinate with them to retrieve the item.')
                        ->action('View Item Details', route('posts.show', $this->claim->post))
                        ->line('Please contact the owner to arrange pickup or delivery.')
                        ->line('Thank you for using our platform!');
        } elseif ($this->status === 'rejected') {
            return (new MailMessage)
                        ->subject('Your claim has been rejected')
                        ->greeting('Hello ' . $notifiable->name . '!')
                        ->line('We regret to inform you that your claim has been rejected.')
                        ->line('**Item:** ' . $this->claim->post->title)
                        ->line('**Posted by:** ' . $this->claim->post->user->name)
                        ->line('The item owner has decided that this claim does not match their lost item.')
                        ->line('Don\'t worry! You can continue searching for other items or post your own if you\'ve lost something.')
                        ->action('Browse Items', route('posts.index'))
                        ->line('Thank you for using our platform!');
        } else {
            // Default: new claim notification
            $messagePreview = strlen($this->claim->message) > 200 
                ? substr($this->claim->message, 0, 200) . '...'
                : $this->claim->message;

            return (new MailMessage)
                        ->subject('Someone claimed your "' . $this->claim->post->title . '"')
                        ->greeting('Hello ' . $notifiable->name . '!')
                        ->line('Good news! Someone has claimed your lost item.')
                        ->line('**Item:** ' . $this->claim->post->title)
                        ->line('**Claimed by:** ' . $this->claim->user->name)
                        ->line('**Their message:** ' . $messagePreview)
                        ->line('**Contact info:** ' . ($this->claim->contact_info ?: 'Not provided'))
                        ->action('View Claim Details', route('posts.show', $this->claim->post))
                        ->line('You can accept or reject this claim from your dashboard.')
                        ->line('Thank you for using our platform!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->status === 'accepted') {
            return [
                'type' => 'claim_accepted',
                'title' => 'Your claim has been accepted!',
                'message' => 'Your claim for "' . $this->claim->post->title . '" has been accepted by ' . $this->claim->post->user->name,
                'post_title' => $this->claim->post->title,
                'poster_name' => $this->claim->post->user->name,
                'action_url' => route('posts.show', $this->claim->post),
                'claim_id' => $this->claim->id
            ];
        } elseif ($this->status === 'rejected') {
            return [
                'type' => 'claim_rejected',
                'title' => 'Your claim has been rejected',
                'message' => 'Your claim for "' . $this->claim->post->title . '" has been rejected by ' . $this->claim->post->user->name,
                'post_title' => $this->claim->post->title,
                'poster_name' => $this->claim->post->user->name,
                'action_url' => route('posts.show', $this->claim->post),
                'claim_id' => $this->claim->id
            ];
        } else {
            // Default: new claim notification
            $messagePreview = strlen($this->claim->message) > 100 
                ? substr($this->claim->message, 0, 100) . '...'
                : $this->claim->message;

            return [
                'type' => 'claim_notification',
                'title' => 'Someone claimed your "' . $this->claim->post->title . '"',
                'message' => $this->claim->user->name . ' claimed your "' . $this->claim->post->title . '"',
                'claimer_name' => $this->claim->user->name,
                'post_title' => $this->claim->post->title,
                'message_preview' => $messagePreview,
                'contact_info' => $this->claim->contact_info,
                'action_url' => route('posts.show', $this->claim->post),
                'claim_id' => $this->claim->id
            ];
        }
    }
}