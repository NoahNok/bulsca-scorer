<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Competition\CreateCompetitionRequest;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\Organisation\OrganisationUserAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * New competition (Svelte): a Personal Competition, or one under an organisation you're a full Organiser of.
 */
class CompetitionCreateController extends Controller
{
    public function create(Request $request)
    {
        $organisations = $this->organisations(Auth::user());

        return Inertia::render('Manage/Competitions/Create', [
            'organisations' => $organisations->map(fn (Organisation $org) => [
                'id' => $org->id,
                'name' => $org->name,
            ]),
            // ?org= from an organisation's page, if you can add to it
            'selected' => $organisations->firstWhere('id', $request->integer('org'))?->id,
        ]);
    }

    public function store(CreateCompetitionRequest $request)
    {
        $validated = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        $organisation = null;
        if ($validated['organisation'] ?? null) {
            $organisation = $this->organisations($user)->firstWhere('id', $validated['organisation']);

            if (!$organisation) {
                return back()->withErrors(['organisation' => 'Only Organisers with full access can add competitions to that organisation. Choose another, or make it Personal.']);
            }
        }

        $comp = new Competition();
        $comp->name = $validated['name'];
        $comp->when = $validated['when'];
        $comp->where = $validated['where'];
        $comp->max_lanes = $validated['lanes'];
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->organisation = $organisation?->id;
        $comp->save();

        // An organisation's Organisers manage its competitions, so only a Personal Competition needs an owner
        if (!$organisation) {
            $comp->addAccount($user, ['owner']);
        }

        session()->flash('success', 'Competition created!');

        // The competition's page is still Blade
        return Inertia::location(route('comps.view', $comp));
    }

    /**
     * Organisations you can add a competition to: those you're an Organiser of with full access
     */
    private function organisations(User $user)
    {
        return Organisation::whereIn('id', OrganisationUserAccess::where('user', $user->id)
            ->whereIn('access_to', ['admin', 'owner'])->select('organisation'))
            ->orderBy('name')->get();
    }
}
