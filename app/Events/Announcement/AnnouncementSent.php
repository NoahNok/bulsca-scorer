<?php

namespace App\Events\Announcement;

use App\Models\DigitalJudge\Announcement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Tells every official online in the competition about a new announcement.
 * Anyone offline picks it up from the list when they next open the app.
 */
class AnnouncementSent implements ShouldBroadcast, ShouldRescue
{
    use Dispatchable, InteractsWithSockets;

    public int $competitionId;
    public array $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->competitionId = $announcement->competition_id;
        $this->announcement = $announcement->payload();
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new PresenceChannel('judge.officials.' . $this->competitionId)];
    }

    public function broadcastAs(): string
    {
        return 'announcement.sent';
    }

    public function broadcastWith(): array
    {
        return $this->announcement;
    }
}
