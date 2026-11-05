<?php

namespace App\Http\Controllers\Manage\Admin;

use App\Helpers\ManagedCompetitions;
use App\Helpers\Pagination;
use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCompetitionsController extends Controller
{
    /**
     * Every competition on the site, newest first, at /admin.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $comps = Competition::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->with('getOrganisation')
            ->orderByDesc('when')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Competitions', [
            'q' => $q,
            'competitions' => collect($comps->items())->map(fn (Competition $comp) => [
                ...ManagedCompetitions::row($comp),
                'url' => route('manage.admin.competitions.show', $comp),
                'organisationName' => $comp->getOrganisation?->name,
            ]),
            'pagination' => Pagination::props($comps),
            'newUrl' => route('manage.competitions.create'),
        ]);
    }

    /**
     * What only Admins can change on a competition: its organisation and the anytime pin. Everything else is in its Management Settings.
     */
    public function show(Competition $comp)
    {
        return Inertia::render('Admin/Competition', [
            'competition' => [
                'id' => $comp->id,
                'name' => $comp->name,
                'when' => $comp->when->toDateString(),
                'organisation' => $comp->organisation,
                'anytimepin' => (bool) $comp->anytimepin,
            ],
            'organisations' => Organisation::orderBy('name')->get(['id', 'name'])
                ->map(fn (Organisation $org) => ['id' => $org->id, 'name' => $org->name]),
            'manageUrl' => route('comps.view', $comp),
            'publicUrl' => RouteHelpers::mainRoute('landing.competition', $comp->getSlug()),
        ]);
    }

    public function update(Competition $comp, Request $request)
    {
        $validated = $request->validate([
            'organisation' => ['nullable', 'integer', 'exists:organisations,id'],
            'anytimepin' => ['required', 'boolean'],
        ]);

        $comp->organisation = $validated['organisation'] ?? null;
        $comp->anytimepin = $validated['anytimepin'];
        $comp->save();

        return back()->with('success', 'Competition saved');
    }
}
