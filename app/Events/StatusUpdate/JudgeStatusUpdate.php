<?php

namespace App\Events\StatusUpdate;

use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JudgeStatusUpdate implements ShouldBroadcast, ShouldRescue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private Competition $competition, private string $judgeId, private string $status, private ?Event $event = null)
    {
        //
    }

    public function getStatusMessage(): string
    {
        if (!$this->event) {
            return $this->status;
        }

        return "{$this->event->getName()} | {$this->status}";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('judge.competition.' . $this->competition->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'judge.status-update';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->judgeId,
            'status' => $this->getStatusMessage(),
            'event' => $this->event ? [
                'id' => $this->event->id,
                'name' => $this->event->getName()
            ] : null
        ];
    }
}
