<?php

namespace App\Http\Controllers;

use App\Http\Requests\Competition\CreateCompetitionAccount;
use App\Http\Requests\Competition\CreateCompetitionRequest;
use App\Http\Requests\Competition\DeleteCompetitionRequest;
use App\Http\Requests\Competition\EditCompetitionAccount;
use App\Http\Requests\Competition\UpdateCompetitionRequest;
use App\Http\Requests\Competition\UpdateCompetitionScoringSettingsRequest;
use App\Models\Competition;
use App\Models\Competition\CompetitionScoringSettings;
use App\Models\Organisation\Organisation;
use App\Models\User;
use App\Models\UserCompetitionAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\NotImplementedException;

class CompetitionController extends Controller
{
    //

    public function index()
    {
        $c = Competition::orderBy('when')->paginate(12);


        if (!auth()->user()->isAdmin()) return back();


        return view('competitions', ['comps' => $c]);
    }

    public function view(Competition $comp, Request $request)
    {


        return view('competition.view', ['comp' => $comp]);
    }

    public function events(Competition $comp, Request $request)
    {


        return view('competition.events', ['comp' => $comp]);
    }


    public function teams(Competition $comp, Request $request)
    {


        return view('competition.teams', ['comp' => $comp]);
    }

    public function competitors(Competition $comp, Request $request)
    {


        return view('competition.competitors', ['comp' => $comp]);
    }

    public function createCompetitionStats(Competition $comp)
    {

        $this->authorize('access', [$comp, 'admin']);

        $comp->generateStats();

        return back()->with('success', 'Stats created');
    }



    public function updateCompetitionSettings(Competition $comp, UpdateCompetitionRequest $request)
    {

        $validated = $request->validated();
        $settings = $comp->getScoringSettings;

        $updatedNameFormats = false;

        foreach ($validated as $key => $value) {

            if ($key == "timezone") {
                continue;
            }

            if ($key == 'team_format' || $key == 'competitor_format') {
                $updatedNameFormats = true;
            }

            if (is_string($value) && strtotime($value)) {

                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $value = Carbon::parse($value);
                } else {
                    $value = Carbon::parse($value, $validated['timezone']);
                    $value->setTimezone('UTC');
                }
            }

            if (str_starts_with($key, 'ss:') && $value != null) {
                $settings->{substr($key, 3)} = $value;
                continue;
            }

            $comp->$key = $value;
        }

        $settings->save();
        $comp->save();

        if ($updatedNameFormats) {
            $comp->clearEntityNameCache();
        }

        return response()->json([]);
    }

    public function updateCompetitionScoringSettings(Competition $comp, UpdateCompetitionScoringSettingsRequest $request)
    {

        $validated = $request->validated();

        $settings = $comp->getScoringSettings;




        foreach ($validated as $key => $value) {

            if ($key == 'heats_per_event' || $key == 'seed_per_event' || $key == 'use_seeds') {
                $comp->{$key} = $value;
                continue;
            }


            $settings->$key = $value;
        }



        $comp->save();

        $settings->save();

        return response()->json([]);
    }

    public function inviteCompetitionAccount(Competition $comp, CreateCompetitionAccount $request)
    {

        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        // if (!$user) {
        //     return response()->json([
        //         'error' => 'No account found with that email.'
        //     ]);
        // }

        if ($user && $comp->userBelongsToCompetition($user)) {
            return response()->json([
                'error' => 'Account already part of this organisation.'
            ]);
        }

        if ($comp->getInvites()->where('email', $validated['email'])->exists()) {
            return response()->json([
                'error' => 'Email has already been invited'
            ]);
        }

        AccountInviteController::invite($validated['email'], $comp, ['access' => $validated['access']]);
        // $organisation->addAccount($user, $request->input('access'));

        session()->flash('success', "Account invited.");

        return response()->json([]);
    }

    public function getCompetitionAccounts(Competition $comp)
    {

        // Get all users that have access to this competition via access table

        $accounts = [];

        foreach (
            UserCompetitionAccess::where('competition', $comp->id)
                ->get()->groupBy('user') as $user_id => $access
        ) {

            $user = User::find($user_id);

            $accounts[] = [
                'id' => $user->id,
                'name' => $user->name . (Auth::user() == $user ? ' (You)' : ''),
                'email' => $user->email,
                'access' => $access->pluck('access_to')->map(function ($item) {
                    return Competition::$accessTypes[$item] ?? $item;
                })->toArray(),
            ];
        }

        return response()->json($accounts);
    }

    public function getCompetitionAccount(Competition $comp, User $account)
    {
        // Get all access for this user in this competition

        $access = UserCompetitionAccess::where('competition', $comp->id)
            ->where('user', $account->id)
            ->get();

        if ($access->isEmpty()) {
            return response()->json(['error' => 'No access found for this user in this competition']);
        }

        return response()->json([
            'id' => $account->id,
            'name' => $account->name,
            'email' => $account->email,
            'access' => $access->pluck('access_to')->toArray(),
        ]);
    }

    public function editCompetitionAccount(Competition $comp, User $account, EditCompetitionAccount $request)
    {
        $request->validated();

        $response = $comp->editCompetitionAccount($account, $request->input('access'));

        if (is_string($response)) {
            return response()->json(['error' => $response]);
        }

        return response()->json([]);
    }

    public function deleteCompetitionAccount(Competition $comp, User $account)
    {

        $user = auth()->user();

        if (!$user->isAdmin() &&  $user->competition != $comp->id) {
            // if the user is not an admin or does not have access to this competition, return error
            return response()->json(['error' => 'You do not have permission to delete this account'], 403);
        }





        $comp->deleteCompetitionAccount($account);

        return response()->json([]);
    }

    public function create()
    {
        return view('competition.create');
    }

    public function createPost(CreateCompetitionRequest $request)
    {
        $validated = $request->validated();


        $comp = new Competition();
        $comp->name = $validated['name'];
        $comp->when = $validated['when'];
        $comp->where = $validated['where'];
        $comp->max_lanes = $validated['lanes'];
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->save();


        if ($validated['org'] != 'null') {
            $organisation = Organisation::find($validated['org']);

            if ($organisation == null) {
                return response()->json(['error' => 'No organisation exists with that Id.']);
            }

            if (!Auth::user()->can('access', [$organisation, 'admin'])) {
                return response()->json(['error' => "You need to be an admin of {$organisation->name} to add competitions to it."]);
            }

            $organisation->getCompetitions()->save($comp);
        } else {
            $comp->addAccount(Auth::user(), ['owner']);
        }


        $css = new CompetitionScoringSettings();
        $css->use_tanks = true;
        $css->competition = $comp->id;
        $css->save();

        $request->session()->flash('success', 'Competition created!');

        return response()->json(['url' => route('comps.view', ['comp' => $comp])]);
    }

    public function deleteComp(Competition $comp, DeleteCompetitionRequest $request)
    {
        $validated = $request->validated();

        if ($validated['name'] != $comp->name) {
            return response()->json(['error' => "Confirmation name doesn't match!"]);
        }

        $comp->delete();

        $request->session()->flash('success', 'Competition deleted!');

        return response()->json([]);
    }
}
