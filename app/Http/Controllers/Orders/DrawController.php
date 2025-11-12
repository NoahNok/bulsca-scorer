<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\Orders\Draw;
use App\Models\SERC;
use App\Services\DrawService;
use Illuminate\Http\Request;

class DrawController extends Controller
{
    public function generate(Competition $comp, DrawService $drawService)
    {

        if ($comp->getScoringSettings->use_tanks) {
            return redirect()->route('comps.heats_and_draws.draws.tank_setup', $comp);
        }

        $serc = $comp->getSERCs()->orderBy('id')->first();

        $drawService->generateDrawForSERC($serc);

        $comp->clearDrawCache();

        return redirect()->back()->with('success', 'Draw Generated');
    }

    public function hide(Competition $comp)
    {

        $comp->show_draws = !$comp->show_draws;
        $comp->save();

        return redirect()->back();
    }

    public function edit(Competition $comp, SERC $serc)
    {

        return view('competition.heats-and-orders.draws.edit', compact('comp', 'serc'));
    }

    public function swap(Competition $comp, SERC $serc, DrawService $drawService)
    {

        $swap_from = Draw::find(request()->input('swap_from'));
        $swap_to = Draw::find(request()->input('swap_to'));

        $drawService->swapInDraw($swap_from, $swap_to);

        if (!$swap_from || !$swap_to) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid draw selections',
            ], 400);
        }

        $comp->clearDrawCache();

        return response()->json([
            'status' => 'success',
            'tanks' => $serc->getTankDraw(),
        ]);
    }

    public function reset(Competition $comp, SERC $serc, DrawService $drawService)
    {
        $drawService->generateDrawForSERC($serc);

        $comp->clearDrawCache();


        return redirect()->back()->with('success', 'Draw Reset');
    }

    public function tankSetup(Competition $comp)
    {
        return view('competition.heats-and-orders.draws.draw', ['comp' => $comp]);
    }

    public function tankSetupPost(Competition $comp, Request $request)
    {
        $allCompetitorsPerLeague = CompetitionTeam::where('competition', $comp->id)->with('leagues')->get()->groupBy(function ($entity) {
            return optional($entity->getLeague())->id;
        });

        $tank_target = $request->json()->all();

        $targetSerc = SERC::where('competition', $comp->id)->orderBy('id')->first();

        // remove old draw 
        Draw::where('serc', $targetSerc->id)->delete();

        foreach ($tank_target as $ind => $tank) {
            $tankTotal = 0;

            foreach ($tank as $bracket) {
                $leagueId = $bracket['league'];

                if ($leagueId === -1) {
                    $leagueId = null;
                }

                $competitors = $allCompetitorsPerLeague->get($leagueId);

                $competitors = $competitors->shuffle();

                foreach ($competitors as $competitor) {
                    $tankTotal++;
                    $draw = new Draw();
                    $draw->entity()->associate($competitor);
                    $draw->serc = $targetSerc->id;

                    $draw->tank = $ind + 1;
                    $draw->draw = $tankTotal;

                    $draw->save();
                }
            }
        }

        $comp->clearDrawCache();

        return response()->json();
    }
}
