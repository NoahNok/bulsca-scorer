<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Championship;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LandingController extends Controller
{

    public function explore()
    {

        $comps = Competition::where('show_competition', true)->orderBy('when', 'desc')->paginate(12);
        $orgs = Organisation::orderBy('name')->paginate(12, ['*'], 'orgs_page');
        $ongoing = Competition::where('show_competition', true)->whereDate('when', Carbon::today())->first();


        return view('landing.explore', compact(['comps', 'orgs', 'ongoing']));
    }

    public function showOrganisation(Organisation $organisation)
    {
        return view('landing.organisation', ['org' => $organisation, 'ongoing' => $organisation->getOngoingCompetition()]);
    }

    public function showChampionship(Championship $championship)
    {
        $organisation = $championship->organisation;
        return view('landing.championship', ['org' => $organisation, 'championship' => $championship]);
    }

    public function showCompetition(Competition $comp)
    {
        return view('landing.competition.overview', ['comp' => $comp]);
    }

    public function showHeatsAndDraws(Competition $comp)
    {
        return view('landing.competition.heats-draws', ['comp' => $comp]);
    }

    public function showResults(Competition $comp)
    {
        return view('landing.competition.results', ['comp' => $comp]);
    }

    public function search(string $search)
    {
        $dbComps = Competition::selectRaw("*,
            CASE
                WHEN name = ? THEN 3
                WHEN name LIKE ? THEN 2
                WHEN name LIKE ? THEN 1
                ELSE 0
            END as relevance
            ", [$search, "$search%", "%$search%"])
            ->where('name', 'LIKE', "%$search%")
            ->where('show_competition', true)
            ->orderByDesc('relevance')->limit(6)->get();

        $comps = [];
        foreach ($dbComps as $comp) {

            $comps[] = [
                'id' => $comp->id,
                'name' => $comp->name,
                'when' => $comp->when->format('M jS Y'),
                'where' => $comp->where,
                'url' => route('landing.competition', $comp->getSlug())
            ];
        }

        $orgs = Organisation::selectRaw("id, name, logo,
            CASE
                WHEN name = ? THEN 3
                WHEN name LIKE ? THEN 2
                WHEN name LIKE ? THEN 1
                ELSE 0
            END as relevance
            ", [$search, "$search%", "%$search%"])
            ->where('name', 'LIKE', "%$search%")
            ->orderByDesc('relevance')->limit(8)->get();

        foreach ($orgs as $org) {
            $org->logo = $org->getLogo();
            $org['url'] = route('landing.organisation', $org->name);
        }

        $championships = Championship::selectRaw("id, name, organisation_id,
            CASE
                WHEN name = ? THEN 3
                WHEN name LIKE ? THEN 2
                WHEN name LIKE ? THEN 1
                ELSE 0
            END as relevance
            ", [$search, "$search%", "%$search%"])
            ->where('name', 'LIKE', "%$search%")
            ->orderByDesc('relevance')->limit(8)->get();

        foreach ($championships as $champ) {
            $champ['url'] = route('landing.championship', $champ->slug());
        }



        return response()->json(compact('comps', 'orgs', 'championships'));
    }
}
