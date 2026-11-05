<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\CreateOrganisationRequest;
use App\Models\Organisation\Organisation;
use App\Services\OrganisationLogoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * New organisation (Svelte): a name and an optional logo. You become its first Organiser, with owner access.
 */
class OrganisationCreateController extends Controller
{
    public function create()
    {
        return Inertia::render('Manage/Organisations/Create');
    }

    public function store(CreateOrganisationRequest $request, OrganisationLogoService $logos)
    {
        // All or nothing: a logo that can't be read mustn't leave an organisation behind, holding its name
        $org = DB::transaction(function () use ($request, $logos) {
            $org = new Organisation();
            $org->name = $request->validated('name');
            $org->save();

            if ($request->hasFile('logo')) {
                $logos->store($org, $request->file('logo'));
            }

            $org->addAccount(Auth::user(), ['owner']);

            return $org;
        });

        return redirect()->route('manage.organisations.show', $org);
    }
}
