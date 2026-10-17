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
 * Tells every official online in the competition a referee withdrew (deleted)
 * an announcement, so it disappears. Anyone offline just won't get it in the list.
 */
class AnnouncementWithdrawn implements ShouldBroadcast, ShouldRescue
{
    use Dispatchable, InteractsWithSockets;

    public int $competitionId;
    public int $announcementId;

    public function __construct(Announcement $announcement)
    {
        $this->competitionId = $announcement->competition_id;
        $this->announcementId = $announcement->id;
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
        return 'announcement.withdrawn';
    }

    public function broadcastWith(): array
    {
        return ['id' => $this->announcementId];
    }
}
