<?php

namespace App\Http\Controllers\DigitalJudge\SERC;

use App\DigitalJudge\DigitalJudge;
use App\Models\SERCResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\ConfirmJudgeRequest;
use App\Http\Requests\DigitalJudge\SERC\StoreEntityMarksRequest;
use App\Http\Requests\DigitalJudge\SERC\StoreOverallNotesRequest;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeNote;
use App\Models\DigitalJudge\OverallJudgeNote;
use App\Models\SERC;
use App\Models\SERCJudge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class SERCJudgeController extends Controller
{
    /** Session key for the Objectives an Official has marked against in a League Objectives SERC. */
    private const LEAGUE_OBJECTIVES_MARKED = 'digitalJudgeLeagueObjectivesMarked';

    /**
     * An entity as judges see it. When the competition hides team names from judges (referees still
     * see them), the name is replaced with its place in the draw so the real name never reaches the page.
     */
    private function entityForJudges(Competition $competition, SERC $serc, $entity, ?int $draw = null): array
    {
        if ($competition->show_teams_to_judges || DigitalJudge::isClientHeadJudge($competition)) {
            return $entity->jsonable();
        }

        $draw ??= $serc->getPositionInDraw($entity)['index'];

        return ['id' => $entity->id, 'name' => 'Draw ' . $draw];
    }

    /**
     * Whether any of the given judges already has a Mark for the entity. Once one does, only a
     * referee can change that entity's marks for the judges in question.
     */
    private function alreadyMarked($entity, $judges): bool
    {
        return $judges->contains(fn($judge) => DigitalJudge::hasEntityBeenJudgedAlready($entity, $judge));
    }


    /**
     * The Objective an Entity is marked against in a League Objectives SERC, with the League that
     * put it there. Null when the Entity's Leagues give no single Objective.
     */
    private function leagueObjectiveFor(SERC $serc, $entity): ?array
    {
        $objectives = $serc->leagueObjectivesFor($entity);

        if ($objectives->count() != 1) {
            return null;
        }

        $objective = $objectives->first();
        $leagueIds = $objective->restrictedLeagues()->pluck('leagues.id');

        return ['objective' => $objective, 'league' => $entity->leagues->first(fn($league) => $leagueIds->contains($league->id))];
    }

    public function confirmJudge(Competition $competition, SERC $serc, SERCJudge $judge)
    {

        return Inertia::render('Judge/Competition/SERC/ConfirmJudge', [
            'competition' => $competition->jsonable(),
            'serc' => $judge->getSerc->only(['id', 'name']),
            'judge' => [
                'id' => $judge->id,
                'name' => $judge->name,
                'marking_points' => $judge->getMarkingPoints->map(function ($mp) {
                    return [
                        'id' => $mp->id,
                        'description' => $mp->name
                    ];
                })
            ]

        ]);
    }

    public function confirmJudgePost(Competition $competition, SERC $serc, ConfirmJudgeRequest $request)
    {
        if ($serc->use_restricted_judges) {
            // with League Objectives every Objective is selected, and each Entity is marked against its League's one
            DigitalJudge::clearClientJudges();
            $serc->getJudges->each(fn($objective) => DigitalJudge::addClientJudge($objective->id));
            Session::forget(self::LEAGUE_OBJECTIVES_MARKED);
        } else {
            // start again if the judges selected so far are for another SERC
            $selected = DigitalJudge::getClientJudges();

            if ($selected->contains(fn($judge) => $judge->serc != $serc->id)) {
                DigitalJudge::clearClientJudges();
                $selected = collect();
            }

            if (!$selected->contains('id', (int) $request->judge)) {
                DigitalJudge::addClientJudge((int) $request->judge);
            }
        }

        if ($competition->getScoringSettings->use_tanks) {
            return to_route('judge.competition.serc.select-tank', compact('competition', 'serc'));
        }
        DigitalJudge::setTank(null);

        return to_route('judge.competition.serc', compact('competition', 'serc'));
    }

    public function selectTank(Competition $competition, SERC $serc)
    {


        // grab serc again as the draw is against the serc with the lowest id?
        $tanks = $competition->drawSerc()->draw()->orderBy('tank')->distinct('tank')->get('tank')->pluck('tank')->toArray();

        return Inertia::render('Judge/Competition/SERC/SelectTank', ['competition' => $competition->jsonable(), 'serc' => $serc, 'tanks' => $tanks]);
    }

    public function setTank(Competition $competition, SERC $serc, int $tank)
    {
        DigitalJudge::setTank($tank);

        return to_route('judge.competition.serc', compact('competition', 'serc'));
    }

    public function home(Competition $competition, SERC $serc)
    {

        $judges = DigitalJudge::getClientJudges();

        $draws = $serc->getDraw;

        $tank = DigitalJudge::getTank();

        if ($tank) {
            $draws = $draws->where('tank', $tank);
        }

        if ($serc->scorable_entity == 'team') {
            $entityIds = $draws->pluck('entity')->filter()->unique('id')->pluck('id');

            $loadedEntities = CompetitionTeam::whereIn('id', $entityIds)->with('getCompetitors')->get();

            $teamMap = $loadedEntities->keyBy('id');

            $draws->each(function ($draw) use ($teamMap) {
                $draw->entity = $teamMap[$draw->entity->id];
            });
        }

        return Inertia::render('Judge/Competition/SERC/Home', [
            'competition' => [...$competition->jsonable(), 'show_teams_to_judges' => $competition->show_teams_to_judges],
            'serc' => $serc->only(['name', 'id']),
            'league_objectives' => (bool) $serc->use_restricted_judges,
            'judges' => $judges->map(function ($judge) {
                return [
                    'id' => $judge->id,
                    'name' => $judge->name,
                    'marking_points' => $judge->getMarkingPoints->map(function ($mp) {
                        return [
                            'id' => $mp->id,
                            'description' => $mp->name
                        ];
                    })
                ];
            }),
            'tank' => $tank,
            'draws' => $draws->map(function ($draw) use ($competition, $serc) {
                return [
                    'draw' => $draw->draw,
                    'entity' => $this->entityForJudges($competition, $serc, $draw->entity, $draw->draw),
                ];
            })->values(),

        ]);
    }

    public function addJudge(Competition $competition, SERC $serc, Request $request)
    {
        $swap = $request->has('swap');


        $selectedJudges = DigitalJudge::getClientJudges();

        $unselectedJudges = $serc->getJudges()->whereNotIn('id', $selectedJudges->pluck('id'))->get();

        return Inertia::render('Judge/Competition/SERC/AddJudge', [
            'competition' => $competition->jsonable(),
            'serc' => $serc->only(['name', 'id']),
            'judges' => $unselectedJudges->map(function ($judge) {
                return [
                    'id' => $judge->id,
                    'name' => $judge->name,
                    'no_marking_points' => $judge->getMarkingPoints()->count()
                ];
            }),
            'swap' => $swap
        ]);
    }

    public function attachJudge(Competition $competition, SERC $serc, SERCJudge $judge, Request $request)
    {
        abort_if($judge->serc != $serc->id, 404);

        if ($request->has('swap')) {
            DigitalJudge::clearClientJudges();
        }

        DigitalJudge::addClientJudge($judge->id);

        return to_route('judge.competition.serc', compact('competition', 'serc'));
    }

    public function detachJudge(Competition $competition, SERC $serc, SERCJudge $judge)
    {
        DigitalJudge::removeClientJudge($judge->id);

        return to_route('judge.competition.serc', compact('competition', 'serc'));
    }

    // =-=-=-=-=-=-=-=-=-=-=~#~=-=-=-=-=-=-=-=-=-=-=~#~=-=-=-=-=-=-=-=-=-=-=~#~=-=-=-=-=-=-=-=-=-=-=
    //                                     MARKING RELATED BELOW
    // =-=-=-=-=-=-=-=-=-=-=~#~=-=-=-=-=-=-=-=-=-=-=~#~=-=-=-=-=-=-=-=-=-=-=~#~=-=-=-=-=-=-=-=-=-=-=
    public function nextEntityToMark(Competition $competition, SERC $serc)
    {
        // For each team, determine if any marking points for the judge have been filled, get the first team with 0 filled
        // SELECT id FROM (SELECT id, (SELECT COUNT(*) FROM serc_results WHERE team=competition_teams.id AND marking_point IN (SELECT id FROM serc_marking_points WHERE judge=1)) AS markedPoints FROM competition_teams WHERE competition=3) AS b WHERE b.markedPoints = 0 LIMIT 1;



        $judges = DigitalJudge::getClientJudges();
        $j = $judges[0];

        $nextTeamId = null;

        $draw = $serc->getDraw;

        // Check if we are marking a tank
        $tank = DigitalJudge::getTank();
        if ($tank != null) {
            $draw = $draw->where('tank', $tank);
        }

        $draw = $draw->sortBy('draw');

        foreach ($draw as $allocation) {
            // same check as markEntity, or a team it turns away would be sent straight back here
            $marked = $this->alreadyMarked($allocation->entity, $judges);

            // an Entity with no single League Objective can't be marked until the organiser fixes it,
            // so carry on to the next one rather than getting stuck on it
            if (!$marked && $serc->use_restricted_judges && $serc->leagueObjectivesFor($allocation->entity)->count() != 1) {
                continue;
            }

            if (!$marked) {

                $nextTeamId = $allocation->entity->id;
                break;
            }
        }


        if ($nextTeamId == null) {

            $judgeNote = OverallJudgeNote::where('judge', $this->overallNoteObjectives($serc)->first()?->id ?? $j->id)->first();
            Inertia::flash('action', ['type' => 'overall_notes', 'data' => ($judgeNote ? $judgeNote->note : "")]);
            Inertia::flash('toast', ['variant' => 'error', 'title' => "You have finished marking."]);
            return to_route('judge.competition.serc', compact('competition', 'serc'));
        }

        $nextTeam = CompetitionTeam::find($nextTeamId);

        return to_route("judge.competition.serc.mark.entity", ["competition" => $competition, "serc" => $serc, "entity_id" => $nextTeam]);
    }

    public function markEntity(Competition $competition, SERC $serc, int $entity_id, Request $request)
    {


        $team = $serc->getScorableEntity()->findOrFail($entity_id);

        // Check team are part of this competition to avoid any dangerous behaviour
        if ($team->competition != $competition->id) return to_route("judge.competition.serc", compact('competition', 'serc'));

        $judges = DigitalJudge::getClientJudges();

        if (!DigitalJudge::isClientHeadJudge($competition) && $this->alreadyMarked($team, $judges)) return to_route("judge.competition.serc.mark.next", compact('competition', 'serc'));

        $leagueObjective = null;
        if ($serc->use_restricted_judges) {
            $leagueObjective = $this->leagueObjectiveFor($serc, $team);

            if (!$leagueObjective) {
                return Inertia::render('Judge/Competition/SERC/NoLeagueObjective', [
                    'competition' => $competition->jsonable(),
                    'serc' => $serc->only(['id', 'name']),
                    'entity' => $this->entityForJudges($competition, $serc, $team),
                    'leagues' => $team->leagues->pluck('name')->sort()->values()->all(),
                    'reason' => $serc->leagueObjectivesFor($team)->isEmpty() ? 'no_objective' : 'several_objectives',
                ]);
            }

            $judges = collect([$leagueObjective['objective']]);
        }

        $draw_info = $serc->getPositionInDraw($team);

        $judgeName = $judges[0]->name;

        DigitalJudge::setStatus($competition, $judgeName . ' | Marking ' . $team->getName() . ' (' . $draw_info['text'] . ')', $serc);

        return Inertia::render("Judge/Competition/SERC/JudgeEntity", [
            'competition' => [...$competition->jsonable(), 'show_teams_to_judges' => $competition->show_teams_to_judges],
            'serc' => $serc->only(['id', 'name']),
            'judges' => $judges->map(function ($judge) {
                return [
                    'id' => $judge->id,
                    'name' => $judge->name,
                    'description' => $judge->description,
                    'marking_points' => $judge->getMarkingPoints->map(function ($mp) {
                        $stats = $mp->minMaxAvg();
                        return [
                            'id' => $mp->id,
                            'description' => $mp->name,
                            'template' => $mp->template?->settings,
                            'stats' => [
                                'min' => $stats?->min_result ?? 0,
                                'max' => $stats?->max_result ?? 0,
                                'avg' => $stats?->avg_result ?? 0
                            ]
                        ];
                    })
                ];
            }),
            'entity' => $this->entityForJudges($competition, $serc, $team),
            'league_objective' => $leagueObjective ? ['objective' => $leagueObjective['objective']->name, 'league' => $leagueObjective['league']->name] : null,
            'draw' => $serc->getPositionInDraw($team),
            'existingMarks' => $judges->mapWithKeys(function ($judge) use ($team) {
                $marks = $judge->getMarkingPoints->mapWithKeys(function ($mp) use ($team) {
                    $result = $mp->getScoreForTeam($team);
                    return [$mp->id => $result];
                });
                return [$judge->id => $marks];
            }),
            'existingNotes' => $judges->mapWithKeys(function ($judge) use ($team) {
                $note = $judge->getNotes()->whereMorphedTo('entity', $team)->first();
                return [$judge->id => $note?->note];
            })

        ]);
    }

    public function storeEntityMarks(Competition $competition, SERC $serc, int $entity_id, StoreEntityMarksRequest $request)
    {

        $data = $request->validated();
        $entity = $serc->getScorableEntity()->findOrFail($entity_id);

        if ($entity->competition != $competition->id) abort(404);

        $judges = $data['marks'] ?? [];
        $notes = $data['notes'] ?? [];

        // Only allow writing marks/notes for the judges selected in this session, and only their own marking points
        $clientJudges = DigitalJudge::getClientJudges()->where('serc', $serc->id)->keyBy('id');
        $allowedMarkingPoints = $clientJudges->map(fn($judge) => $judge->getMarkingPoints->pluck('id')->all());


        // validated payload from
        // judge_id => marking_point_id => mark

        // Check the whole payload before writing anything so a bad request can't partially save
        foreach ($judges as $judge_id => $marking_points) {
            if (!$allowedMarkingPoints->has($judge_id)) abort(403);

            foreach (array_keys($marking_points) as $marking_point_id) {
                if (!in_array($marking_point_id, $allowedMarkingPoints[$judge_id])) abort(403);
            }
        }

        foreach (array_keys($notes) as $judge_id) {
            if (!$allowedMarkingPoints->has($judge_id)) abort(403);
        }

        // with League Objectives the Entity can only be marked against its League's Objective, and it
        // counts as marked once any Objective has a Mark for it
        if ($serc->use_restricted_judges) {
            $objectiveId = $this->leagueObjectiveFor($serc, $entity)['objective']->id ?? null;

            foreach ([...array_keys($judges), ...array_keys($notes)] as $judge_id) {
                abort_if($judge_id != $objectiveId, 422, "These marks aren't for this team's League Objective.");
            }
        }

        // Marks already submitted can only be overridden by a referee
        $payloadJudges = $serc->use_restricted_judges
            ? $clientJudges
            : $clientJudges->only([...array_keys($judges), ...array_keys($notes)]);

        abort_if(
            !DigitalJudge::isClientHeadJudge($competition) && $this->alreadyMarked($entity, $payloadJudges),
            409,
            'Already marked — ask a referee to change it.'
        );

        $changes = [];

        foreach ($judges as $judge_id => $marking_points) {
            $judge = $clientJudges[$judge_id];

            foreach ($marking_points as $marking_point_id => $mark) {

                $result = SERCResult::firstOrNew(['marking_point' => $marking_point_id, 'entity_type' => $entity->getMorphClass(), 'entity_id' => $entity->id]);
                $was = $result->result;
                $result->result = $mark;
                $result->save();

                // null == 0 in PHP, so a first mark of 0 needs checking for explicitly
                if ($was === null || (float) $was != (float) $mark) {
                    $mp = $judge->getMarkingPoints->find($marking_point_id);
                    $changes[] = ['marking_point' => $mp->id, 'judge' => $judge->id, 'name' => "{$judge->name} - {$mp->name}", 'old' => $was, 'new' => $mark];
                }
            }

            // $judgeNote = JudgeNote::firstOrNew(['judge' => $judge_id, 'entity_type' => $entity->getMorphClass(), 'entity_id' => $entity->id]);

            // if ($notes === null || $notes === '') {
            //     if ($judgeNote->id) $judgeNote->delete();
            //     continue;
            // } else {
            //     $judgeNote->note = $notes;
            //     $judgeNote->save();
            // }
        }

        foreach ($notes as $judge_id => $note) {
            $judgeNote = JudgeNote::firstOrNew(['judge' => $judge_id, 'entity_type' => $entity->getMorphClass(), 'entity_id' => $entity->id]);

            if ($note === null || $note === '') {
                if ($judgeNote->id) $judgeNote->delete();
                continue;
            } else {
                $judgeNote->note = $note;
                $judgeNote->save();
            }
        }

        if ($serc->use_restricted_judges && count($judges) > 0) {
            Session::put(self::LEAGUE_OBJECTIVES_MARKED, collect(Session::get(self::LEAGUE_OBJECTIVES_MARKED, []))->push($objectiveId)->unique()->values()->all());
        }

        if (count($changes) > 0) {
            $overridden = collect($changes)->contains(fn($change) => $change['old'] !== null);
            $verb = $overridden ? 'overridden' : 'marked';

            $serc->recordActivity('SERC_MARKED', "{$entity->getName($competition)}'s marks were {$verb} by {$request->user()->name}", context: [
                'changes' => $changes,
            ], related: [$serc, $entity, $competition]);
        }
    }

    public function storeOverallNotes(Competition $competition, SERC $serc, StoreOverallNotesRequest $request)
    {
        $data = $request->validated();

        $judges = $this->overallNoteObjectives($serc);

        if ($judges->isEmpty()) {
            Inertia::flash('toast', ['variant' => 'error', 'title' => "Mark a team before leaving overall notes"]);
            return;
        }

        foreach ($judges as $judge) {
            $note = OverallJudgeNote::firstOrNew(['judge' => $judge->id]);

            if ($data['note'] == ''  && $note->id) {
                $note->delete();
            } else if ($data['note'] != '' && $data['note'] != null) {
                $note->note = $data['note'];
                $note->save();
            }
        }

        Inertia::flash('toast', ['variant' => 'success', 'title' => "Overall note saved"]);
    }

    /**
     * The Objectives an Official's overall notes are for: the one they picked, or with League
     * Objectives, each Objective they marked against in this session.
     */
    private function overallNoteObjectives(SERC $serc)
    {
        $judges = DigitalJudge::getClientJudges();

        if (!$serc->use_restricted_judges) {
            return $judges->take(1);
        }

        return $judges->whereIn('id', Session::get(self::LEAGUE_OBJECTIVES_MARKED, []))->values();
    }

    public function getJudgeNotes(Competition $competition, SERC $serc)
    {
        $judges = DigitalJudge::getClientJudges();

        $notes = $judges->map(function ($judge) use ($competition, $serc) {
            return [
                'name' => $judge->name,
                'notes' => $judge->getNotes()->with('entity')->get(['note', 'entity_id', 'entity_type'])->map(function ($note) use ($competition, $serc) {
                    return [
                        'entity' => $this->entityForJudges($competition, $serc, $note->entity),
                        'note' => $note->note
                    ];
                })
            ];
        });

        return response()->json($notes);
    }

    public function getPreviousMarks(Competition $competition, SERC $serc, int $judge_id)
    {
        $judge = DigitalJudge::getClientJudges()->firstWhere('id', $judge_id);

        $data = [];

        if ($judge) {
            $markingPoints = $judge->getMarkingPoints;
            $draws = $judge->getSERC->getDraw;


            foreach ($markingPoints as $mp) {

                $entityMarks = [];

                foreach ($draws as $draw) {
                    $entityMarks[] = [
                        'entity' => $this->entityForJudges($competition, $serc, $draw->entity, $draw->draw),
                        'mark' => $mp->getScoreForTeam($draw->entity)
                    ];
                }

                $data[] = [
                    'marking_point' => [
                        'id' => $mp->id,
                        'description' => $mp->name
                    ],
                    'marks' => $entityMarks
                ];
            }
        }

        return response()->json($data);
    }
}
