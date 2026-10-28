<?php

namespace App\Http\Controllers;

use App\Helpers\CompetitionHeader;
use App\Models\Competition;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Pdf\CompetitionPdfCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PrintableController extends Controller
{

    /**
     * Printables (Svelte, in CompetitionLayout): each Pack's url and every SERC's rough mark sheets, or why
     * there's nothing to print yet
     */
    public function index(Competition $comp)
    {
        $noHeats = Heat::whereIn('speed_event', $comp->getSpeedEvents()->select('id'))->exists()
            ? null : $this->unavailable($comp, 'No heats yet', 'manage.competitions.heats_and_draws', 'heats_and_draws');

        $sercs = $comp->getSERCs()->orderBy('id')->get();
        $noSercs = $sercs->isEmpty()
            ? $this->unavailable($comp, 'No SERCs yet', 'manage.competitions.events', 'speed|serc|serc_writer') : null;
        $noDraw = $noSercs ?? ($comp->drawSerc()->draw()->exists()
            ? null : $this->unavailable($comp, 'No draw yet', 'manage.competitions.heats_and_draws', 'heats_and_draws'));

        return Inertia::render('Manage/Competition/Printables', [
            'competition' => CompetitionHeader::for($comp),
            'packs' => [
                'chiefTimekeeper' => [
                    'url' => $noHeats ? null : route('comps.printables.chief-timekeeper-pack', $comp),
                    'packedUrl' => $noHeats ? null : route('comps.printables.chief-timekeeper-pack', [$comp, 'layout' => 'packed']),
                    'unavailable' => $noHeats,
                ],
                'speedMarshalling' => [
                    'url' => $noHeats ? null : route('comps.printables.marshalling', $comp),
                    'unavailable' => $noHeats,
                ],
                'sercMarking' => [
                    'url' => $noDraw ? null : route('comps.printables.serc-marking-pack', $comp),
                    'unavailable' => $noDraw,
                ],
                'sercMarshalling' => [
                    'url' => $noDraw ? null : route('comps.printables.marshalling', [$comp, 'type' => 'serc']),
                    'unavailable' => $noDraw,
                ],
            ],
            // Not a Pack: a SERC's own sheets for its judges
            'roughMarkSheets' => [
                'sercs' => $sercs->map(fn (SERC $serc) => [
                    'id' => $serc->id,
                    'name' => $serc->getName(),
                    'url' => route('comps.printables.serc-sheets', [$comp, $serc]),
                ])->all(),
                'unavailable' => $noSercs,
            ],
        ]);
    }

    /**
     * Why a Pack has nothing to print yet, linking to the page that fixes it when the user can open it
     */
    private function unavailable(Competition $comp, string $reason, string $route, string $access): array
    {
        return [
            'reason' => $reason,
            'url' => Gate::allows('access', [$comp, $access]) ? route($route, $comp) : null,
        ];
    }

    public function printCTP(Competition $comp, Request $request)
    {
        $pdfCreator = new CompetitionPdfCreator($comp);

        return $pdfCreator->chiefTimekeeper($request->input('layout') !== 'packed');
    }

    public function printSMS(Competition $comp)
    {
        $pdfCreator = new CompetitionPdfCreator($comp);

        return $pdfCreator->sercMarking();
    }

    public function sercSheets(Competition $comp, SERC $serc)
    {
        $pdfCreator = new CompetitionPdfCreator($comp);

        return $pdfCreator->roughMarkSheets($serc);
    }

    public function printMarshalling(Competition $comp, Request $request)
    {
        $pdfCreator = new CompetitionPdfCreator($comp);

        return $pdfCreator->marshalling($request->input('type', 'speed'));
    }
}
