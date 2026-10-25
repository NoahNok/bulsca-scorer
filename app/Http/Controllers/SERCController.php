<?php

namespace App\Http\Controllers;

use App\Helpers\ClassHelpers;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\League;
use App\Models\SERC;
use App\Models\SERCDisqualification;
use App\Models\SERCJudge;
use App\Models\SERCPenalty;
use App\Models\SERCResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SERCController extends Controller
{





    public function restrictionMap(Competition $comp, SERC $serc)
    {
        $entities = $serc->getScorableEntities();

        $restrictionMap = $entities->map(function ($entity) use ($comp, $serc) {
            $objectives = $serc->leagueObjectivesFor($entity);

            return [
                'entity_name' => $entity->getName($comp),
                'league_names' => $entity->leagues->pluck('name')->all(),
                'objective' => $objectives->count() == 1 ? $objectives->first()->name : null,
                'problem' => match ($objectives->count()) {
                    0 => 'no_objective',
                    1 => null,
                    default => 'several_objectives',
                },
            ];
        })->values()->all();

        return view('competition.events.sercs.restriction-map', [
            'comp' => $comp,
            'serc' => $serc,
            'restrictionMap' => $restrictionMap,
            'totalEntities' => $entities->count()
        ]);
    }





    public function editResultsView(Competition $comp, SERC $serc, int $entity_id)
    {

        $team = $serc->getScorableEntity()::where('competition', $comp->id)->findOrFail($entity_id);

        return view('competition.events.sercs.edit-team-results', ['comp' => $comp, 'serc' => $serc, 'team' => $team]);
    }

    public function updateTeamResults(Competition $comp, SERC $serc, int $entity_id, Request $request)
    {
        $team = $serc->getScorableEntity()::where('competition', $comp->id)->findOrFail($entity_id);

        $json = json_decode($request->input('data'));
        $sawDQ = false;
        $sawPen = false;

        $changes = [];

        $marking_points = $serc->getMarkingPoints()->select('serc_marking_points.id', 'serc_marking_points.name', 'serc_marking_points.judge', 'serc_judges.name AS judge_name')->get();

        foreach ($json as $mp) {

            if ($mp->id == "disqualification") {
                $sawDQ = true;
                $serc->clearEntityDisqualifications($team);
                $dq = str_replace('dq', '', strtolower(trim($mp->values->disqualification)));
                $serc->addEntityDisqualification($team, $dq);
                continue;
            }

            if ($mp->id == "penalties") {
                $sawPen = true;
                $serc->clearEntityPenalties($team);
                foreach (explode(',', $mp->values->penalties) as $pen) {
                    $pen = str_replace('p', '', strtolower(trim($pen)));
                    $serc->addEntityPenalty($team, $pen);
                }
                continue;
            }


            $id = $mp->id;
            $score = $mp->values->score ?: 0;

            if ($score > 10) $score = 10;
            if ($score < 0) $score = 0;

            $result = SERCResult::where('marking_point', $id)->forEntity($team)->first();

            $was = null;

            if (!$result) {
                $result = new SERCResult();
                $result->marking_point = $id;
            } else {
                $was = $result->result;
            }

            $result->result = $score;
            $result->entity()->associate($team);

            $result->save();

            if ($was != $score) {
                $mp_record = $marking_points->find($id);
                $changes[] = ['name' => "{$mp_record->judge_name} - {$mp_record->name}", 'old' => $was, 'new' => $score];
            }

            Cache::forget('mp.' . $id . '.team.' . $team->id);
        }

        if (!$sawDQ) {
            $serc->clearEntityDisqualifications($team);
        }

        if (!$sawPen) {
            $serc->clearEntityPenalties($team);
        }


        $nextTeamId = $this->nextTeamId($comp, $serc, $entity_id);

        session()->flash('success', "Results updated for " . $team->getName($comp));

        $changeCount = count($changes);
        if ($changeCount > 0) {
            $serc->recordActivity('SERC_RESULT_UPDATED', $changeCount > 1 ? "{$changeCount} results changed" : "1 result changed", context: [
                'changes' => $changes,
            ], related: [$serc, $team, $comp]);
        }


        if (!$nextTeamId) {
            return response()->json(['sid' => $serc->id]);
        }

        return response()->json(['url' => Route('comps.events.sercs.editResults', [$comp, $serc, $nextTeamId])]);
    }

    private function nextTeamId(Competition $comp, SERC $serc, int $entity_id): ?int
    {
        $team = $serc->getScorableEntity()::where('competition', $comp->id)->findOrFail($entity_id);

        $teamIds = $serc->getScorableEntities()->pluck('id')->toArray();

        $draw = $serc->getDraw->sortBy('draw')->sortBy('tank');

        if ($draw) {
            $teamIds = $draw->pluck('entity_id')->toArray();
        }
        $index = array_search($team->id, array_values($teamIds));



        if ($index + 2 > count($teamIds)) {

            return null;
        }

        return $teamIds[$index + 1];
    }

    public function next(Competition $comp, SERC $serc, int $entity_id)
    {
        $nextTeamId = $this->nextTeamId($comp, $serc, $entity_id);

        if (!$nextTeamId) {
            return redirect()->route('manage.competitions.events.sercs.show', [$comp, $serc]);
        }

        return redirect()->route('comps.events.sercs.editResults', [$comp, $serc, $nextTeamId]);
    }


    public function addSercImage(Competition $comp, SERC $serc, Request $request)
    {
        $request->validate([
            'image' => 'nullable|image',
        ]);

        $oldImage = $serc->image;
        if ($request->hasFile('image')) {
            // Remove old file



            if ($oldImage !== null) {
                unlink(public_path() . '/storage/' . $oldImage);
            }




            $serc->image = $request->file('image')->store('serc-images', 'public');
            $serc->save();
        }

        return redirect()->back();
    }

    public function removeSercImage(Competition $comp, SERC $serc)
    {
        unlink(public_path() . '/storage/' . $serc->image);
        $serc->image = null;
        $serc->save();
        return redirect()->back();
    }




    public function printResults(Competition $comp, SERC $serc)
    {
        $data = [[
            'league' => 'Overall',
            'results' => $serc->getRankedResults(),
        ]];

        foreach ($comp->getLeagues->sortBy('name') as $league) {
            $results = $serc->getRankedResults($league);

            $data[] = [
                'league' => $league->name,
                'results' => $results,
            ];
        }

        return view('competition.events.sercs.print', ['comp' => $comp, 'serc' => $serc, 'data' => $data]);
    }

    public function markSplits(Competition $comp, SERC $serc)
    {
        return view('competition.events.sercs.mark-splits', ['comp' => $comp, 'serc' => $serc]);
    }

    public function loadMarkSplit(Competition $comp, SERC $serc, SERCJudge $judge)
    {
        $marking_points = $serc->getMarkingPoints()->where('judge', $judge->id)->get();

        $results = SERCResult::with('entity')->whereIn('marking_point', $marking_points->pluck('id'))->get();
        $grouped = $results->groupBy('entity_id')->map(function ($item) {
            return $item->sortBy('marking_point')->mapWithKeys(function ($mp) {
                return [
                    $mp->marking_point => [
                        'result' => $mp->result,
                        'date_recorded' => $mp->created_at->toDateTimeString()
                    ],
                ];
            });
        });

        $draw = $serc->getDraw->sortBy('draw')->sortBy('tank');




        $data = [];

        foreach ($draw as $draw_team) {

            $entity = $draw_team->entity;
            $entity_results = $grouped->get($draw_team->entity_id, collect([]));
            $data[] = [
                'entity' => "{$draw_team->draw}. {$entity->getName($comp)}",
                'results' => $entity_results,
            ];
        }


        // group by the entity and then order by marking point id


        $marking_points = $marking_points->sortBy('id')->map(function ($mp) {
            return ['id' => $mp->id, 'name' => $mp->name];
        })->toArray();



        return response()->json(['marking_points' => $marking_points, 'results' => $data]);
    }
}
