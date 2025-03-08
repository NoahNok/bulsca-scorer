<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeedEvent extends Model
{
    use HasFactory, Cloneable;

    public function getName()
    {
        return $this->name;
    }

    public function getEventCodes($type = null, $codeType = null)
    {

        $ec = new EventCode();

        if (!is_null($type)) {
            $ec = $ec->where('type', $type);
        }

        if (!is_null($codeType)) {
            $ec = $ec->where('pendq_type', $codeType);
        }

        $codes = $ec->where('event', $this->name)->get();

        $dqCodes = $codes->where('pendq_type', 'App\Models\DQCode')->pluck('pendq_id');
        $penCodes = $codes->where('pendq_type', 'App\Models\PenaltyCode')->pluck('pendq_id');

        $dqCodes = DQCode::whereIn('id', $dqCodes)->get();
        $penCodes = PenaltyCode::whereIn('id', $penCodes)->get();

        foreach ($dqCodes as $dq) {
            $dq->type = $codes->where('pendq_id', $dq->id)->first()->type;
        }

        foreach ($penCodes as $pen) {
            $pen->type = $codes->where('pendq_id', $pen->id)->first()->type;
        }

        return [
            'dq' => $dqCodes->groupBy('type'),
            'pen' => $penCodes->groupBy('type')
        ];
    }

    public function getMissingEventCodes()
    {


        $dqCodes = EventCode::where('event', $this->name)->where('pendq_type', 'App\Models\DQCode')->pluck('pendq_id');
        $penCodes = EventCode::where('event', $this->name)->where('pendq_type', 'App\Models\PenaltyCode')->pluck('pendq_id');

        $missingDQCodes = DQCode::whereNotIn('id', $dqCodes)->get();
        $missingPenCodes = PenaltyCode::whereNotIn('id', $penCodes)->get();

        foreach ($missingDQCodes as $dq) {
            $dq->type = "OTHER";
        }

        foreach ($missingPenCodes as $pen) {
            $pen->type = "OTHER";
        }

        return [
            'dq' => $missingDQCodes->makeHidden(['created_at', 'updated_at'])->groupBy('type'),
            'pen' => $missingPenCodes->makeHidden(['created_at', 'updated_at'])->groupBy('type')
        ];
    }
}
