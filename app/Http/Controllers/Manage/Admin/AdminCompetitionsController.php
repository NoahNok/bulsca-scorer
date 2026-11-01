<?php

namespace App\Http\Controllers\Manage\Admin;

use App\Helpers\ManagedCompetitions;
use App\Helpers\Pagination;
use App\Http\Controllers\Controller;
use App\Models\Competition;
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
}
