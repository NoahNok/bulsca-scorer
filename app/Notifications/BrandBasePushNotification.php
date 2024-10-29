<?php

namespace App\Notifications;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class BrandBasePushNotification extends Notification implements ShouldQueue
{
    use Queueable;


    protected string $title, $body;
    protected array $actions;
    protected Competition $competition;


    public function __construct(Competition $competition, string $title, string $body, array $actions = [])
    {
        $this->competition = $competition;
        $this->title = $title;
        $this->body = $body;
        $this->actions = $actions;
    }

    public function getCompetition(): Competition
    {
        return $this->competition;
    }

    /**
     * Sends the notification to the users, internally calls Notification::send(...)
     * @param Competition $competition the competition to grab related users from
     * @param string|array $targetRoles the brand roles that should be notified, defaults to admins only
     * @param bool $sendToAdmin if scorer admins (not brand) get the notification
     */


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $wpm = (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon(asset('blogo.png'));




        return $wpm;

        // ->data(['id' => $notification->id])
        // ->badge()
        // ->dir()
        // ->image()
        // ->lang()
        // ->renotify()
        // ->requireInteraction()
        // ->tag()
        // ->vibrate()
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
