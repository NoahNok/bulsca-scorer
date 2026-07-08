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
use App\Models\User;
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

    public function login(Request $request)
    {
        return Inertia::render("Judge/Auth/Login", [
            'stage' => 'email',
            'email' => $request->old('email', ''),
        ]);
    }

    public function resendPin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $judge = User::where('email', $request->email)->first();

        if (!$judge) {
            return back()->withInput();
        }

        $judge->sendOneTimePassword();

        return back()->with('success', 'A new one-time password has been sent to your email. Please check your inbox and log in using the OTP.');
    }

    public function loginPost(Request $request)
    {

        // pin only required if query string step=pin is present

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'pin'  => 'required_if:step,pin|digits:6',
        ]);

        $judge = User::where('email', $request->email)->first();

        if (!$judge) {
            return back()->with(['email' => 'Invalid Email'])->withInput();
        }

        // if pin is present, attempt to log in
        if ($request->has('pin')) {
            $result = $judge->attemptLoginUsingOneTimePassword($request->input('pin'), remember: true);

            if ($result->isOk()) {
                // it is best practice to regenerate the session id after a login   
                $request->session()->regenerate();


                return Inertia::render("Judge/Dashboard");
            } else {
                return Inertia::render("Judge/Auth/Login", [
                    'stage'  => 'pin',
                    'email'  => $request->email,
                    'errors' => ['pin' => 'Invalid PIN. Please try again.'],
                ]);
            }
        }

        $judge->sendOneTimePassword();

        return Inertia::render("Judge/Auth/Login", [

            'stage'  => 'pin',
            'email'  => $request->email,
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

        return Inertia::render("Judge/Competition/Home", [
            'competition' => $competition,
            'sercs' => $competition->getSERCs()->where('digitalJudgeEnabled', true)->get()->map(function ($serc) {

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
                    'type' => 'serc',
                    'completed' => $serc->completed || $serc->isComplete(),
                    'confirmed' => $serc->digitalJudgeConfirmed,
                ];
            })->toArray(),
        ]);
    }

    public function confirmJudge(Competition $competition, SERCJudge $judge)
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

    public function confirmJudgePost(Competition $competition, ConfirmJudgeRequest $request)
    {


        DigitalJudge::addClientJudge($request->judge);


        if ($competition->getScoringSettings->use_tanks) {
            return to_route('judge.competition.serc.select-tank', ['competition' => $competition]);
        }
        DigitalJudge::setTank(null);

        return to_route('judge.competition.serc', ['competition' => $competition]);
    }

    public function selectTank(Competition $competition)
    {

        $tanks = SERC::where('competition', $competition->id)->first()->draw()->orderBy('tank')->distinct('tank')->get('tank')->pluck('tank')->toArray();

        return Inertia::render('Judge/Competition/SERC/SelectTank', ['competition' => $competition, 'tanks' => $tanks]);
    }

    public function setTank(Competition $competition, int $tank)
    {
        DigitalJudge::setTank($tank);

        return to_route('judge.competition.serc', ['competition' => $competition]);
    }

    public function sercHome(Competition $competition)
    {

        $judges = DigitalJudge::getClientJudges();
        $serc = $judges->first()->getSERC;

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
            'competition' => $competition->only(['id', 'name']),
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
            })->values()
        ]);
    }

    public function addJudge(Competition $competition, Request $request)
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

    public function attachJudge(Competition $competition, SERCJudge $judge, Request $request)
    {

        if ($request->has('swap')) {
            DigitalJudge::clearClientJudges();
        }

        DigitalJudge::addClientJudge($judge->id);

        return to_route('judge.competition.serc', ['competition' => $competition]);
    }

    public function detachJudge(Competition $competition, SERCJudge $judge)
    {
        DigitalJudge::removeClientJudge($judge->id);

        return to_route('judge.competition.serc', ['competition' => $competition]);
    }
}
