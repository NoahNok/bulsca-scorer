<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\Orders\EntityEventSeed;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    //

    public function view(Competition $comp)
    {
        return view('competition.entities', compact('comp'));
    }

    public function edit(Competition $comp)
    {
        return view('competition.entities.edit', compact('comp'));
    }

    public function save(Competition $comp, Request $request)
    {
        $request_data = $request->json()->all();

        $data = $request_data['data'];
        $removed = $request_data['removed'];

        // Handle removals
        Club::whereIn('id', $removed['clubs'])->delete();
        CompetitionTeam::whereIn('id', $removed['teams'])->delete();
        Competitor::whereIn('id', $removed['competitors'])->delete();

        // Add/update items
        $clubs = $data['clubs'];
        $teams = $data['teams'];
        $competitors = $data['competitors'];

        foreach ($clubs as $club) {
            $this->saveClub($comp, $club);
        }

        foreach ($teams as $team) {
            $this->saveTeam($comp, $team);
        }

        foreach ($competitors as $competitor) {
            $this->saveCompetitor($comp, $competitor);
        }

        session()->flash('success', 'Saved entries');

        return response()->json([]);
    }

    private function saveClub(Competition $comp, array $data)
    {
        $club = Club::updateOrCreate([
            'id' => $data['id'],
            'competition' => $comp->id
        ], [
            'name' => $data['name'],
            'league' => null
        ]);

        if (count($data['teams']) > 0) {
            foreach ($data['teams'] as $team) {
                $this->saveTeam($comp, $team, $club);
            }
        }
    }

    private function seedToMillis($seed): ?int
    {
        $minSecSplit = explode(":", $seed);

        if (count($minSecSplit) == 1) {
            return null;
        }

        $min = $minSecSplit[0];
        $secMillisSplit = explode(".", $minSecSplit[1]);

        if (count($secMillisSplit) == 1) {
            return null;
        }

        if (strlen($secMillisSplit[1]) == 2) {
            $secMillisSplit[1] = $secMillisSplit[1] * 10;
        }

        return $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];
    }

    private function saveTeam(Competition $comp, array $data, ?Club $club = null)
    {
        $team = CompetitionTeam::updateOrCreate([
            'id' => $data['id'],
            'competition' => $comp->id
        ], [
            'team' => $data['name'],
            'club' => $club?->id,
            'league' => $data['league']
        ]);

        foreach ($data['seeds'] as $event_id => $seed_data) {

            $seed_raw = $seed_data['seed'];

            if ($seed_raw == '' && $seed_data['id'] != null) {
                EntityEventSeed::find($seed_data['id'])->delete();
                continue;
            }

            $seed = $this->seedToMillis($seed_raw);

            if ($seed == null) {
                continue;
            }

            EntityEventSeed::updateOrCreate([
                'speed_event' => $event_id,
                'entity_type' => 'team',
                'entity_id' => $team->id
            ], [
                'seed' => $seed
            ]);
        }

        if (count($data['competitors']) > 0) {
            foreach ($data['competitors'] as $competitor) {
                $this->saveCompetitor($comp, $competitor, $team);
            }
        }
    }

    private function saveCompetitor(Competition $comp, array $data, ?CompetitionTeam $team = null)
    {
        $competitor = Competitor::updateOrCreate([
            'id' => $data['id'],
            'competition' => $comp->id
        ], [
            'name' => $data['name'],
            'team' => $team?->id,
            'league' => $data['league']
        ]);


        foreach ($data['seeds'] as $event_id => $seed_data) {

            $seed_raw = $seed_data['seed'];

            if ($seed_raw == '' && $seed_data['id'] != null) {
                EntityEventSeed::find($seed_data['id'])->delete();
                continue;
            }

            $seed = $this->seedToMillis($seed_raw);

            if ($seed == null) {
                continue;
            }

            EntityEventSeed::updateOrCreate([
                'speed_event' => $event_id,
                'entity_type' => 'competitor',
                'entity_id' => $competitor->id
            ], [
                'seed' => $seed
            ]);
        }
    }
}
