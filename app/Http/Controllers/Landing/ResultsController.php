<?php

namespace App\Http\Controllers\Landing;

use App\DTO\RankedResult;
use App\Http\Controllers\Controller;
use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\League;
use App\Models\MasterSchema;
use App\Models\ResultSchema;
use App\Models\SERC;
use App\Models\SpeedResult;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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

            $target_columns = $event->hasPenalties() ? [
                'name',
                'result',
                'disqualifications',
                'penalties',
                'points',
                'position',
            ] : [
                'name',
                'result',
                'disqualifications',
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

        if (!$event->viewable || !$comp->public_results) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        $results = $event->getRankedResults($league);

        $show_dq_points = $event->scoringSchema->schema['allow_disqualified_to_rank'] ?? false;

        return response()->json($this->tablify($comp, $results, $target_columns, $type, $show_dq_points));
    }

    public function getSheetResults(Competition $comp, ResultSchema $schema)
    {

        if (!$schema->viewable || !$comp->public_results) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        $rawResults = $schema->getResults();

        $results = $rawResults->map(function ($r) use ($comp) {
            return [
                'name' => $r->entity->getName($comp),
                'points' => round($r->totalPoints, 1),
                'position' => $r->position,
                ...$r->events->mapWithKeys(fn($res) => [
                    "{$res->event->id}:{$res->event->getType()}" => [
                        'position' => $res->position,
                        'points' => round($res->adjustedPoints, 1),
                    ],
                ]),
            ];
        });

        return response()->json([
            'columns' => array_merge(['name' => 'Name'], $rawResults->first()?->events->mapWithKeys(fn($e) => ["{$e->event->id}:{$e->event->getType()}" => $e->event->getName()])->toArray() ?? [], ['points' => 'Points', 'position' => 'Position']),
            'data' => $results,
        ]);
    }

    public function getMasterSheetResults(Competition $comp, MasterSchema $schema)
    {

        if (!$schema->viewable || !$comp->public_results) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        $rawResults = $schema->getResults();

        $results = $rawResults->map(function ($r) use ($comp) {
            return [
                'name' => $r->entity->getName($comp),
                'points' => $r->total,
                'position' => $r->position,
            ];
        });

        return response()->json([
            'columns' => array_merge(['name' => 'Name'], ['points' => 'Points', 'position' => 'Position']),
            'data' => $results,
        ]);
    }

    public function getViolation(Competition $comp, $violation_id, $violation_type)
    {

        $violation = null;

        if ($violation_type == 'dq') {
            $violation = Disqualification::findOrFail($violation_id);
        } else {
            $violation = Penalty::findOrFail($violation_id);
        }

        $submission = $violation->submission;



        return response()->json([
            'code' => "{$violation}",
            'description' => $violation->getMessage(),
            'submission' => $submission?->only([
                'code',
                'turn',
                'length',
                'details',
                'name',
                'position',
                'seconder_name',
                'seconder_position',
                'resolved'
            ]),
            'for' => $violation->entity->getName($comp),
        ]);
    }

    public function showSercBreakdown(Competition $comp, SERC $serc)
    {
        $fasterSercData = $serc->getSERCData();

        $overallJudgeNotes = $serc->getOverallJudgeNotes();


        return view('landing.competition.serc-breakdown', ['comp' => $comp, 'event' => $serc, 'fsd' => $fasterSercData, 'overallJudgeNotes' => $overallJudgeNotes]);
    }

    public function getSercNote(Competition $comp, SERC $serc, int $entity_id)
    {

        $entity = $serc->getScorableEntity()::find($entity_id);

        $notes = [];

        foreach ($serc->getNotesForEntity($entity) as $note) {
            $notes[] = [
                'judge' => $note->getJudge->name,
                'note' => $note->note
            ];
        }


        return [
            'name' => $entity->getName($comp),
            'notes' => $notes
        ];
    }


    /**
     * @param RankedResult[] $results
     */
    private function tablify(Competition $comp, array $results, array $use_columns, string $type, bool $show_dq_points = false)
    {
        $t = new Tablify($comp, $type, $show_dq_points);

        return $t->from($use_columns, $results, function ($row) {
            return $row->entity->id;
        });
    }
}

class Tablify
{

    private $available_columns = [
        'name' => 'Name',
        'position' => 'Position',
        'points' => 'Points',
        'result' => 'Result',
        'disqualifications' => 'Disqualifications',
        'penalties' => 'Penalties',
    ];

    private $event_type;
    private Competition $comp;
    private bool $show_dq_points;

    public function __construct(Competition $comp, $event_type = 'speed', $show_dq_points = false)
    {
        $this->comp = $comp;
        $this->event_type = $event_type;
        $this->show_dq_points = $show_dq_points;
    }

    public function from(array $use_columns, array $results, $row_id_func = null)
    {
        $table_data = [];


        $use_columns[] = '_entity_id';

        $table_use_columns = [];
        foreach ($use_columns as $col) {
            if (array_key_exists($col, $this->available_columns)) {
                $table_use_columns[$col] = $this->available_columns[$col];
            }
        }
        $table_columns = $table_use_columns;

        foreach ($results as $result) {
            $row_data = [];
            foreach ($table_columns as $column => $label) {
                $row_data[$column] = $this->resolveColumnData($column, $result);
            }

            $table_data[] = [
                'data' => $row_data,
                'id' => $row_id_func ? $row_id_func($result) : null,
            ];
        }

        return [
            'columns' => $table_columns,
            'data' => $table_data,
        ];
    }

    private function resolveColumnData($column, $result)
    {

        $is_combined = $result->isCombined();

        return match ($column) {
            'name' => $is_combined ? ['type' => 'string-array', 'data' => $result->combined->map(fn($item) => $item->entity->getName($this->comp))->toArray()] : $result->entity->getName($this->comp),
            'position' => $result->position,
            'points' => $result->isDisqualified() && !$this->show_dq_points ? 'DQ' : round($result->points, 1),
            'result' => $this->resolveResult($result, $is_combined),
            'disqualifications' => $this->resolveDisqualifications($result, $is_combined),
            'penalties' => $this->resolvePenalties($result),
            default => null,
        };
    }

    private function resolveResult($result, $combined = false)
    {
        $type = $this->event_type;
        $data = ['is' => $type == 'speed' ? SpeedResult::prettyTime($result->resolvedResult) : $result->resolvedResult, 'was' => $type == 'speed' ? SpeedResult::prettyTime($result->result) : $result->result];

        $result_type = 'result';

        if ($combined) {
            $result_type = 'result-combined';
            $data['combined'] = [
                'results' => $result->combined->map(fn($item) => SpeedResult::prettyTime($item->result))->toArray(),
                'dqs' => $result->combined->map(fn($item) => $item->getDisqualificationsString() ?: '-')->toArray(),
            ];
        }

        return ['type' => $result_type, 'data' => $data];
    }

    private function resolveDisqualifications($result, $combined = false)
    {



        if ($combined) {
            return ['type' => 'violation-combined', 'data' => $result->combined->map(function ($item) {
                if (!$item->isDisqualified()) {
                    return '-';
                }
                return $item->disqualifications->map(fn(Disqualification $dq) => ['display' => "{$dq}", 'id' => $dq->id, 'type' => 'dq'])->values()->toArray();
            })->toArray()];
        }

        if (!$result->isDisqualified()) {
            return '-';
        }

        return ['type' => 'violation', 'data' => $result->disqualifications->map(fn(Disqualification $dq) => ['display' => "{$dq}", 'id' => $dq->id, 'type' => 'dq'])->values()->toArray()];
    }

    private function resolvePenalties($result)
    {
        if (!$result->hasPenalties()) {
            return '-';
        }
        return ['type' => 'violation', 'data' => $result->penalties->map(fn(Penalty $p) => ['display' => "{$p}", 'id' => $p->id, 'type' => 'pen'])->values()->toArray()];
    }
}
