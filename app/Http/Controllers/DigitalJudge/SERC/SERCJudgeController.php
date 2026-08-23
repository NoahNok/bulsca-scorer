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


    public function confirmJudge(Competition $competition, SERC $serc, SERCJudge $judge)
    {

        return Inertia::render('Judge/Competition/SERC/ConfirmJudge', [
            'competition' => $competition->only(['id', 'name']),
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


        DigitalJudge::addClientJudge($request->judge);


        if ($competition->getScoringSettings->use_tanks) {
            return to_route('judge.competition.serc.select-tank', compact('competition', 'serc'));
        }
        DigitalJudge::setTank(null);

        return to_route('judge.competition.serc', compact('competition', 'serc'));
    }

    public function selectTank(Competition $competition, SERC $serc)
    {


        // grab serc again as the draw is against the serc with the lowest id?
        $tanks = SERC::where('competition', $competition->id)->first()->draw()->orderBy('tank')->distinct('tank')->get('tank')->pluck('tank')->toArray();

        return Inertia::render('Judge/Competition/SERC/SelectTank', ['competition' => $competition, 'serc' => $serc, 'tanks' => $tanks]);
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
            'competition' => $competition->only(['id', 'name', 'show_teams_to_judges']),
            'serc' => $serc->only(['name', 'id']),
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
            'draws' => $draws->map(function ($draw) use ($competition) {
                return [
                    'draw' => $draw->draw,
                    'entity' => [
                        'id' => $draw->entity->id,
                        'name' => $draw->entity->getName($competition)
                    ]
                ];
            })->values(),

        ]);
    }

    public function addJudge(Competition $competition, SERC $serc, Request $request)
    {
        $swap = $request->has('swap');


        $selectedJudges = DigitalJudge::getClientJudges();
        $serc = $selectedJudges->first()->getSERC;



        $unselectedJudges = $serc->getJudges()->whereNotIn('id', $selectedJudges->pluck('id'))->get();

        return Inertia::render('Judge/Competition/SERC/AddJudge', [
            'competition' => $competition->only(['id', 'name']),
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



        $j = DigitalJudge::getClientJudges()[0];

        $nextTeamId = null;

        $serc = $j->getSERC;
        $draw = $serc->getDraw;

        // Check if we are marking a tank
        $tank = DigitalJudge::getTank();
        if ($tank != null) {
            $draw = $draw->where('tank', $tank);
        }

        $draw = $draw->sortBy('draw');

        foreach ($draw as $allocation) {
            $marked = DigitalJudge::hasTeamBeenJudgedAlready($allocation->entity);
            if (!$marked) {

                $nextTeamId = $allocation->entity->id;
                break;
            }
        }


        if ($nextTeamId == null) {

            $judgeNote = OverallJudgeNote::where('judge', $j->id)->first();
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

        if (!DigitalJudge::isClientHeadJudge() && DigitalJudge::hasTeamBeenJudgedAlready($team)) return to_route("judge.competition.serc.mark.next", compact('competition', 'serc'));

        $draw_info = $serc->getPositionInDraw($team);

        $judgeName = DigitalJudge::getClientJudges()[0]->name;

        DigitalJudge::setStatus($competition, $judgeName . ' | Marking ' . $team->getName() . ' (' . $draw_info['text'] . ')', $serc);

        return Inertia::render("Judge/Competition/SERC/JudgeEntity", [
            'competition' => $competition->only(['id', 'name', 'show_teams_to_judges']),
            'serc' => $serc->only(['id', 'name']),
            'judges' => DigitalJudge::getClientJudges()->map(function ($judge) {
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
            'entity' => [
                'id' => $team->id,
                'name' => $team->getName()
            ],
            'draw' => $serc->getPositionInDraw($team),

        ]);
    }

    public function storeEntityMarks(Competition $competition, SERC $serc, int $entity_id, StoreEntityMarksRequest $request)
    {

        $judges = $request->validated();
        $entity = $serc->getScorableEntity()->findOrFail($entity_id);

        // fake 5s delay to simulate network latency for testing



        // validated payload from 
        // {
        //     judge_id: number;
        //     marks: {
        //         marking_point_id: number;
        //         mark: number;
        //     }[];
        //     notes?: string;
        // }[]

        foreach ($judges as $judge) {
            $judge_id = $judge['judge_id'];
            $marks = $judge['marks'];
            $notes = $judge['notes'] ?? null;

            foreach ($marks as $mark) {
                $marking_point_id = $mark['marking_point_id'];
                $mark_value = $mark['mark'];

                $result = SERCResult::firstOrNew(['marking_point' => $marking_point_id, 'entity_type' => $entity->getMorphClass(), 'entity_id' => $entity->id]);
                $result->result = $mark_value;
                $result->save();
            }

            $judgeNote = JudgeNote::firstOrNew(['judge' => $judge_id, 'entity_type' => $entity->getMorphClass(), 'entity_id' => $entity->id]);

            if ($notes === null || $notes === '') {
                if ($judgeNote->id) $judgeNote->delete();
                continue;
            } else {
                $judgeNote->note = $notes;
                $judgeNote->save();
            }
        }
    }

    public function storeOverallNotes(Competition $competition, SERC $serc, StoreOverallNotesRequest $request)
    {
        $data = $request->validated();

        $judge = DigitalJudge::getClientJudges()[0];

        $note = OverallJudgeNote::firstOrNew(['judge' => $judge->id]);

        if ($data['note'] == ''  && $note->id) {
            $note->delete();
        } else if ($data['note'] != '' && $data['note'] != null) {
            $note->note = $data['note'];
            $note->save();
        }

        Inertia::flash('toast', ['variant' => 'success', 'title' => "Overall note saved"]);
    }

    public function getJudgeNotes(int $competition, int $serc)
    {
        $judges = DigitalJudge::getClientJudges();

        $notes = $judges->map(function ($judge) {
            return [
                'name' => $judge->name,
                'notes' => $judge->getNotes()->with('entity')->get(['note', 'entity_id', 'entity_type'])->map(function ($note) {
                    return [
                        'entity' => $note->entity->jsonable(),
                        'note' => $note->note
                    ];
                })
            ];
        });

        return response()->json($notes);
    }

    public function getPreviousMarks(int $competition, int $serc, int $judge_id)
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
                        'entity' => $draw->entity->jsonable(),
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
