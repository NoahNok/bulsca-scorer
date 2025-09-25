<?php

namespace App\Models;

use App\DTO\DQ;
use App\DTO\Result;
use App\DTO\SERCResult as DTOSERCResult;
use App\Models\AbstractClasses\Resultable;
use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SERC;

class SERCResult extends Resultable
{
    use HasFactory, Cloneable;

    protected $table = "serc_results";

    protected $fillable = [
        'marking_point',
        'entity_type',
        'entity_id'
    ];

    public function penalties()
    {
        return null;
    }

    public function disqualifications()
    {
        return null;
    }

    public function transformToResult(): Result
    {
        return new DTOSERCResult(
            $this->id,
            $this->result,
            $this->getMarkingPoint,
            $this->entity,
            $this->getSerc(),
        );
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public function getMarkingPointName()
    {
        return $this->belongsTo(SERCMarkingPoint::class, 'marking_point', 'id')->get('name')->implode('name');
    }

    public function getMarkingPoint()
    {

        return $this->belongsTo(SERCMarkingPoint::class, 'marking_point', 'id');
    }

    public function getSerc(): SERC
    {
        return SERC::find($this->getMarkingPoint->serc);
    }

    public function getTeam()
    {
        return $this->belongsTo(CompetitionTeam::class, 'team', 'id');
    }

    public function getJudgeLogTitle()
    {
        return "SERC: {judge} marked {team} for {event}";
    }

    public function getJudgeLogDescription()
    {
        return  $this->getMarkingPointName() . " (" . $this->getMarkingPoint->getJudge->name . "): " . round($this->result);
    }

    public function resolveJudgeLogTeam(): ?CompetitionTeam
    {
        return $this->getTeam;
    }

    public function resolveJudgeLogName()
    {
        return $this->getSerc()->getName();
    }

    public function resolveJudgeLogAssociation()
    {
        return $this->getMarkingPoint->getJudge;
    }
}
