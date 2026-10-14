<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\ConfirmJudgeRequest;
use App\Http\Requests\DigitalJudge\JoinCompetition;
use App\Http\Requests\DigitalJudge\JoinCompetitionRequest;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\Judge;
use App\Models\SERC;
use App\Models\SERCJudge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class JudgeController extends Controller
{
    public function index()
    {
        $judge = Auth::user();

        return Inertia::render("Judge/Dashboard", [
            "competitions" => $judge->officiating->map(function ($comp) {
                return ["id" => $comp->id, "name" => $comp->name, "where" => $comp->where, "when" => $comp->when->format('M jS Y')];
            }),
        ]);
    }

    public function joinCompetition(JoinCompetitionRequest $request)
    {

        $competition = Competition::where('digitalJudgePin', $request->pin)->first();

        if (!$competition) {
            return back()->withInput()->withErrors(['pin' => 'No competition found.']);
        }

        $request->user()->officiating()->syncWithoutDetaching($competition);

        return to_route('judge.competition', ['competition' => $competition->getSlug()]);
    }

    public function home(Competition $competition)
    {
        DigitalJudge::clearClientJudges();
        DigitalJudge::setTank(null);

        $canSeeSercs = $competition->canSeeSercs(Auth::user());

        return Inertia::render("Judge/Competition/Home", [
            'competition' => $competition,
            'sercsReleased' => $competition->sercsReleased(),
            'sercs' => $competition->getSERCs()->where('digitalJudgeEnabled', true)->get()->map(function ($serc) use ($canSeeSercs) {

                // Judge names can describe the casualties, so a locked SERC only gives its name away
                if (!$canSeeSercs) {
                    return [
                        'id' => $serc->id,
                        'name' => $serc->getName(),
                        'type' => 'serc',
                        'locked' => true,
                    ];
                }

                return [
                    'id' => $serc->id,
                    'name' => $serc->getName(),
                    'type' => 'serc',
                    'completed' => $serc->completed || $serc->isComplete(),
                    'confirmed' => $serc->digitalJudgeConfirmed,
                    'judges' => $serc->getJudges->map(function ($judge) {
                        return [
                            'id' => $judge->id,
                            'name' => $judge->name
                        ];
                    })
                ];
            })->toArray(),
            'speeds' => $competition->getSpeedEvents()->where('digitalJudgeEnabled', true)->get()->map(function ($serc) {
                return [
                    'id' => $serc->id,
                    'name' => $serc->getName(),
                    'type' => 'speed',
                    'completed' => $serc->completed || $serc->isComplete(),
                    'confirmed' => $serc->digitalJudgeConfirmed,
                ];
            })->toArray(),
        ]);
    }

    public function setSercsReleased(Competition $competition, Request $request)
    {
        $validated = $request->validate(['released' => 'required|boolean']);

        $competition->setSercsReleased($validated['released']);

        Inertia::flash('toast', ['variant' => 'success', 'title' => $competition->sercsReleased() ? 'SERCs released to officials' : 'SERCs locked']);

        return back();
    }

    public function toggleReferee(Competition $competition)
    {

        $isReferee = DigitalJudge::isClientHeadJudge($competition);



        // Lets also set the value in hte pivot role
        $competition->setOfficialRole(Auth::user(), $isReferee ? 'official' : 'referee');


        Inertia::flash('toast', ['variant' => 'success', 'title' => $isReferee ? "You are nolonger a referee" : "You are now a referee"]);
    }
}
