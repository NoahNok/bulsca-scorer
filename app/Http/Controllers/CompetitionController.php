<?php

namespace App\Http\Controllers;

use App\Http\Requests\Competition\CreateCompetitionAccount;
use App\Http\Requests\Competition\DeleteCompetitionRequest;
use App\Http\Requests\Competition\EditCompetitionAccount;
use App\Http\Requests\Competition\UpdateCompetitionRequest;
use App\Http\Requests\Competition\UpdateCompetitionScoringSettingsRequest;
use App\Models\Competition;
use App\Models\User;
use App\Models\UserCompetitionAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

    public function activityLog(Competition $comp, Request $request)
    {

        return view('competition.activity-log', ['comp' => $comp]);
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

            // Only the date fields: a name like "Monday" would parse as one too
            if ($key == 'when' && $value) {
                $value = Carbon::parse($value);
            } elseif ($key == 'serc_start_time' && $value) {
                $value = Carbon::parse($value, $validated['timezone'] ?? 'UTC')->setTimezone('UTC');
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

        return back()->with('success', 'Settings saved');
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
            throw ValidationException::withMessages(['email' => 'They can already manage this competition.']);
        }

        if ($comp->getInvites()->where('email', $validated['email'])->exists()) {
            throw ValidationException::withMessages(['email' => "They've already been invited."]);
        }

        AccountInviteController::invite($validated['email'], $comp, ['access' => $validated['access']]);
        // $organisation->addAccount($user, $request->input('access'));

        return back()->with('success', 'Invite sent');
    }

    public function editCompetitionAccount(Competition $comp, User $account, EditCompetitionAccount $request)
    {
        $request->validated();

        $response = $comp->editCompetitionAccount($account, $request->input('access'));

        if (is_string($response)) {
            throw ValidationException::withMessages(['access' => $response]);
        }

        return back()->with('success', 'Access saved');
    }

    public function deleteCompetitionAccount(Competition $comp, User $account)
    {

        $user = auth()->user();

        if (!$user->isAdmin() &&  $user->competition != $comp->id) {
            // if the user is not an admin or does not have access to this competition, return error
            throw ValidationException::withMessages(['account' => "You can't remove this account."]);
        }

        // The page hides Remove for these, as the old one refused them
        if ($account->is($user) || UserCompetitionAccess::where('competition', $comp->id)->where('user', $account->id)->where('access_to', 'owner')->exists()) {
            throw ValidationException::withMessages(['account' => "The owner and you can't be removed."]);
        }

        $response = $comp->deleteCompetitionAccount($account);

        if (is_string($response)) {
            throw ValidationException::withMessages(['account' => $response]);
        }

        return back()->with('success', 'Account removed');
    }

    public function deleteComp(Competition $comp, DeleteCompetitionRequest $request)
    {
        $validated = $request->validated();

        if ($validated['name'] != $comp->name) {
            throw ValidationException::withMessages(['name' => "That isn't the competition's name."]);
        }

        $comp->delete();

        return redirect()->route('manage.home')->with('success', 'Competition deleted');
    }
}
