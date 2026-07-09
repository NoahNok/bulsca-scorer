<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organisation\Championship\CreateChampionshipRequest;
use App\Models\Championship;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChampionshipController extends Controller
{

    public function view(Organisation $organisation)
    {

        $championships = $organisation->championships()->paginate(10);

        return view('organisation.championship.index', ['org' => $organisation, 'championships' => $championships]);
    }

    public function show(Organisation $organisation, Championship $championship)
    {


        return view('organisation.championship.show', ['org' => $organisation, 'championship' => $championship]);
    }

    public function create(Organisation $organisation)
    {
        return view('organisation.championship.create', ['org' => $organisation]);
    }

    public function store(CreateChampionshipRequest $request, Organisation $organisation)
    {
        $validated = $request->validated();

        $championship = $organisation->championships()->create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return redirect()->route('orgs.championship.view', ['organisation' => $organisation->name, 'championship' => $championship]);
    }

    public function addCompetition(Organisation $organisation, Championship $championship)
    {
        $competitions = $organisation->getCompetitions()->whereDoesntHave('championship', function ($query) use ($championship) {
            $query->where('championship_id', $championship->id);
        })->paginate(10);

        return view('organisation.championship.add-competition', [
            'org' => $organisation,
            'championship' => $championship,
            'comps' => $competitions,
            'types' => array_slice(Competition::$types, 1), // Exclude the first type (STANDALONE)
        ]);
    }

    public function associateCompetition(Organisation $organisation, Championship $championship, Request $request)
    {
        // Chekc user has access to manage the championship
        $validated = $request->validate([
            'competition_id' => 'required|exists:competitions,id',
            'type' => 'required|in:' . implode(',', Competition::$types),
        ]);

        $competition = Competition::findOrFail($validated['competition_id']);

        if (!$organisation->canUser(Auth::user(), 'admin')) {
            abort(403, 'You do not have permission to manage this championship');
        }

        // check competition belongs to organisation
        if ($competition->organisation !== $organisation->id) {
            abort(403, 'Competition does not belong to this organisation');
        }


        // Associate the competition with the championship
        $competition->championship()->associate($championship);
        $competition->type = $validated['type'];
        $competition->save();

        return redirect()->route('orgs.championship.view', ['organisation' => $organisation->name, 'championship' => $championship]);
    }

    public function deassociateCompetition(Organisation $organisation, Championship $championship, Request $request)
    {
        // validator to get competition
        $validated = $request->validate([
            'competition_id' => 'required|exists:competitions,id',
        ]);

        $competition = Competition::findOrFail($validated['competition_id']);


        // Chekc user has access to manage the championship
        if (!$organisation->canUser(Auth::user(), 'admin')) {
            abort(403, 'You do not have permission to manage this championship');
        }

        // check competition belongs to organisation
        if ($competition->organisation !== $organisation->id) {
            abort(403, 'Competition does not belong to this organisation');
        }

        // check competition is associated with the championship
        if ($competition->championship_id !== $championship->id) {
            abort(403, 'Competition is not associated with this championship');
        }

        // Deassociate the competition from the championship
        $competition->championship()->dissociate();
        $competition->type = 'STANDALONE';
        $competition->save();

        return redirect()->route('orgs.championship.view', ['organisation' => $organisation->name, 'championship' => $championship]);
    }
}
