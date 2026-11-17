<?php

namespace App\Http\Controllers\Landing;

use App\Scoring\CompetitionResults;
use App\Scoring\Results\EventResult;
use App\Scoring\Results\EventResultPart;
use App\Scoring\Results\MasterRow;
use App\Scoring\Results\SheetRow;
use App\Scoring\Results\SheetRowEvent;
use App\Scoring\Results\Violation;
use App\Exceptions\ScoringException;
use App\Http\Controllers\Controller;
use App\Models\AbstractClasses\Event;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Event\Disqualification;
use App\Models\Event\Penalty;
use App\Models\League;
use App\Models\MasterSchema;
use App\Models\PenaltyCode;
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

        if (!$comp->hasReleased($event)) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        $results = app(CompetitionResults::class)->event($event, $league);

        $show_dq_points = $event->scoringSchema->schema['allow_disqualified_to_rank'] ?? false;

        return response()->json($this->tablify($results, $target_columns, $type, $show_dq_points));
    }

    public function getSheetResults(Competition $comp, ResultSchema $schema)
    {

        if (!$comp->hasReleased($schema)) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        try {
            $rows = app(CompetitionResults::class)->sheet($schema);
        } catch (ScoringException $e) {
            report($e);

            return response()->json(['error' => 'Results unavailable at this time, please try again later. (S_ERR)'], 403);
        }

        $results = array_map(fn(SheetRow $row) => [
            'name' => $row->entry->name,
            'points' => round($row->totalPoints, 1),
            'position' => $row->position,
            ...collect($row->events)->mapWithKeys(fn(SheetRowEvent $event) => [
                $event->eventKey() => [
                    'position' => $event->position,
                    'points' => round($event->adjustedPoints, 1),
                ],
            ]),
        ], $rows);

        return response()->json([
            'columns' => array_merge(
                ['name' => 'Name'],
                collect($rows[0]->events ?? [])->mapWithKeys(fn(SheetRowEvent $event) => [$event->eventKey() => $event->eventName])->toArray(),
                ['points' => 'Points', 'position' => 'Position'],
            ),
            'data' => $results,
        ]);
    }

    public function getMasterSheetResults(Competition $comp, MasterSchema $schema)
    {

        if (!$comp->hasReleased($schema)) {
            return response()->json(['error' => 'Results unavailable at this time, please try again later.'], 403);
        }

        $results = array_map(fn(MasterRow $row) => [
            'name' => $row->club->name,
            'points' => $row->total,
            'position' => $row->position,
        ], app(CompetitionResults::class)->master($schema)->rows);

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
        } else if ($violation_type == 'pen-special') {
            return response()->json([
                'code' => "P{$violation_id}",
                'description' => PenaltyCode::message($violation_id, $comp->getOrganisation),
                'submission' => null,

            ]);
        } else {
            $violation = Penalty::findOrFail($violation_id);
        }

        abort_unless($violation->entity?->competition == $comp->id && $comp->hasReleased($violation->event), 404);

        return response()->json([
            'code' => "{$violation}",
            'description' => $violation->getMessage(),
            'submission' => $violation->violationSubmission?->publicDetails() ?? $violation->submission?->only([
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
        abort_unless($comp->hasReleased($serc), 404);

        $fasterSercData = $serc->getSERCData();

        $overallJudgeNotes = $serc->getOverallJudgeNotes();


        return view('landing.competition.serc-breakdown', ['comp' => $comp, 'event' => $serc, 'fsd' => $fasterSercData, 'overallJudgeNotes' => $overallJudgeNotes]);
    }

    public function getSercNote(Competition $comp, SERC $serc, int $entity_id)
    {
        abort_unless($comp->hasReleased($serc), 404);

        $entity = $serc->findScorableEntity($entity_id);

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
     * @param EventResult[] $results
     */
    private function tablify(array $results, array $use_columns, string $type, bool $show_dq_points = false)
    {
        $t = new Tablify($type, $show_dq_points);

        return $t->from($use_columns, $results, function (EventResult $row) {
            return $row->entry->id;
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
    private bool $show_dq_points;

    public function __construct($event_type = 'speed', $show_dq_points = false)
    {
        $this->event_type = $event_type;
        $this->show_dq_points = $show_dq_points;
    }

    /**
     * @param EventResult[] $results
     */
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

    private function resolveColumnData($column, EventResult $result)
    {
        return match ($column) {
            'name' => $result->isCombined()
                ? ['type' => 'string-array', 'data' => array_map(fn(EventResultPart $part) => $part->entry->name, $result->parts)]
                : $result->entry->name,
            'position' => $result->position,
            'points' => $result->isDisqualified() && !$this->show_dq_points ? 'DQ' : round($result->points, 1),
            'result' => $this->resolveResult($result),
            'disqualifications' => $this->resolveDisqualifications($result),
            'penalties' => $this->resolvePenalties($result),
            default => null,
        };
    }

    private function resolveResult(EventResult $result)
    {
        $type = $this->event_type;
        $data = ['is' => $type == 'speed' ? SpeedResult::prettyTime($result->resolvedResult) : $result->resolvedResult, 'was' => $type == 'speed' ? SpeedResult::prettyTime($result->result) : $result->result];

        $result_type = 'result';

        if ($result->isCombined()) {
            $result_type = 'result-combined';
            $data['combined'] = [
                'results' => array_map(fn(EventResultPart $part) => SpeedResult::prettyTime($part->result), $result->parts),
                'dqs' => array_map(fn(EventResultPart $part) => $part->disqualificationLabel() ?: '-', $result->parts),
            ];
        }

        return ['type' => $result_type, 'data' => $data];
    }

    private function resolveDisqualifications(EventResult $result)
    {
        $links = fn(array $dqs) => array_map(fn(Violation $dq) => ['display' => $dq->label(), 'id' => $dq->id, 'type' => 'dq'], $dqs);

        if ($result->isCombined()) {
            return ['type' => 'violation-combined', 'data' => array_map(
                fn(EventResultPart $part) => $part->isDisqualified() ? $links($part->disqualifications) : '-',
                $result->parts,
            )];
        }

        if (!$result->isDisqualified()) {
            return '-';
        }

        return ['type' => 'violation', 'data' => $links($result->disqualifications)];
    }

    private function resolvePenalties(EventResult $result)
    {
        if (!$result->hasPenalties()) {
            return '-';
        }

        $penaltie_values = [];
        $penalties = $result->penalties;

        // Check for P900, remove all of them and add a single entry with the count if more than 1
        $p900Count = count(array_filter($penalties, fn(Violation $pen) => $pen->code == '900'));
        if ($p900Count > 1) {
            $penalties = array_filter($penalties, fn(Violation $pen) => $pen->code != '900');
            $penaltie_values[] = ['display' => "P900 (x$p900Count)", 'id' => 900, 'type' => 'pen-special'];
        }

        foreach ($penalties as $penalty) {
            $penaltie_values[] = ['display' => $penalty->label(), 'id' => $penalty->id, 'type' => 'pen'];
        }


        return ['type' => 'violation', 'data' => $penaltie_values];
    }
}
