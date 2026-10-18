<?php

namespace App\Events\Violation;

use App\Models\DigitalJudge\Violation\ViolationSubmission;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Tells the judge who made a submission that the referee changed its status.
 * Only reaches them if they're online, nothing is stored.
 */
class ViolationStatusChanged implements ShouldBroadcast, ShouldRescue
{
    use Dispatchable, InteractsWithSockets;

    public int $submitterId;
    public array $alert;

    public function __construct(ViolationSubmission $submission, ?string $from)
    {
        // Build the payload now, so a queued broadcast still describes this change
        $this->submitterId = $submission->submitter_id;
        $this->alert = $submission->alertPayload($from);
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.' . $this->submitterId)];
    }

    public function broadcastAs(): string
    {
        return 'violation.status-changed';
    }

    public function broadcastWith(): array
    {
        return $this->alert;
    }
}
