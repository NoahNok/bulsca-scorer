<?php

namespace App\Models\DigitalJudge\Violation;

use App\Models\Competition;
use App\Models\DQCode;
use App\Models\Interfaces\IJsonable;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

class ViolationSubmission extends Model implements IJsonable
{
    // THIS IS REFERENCED BY A MIGRATION, ANY CHANGES MUST BE REFLECTED INTO THE DATABASE AS THE COLUMN IS A ENUM
    public static array $STATES = ["SUBMITTED", "ACCEPTED", "REJECTED", "APPEALED", "REMOVED"];

    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    public function submitted(): MorphTo
    {
        return $this->morphTo();
    }

    public function applied(): MorphTo
    {
        return $this->morphTo();
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    #[Override]
    public function jsonable(): array
    {
        $violation = $this->submitted;
        $user = $this->submitter;



        return [
            'id' => $this->id,
            'status' => $this->status,
            'entity' => $this->entity->jsonable(),
            'event' => $this->event->jsonable(),
            'violation' => [
                'id' => $violation->id,
                'code' => $violation->code,
                'description' => $violation->description,
                'type' => null,
                'vtype' => $violation instanceof (DQCode::class) ? "DQ" : "PEN"
            ],
            'details' => [
                'turn' => $this->turn,
                'length' => $this->length,
                'details' => $this->details,
            ],
            'submitter' => [
                'user' => ['id' => $user->id, 'name' => $user->name],
                'position' => $this->submitter_position
            ],
            'seconder' => [
                'name' => $this->seconder_name,
                'position' => $this->seconder_position
            ],
            'order' => $this->event->getType() == "speed" ? $this->getHeatLane() : $this->getTankDraw()
        ];
    }

    private function getHeatLane()
    {
        $heatlane = $this->event->getHeats()->whereMorphedTo('entity', $this->entity)->first();

        if (!$heatlane) {
            $entityName = $this->entity->name;
            $eventName = $this->event->getName();
            throw new Exception("Unable to locate attached heat for DQ/Pen which isn't acceptable - {$entityName} in {$eventName}");
        }

        return [
            'heat' => $heatlane->heat,
            'lane' => $heatlane->lane
        ];
    }

    private function getTankDraw()
    {
        $use_tanks = $this->competition->getScoringSettings->use_tanks;

        $tankdraw = $this->event->getDraw()->whereMorphedTo('entity', $this->entity)->first();

        $draw = ['draw' => $tankdraw->draw];

        if ($use_tanks) {
            $draw['tank'] = $tankdraw->tank;
        }

        return $draw;
    }
}
