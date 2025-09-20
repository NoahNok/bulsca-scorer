<?php

namespace App\Http\Controllers\Landing;

use App\DTO\RankedResult;
use App\Http\Controllers\Controller;
use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\League;
use App\Models\ResultSchema;
use App\Models\SERC;
use App\Models\SpeedResult;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function getEventResults(Competition $comp, $league, $event, $type)
    {


        if ($league === 'all') {
            $league = null;
        } else {
            $league = League::findOrFail($league);
        }

        $target_columns = [];

        if ($type == 'speed') {
            $event = CompetitionSpeedEvent::findOrFail($event);
            $target_columns = [
                'name',
                'result',
                'disqualifications',
                'penalties',
                'points',
                'position',
            ];
        } else {
            $event = SERC::findOrFail($event);
            $target_columns = [
                'name',
                'disqualifications',
                'result',
                'points',
                'position',
            ];
        }

        if (!$event->viewable) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        $results = $event->getRankedResults($league);

        return response()->json($this->tablify($results, $target_columns, $type));
    }

    public function getSheetResults(Competition $comp, ResultSchema $schema)
    {

        if (!$schema->viewable) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        $rawResults = $schema->getResults();

        $results = $rawResults->map(function ($r) use ($schema) {
            return [
                'name' => $r->entity->getName(),
                'points' => round($r->totalPoints),
                'position' => $r->position,
                ...$r->events->mapWithKeys(fn($res) => [
                    "{$res->event->id}:{$res->event->getType()}" => [
                        'position' => $res->position,
                        'points' => round($res->adjustedPoints),
                    ],
                ]),
            ];
        });

        return response()->json([
            'columns' => array_merge(['name' => 'Name'], $rawResults->first()->events->mapWithKeys(fn($e) => ["{$e->event->id}:{$e->event->getType()}" => $e->event->getName()])->toArray(), ['points' => 'Points', 'position' => 'Position']),
            'data' => $results,
        ]);
    }


    /**
     * @param RankedResult[] $results
     */
    private function tablify(array $results, array $use_columns, string $type)
    {
        $table_data = [];

        $table_columns = [
            'name' => 'Name',
            'position' => 'Position',
            'points' => 'Points',
            'result' => 'Result',
            'disqualifications' => 'Disqualifications',
            'penalties' => 'Penalties',
        ];

        $table_use_columns = [];
        foreach ($use_columns as $col) {
            if (array_key_exists($col, $table_columns)) {
                $table_use_columns[$col] = $table_columns[$col];
            }
        }
        $table_columns = $table_use_columns;

        foreach ($results as $result) {
            $row = [];
            foreach ($table_columns as $column => $label) {
                $row[$column] = match ($column) {
                    'name' => $result->entity->getName(),
                    'position' => $result->position,
                    'points' => round($result->points),
                    'result' => ['is' => $type == 'speed' ? SpeedResult::prettyTime($result->resolvedResult) : $result->resolvedResult, 'was' => $type == 'speed' ? SpeedResult::prettyTime($result->result) : $result->result],
                    'disqualifications' => $result->isDisqualified() ? $result->disqualifications->map(fn(Disqualification $dq) => "{$dq}")->join(', ') : '-',
                    'penalties' => $result->hasPenalties() ? $result->penalties->map(fn(Penalty $p) => "{$p}")->join(', ') : '-',
                    default => null,
                };
            }

            $table_data[] = $row;
        }

        return [
            'columns' => $table_columns,
            'data' => $table_data,
        ];
    }
}
