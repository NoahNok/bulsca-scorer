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
 * Tells the competition's referees a submission (or resubmission) is waiting
 * for review. Only reaches them if they're online, nothing is stored.
 */
class ViolationSubmitted implements ShouldBroadcast, ShouldRescue
{
    use Dispatchable, InteractsWithSockets;

    public int $competitionId;
    public array $alert;

    public function __construct(ViolationSubmission $submission, ?string $from = null)
    {
        $this->competitionId = $submission->competition_id;
        $this->alert = $submission->alertPayload($from);
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('judge.referees.' . $this->competitionId)];
    }

    public function broadcastAs(): string
    {
        return 'violation.submitted';
    }

    public function broadcastWith(): array
    {
        return $this->alert;
    }
}
