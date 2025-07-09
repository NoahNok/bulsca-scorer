<?php

namespace App\Http\Controllers;

use App\Http\Requests\Competition\CreateCompetitionAccount;
use App\Http\Requests\Competition\CreateCompetitionRequest;
use App\Http\Requests\Competition\EditCompetitionAccount;
use App\Models\Competition;
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
        $request->session()->put('ac', $comp);

        return view('competition.view', ['comp' => $comp]);
    }

    public function events(Competition $comp, Request $request)
    {
        $request->session()->put('ac', $comp);

        return view('competition.events', ['comp' => $comp]);
    }


    public function teams(Competition $comp, Request $request)
    {
        $request->session()->put('ac', $comp);

        return view('competition.teams', ['comp' => $comp]);
    }

    public function competitors(Competition $comp, Request $request)
    {
        $request->session()->put('ac', $comp);

        return view('competition.competitors', ['comp' => $comp]);
    }

    public function createCompetitionStats(Competition $comp)
    {

        $comp->generateStats();

        return back()->with('success', 'Stats created');
    }



    public function updateCompetitionSettings(Competition $comp, Request $request)
    {
        $comp->max_lanes = $request->input('lanes', $comp->max_lanes);
        $newDateTime = $request->input('serc_start_time', $comp->serc_start_time);
        $utcDate = Carbon::parse($newDateTime, 'BST');
        $utcDate->setTimezone('UTC');

        $comp->serc_start_time = $utcDate;
        $comp->can_be_live = $request->has('can_be_live');

        $comp->save();

        return;
    }

    public function createCompetitionAccount(Competition $comp, CreateCompetitionAccount $request)
    {

        $request->validated();

        $response = $comp->createCompetitionAccount($request->input('name'), $request->input('email'), $request->input('access'));

        if (is_string($response)) {
            return response()->json(['error' => $response]);
        }

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


        if ($validated['org'] != 'null') {
            throw new NotImplementedException();
        }


        $comp = new Competition();
        $comp->name = $validated['name'];
        $comp->when = $validated['when'];
        $comp->where = $validated['where'];
        $comp->isLeague = true;
        $comp->max_lanes = $validated['lanes'];
        $comp->anytimepin = $validated['anytimepin'];
        $comp->scoring_Version = "1.1.0"; // Must forcibly set the updated version 1.1.0 programatically - UPDATE THIS WITH EACH NEW SCORING UPDATE



        $comp->scoring_type = $validated['scoring_type'];
        $comp->save();

        // Add the user that created thee competition as an owner
        $comp->addAccount(Auth::user(), ['owner']);



        return response()->json(['url' => route('comps.view', ['comp' => $comp])]);
    }
}
