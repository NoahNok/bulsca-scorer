<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\ManagedCompetitions;
use App\Helpers\OrganisationHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\EditOrganisationRequest;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Services\OrganisationLogoService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

/**
 * An organisation's Overview (anyone in it) and Settings (its Organisers), in OrganisationLayout.
 */
class OrganisationController extends Controller
{
    public function show(Organisation $organisation)
    {
        $this->authorize('access', [$organisation, '*']);

        $admin = Gate::allows('access', [$organisation, 'admin']);
        [$upcoming, $past] = ManagedCompetitions::upcomingThenPast($organisation->getCompetitions()->get());

        return Inertia::render('Manage/Organisation/Overview', [
            'organisation' => OrganisationHeader::for($organisation),
            'competitions' => $upcoming->concat($past)->map(fn (Competition $comp) => ManagedCompetitions::row(
                $comp,
                limited: !$admin && !Gate::allows('access', [$comp, 'admin']),
            )),
            'newCompetitionUrl' => $admin ? route('manage.competitions.create', ['org' => $organisation->id]) : null,
        ]);
    }

    public function settings(Organisation $organisation)
    {
        return Inertia::render('Manage/Organisation/Settings', [
            'organisation' => OrganisationHeader::for($organisation),
            'settings' => [
                'name' => $organisation->name,
                'logo' => $organisation->getLogo() ?: null,
            ],
        ]);
    }

    public function update(EditOrganisationRequest $request, Organisation $organisation, OrganisationLogoService $logos)
    {
        $organisation->name = $request->validated('name');
        $organisation->save();

        if ($request->hasFile('logo')) {
            $logos->store($organisation, $request->file('logo'));
        }

        return redirect()->route('manage.organisations.settings', $organisation)->with('success', 'Settings saved');
    }
}
