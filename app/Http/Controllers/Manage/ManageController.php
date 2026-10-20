<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ManageController extends Controller
{
    /**
     * The old Blade dashboard on app.: the competitions and organisations you can manage.
     */
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        $comps = [];
        $comps['owner'] = $user->getCompetitionsByAccess('owner');
        $comps['invited'] = $user->getCompetitionsWithAccess;

        return view('dashboard', ['comps' => $comps, 'orgs' => $user->getOrganisations]);
    }

    /**
     * The Management app's home (Svelte), at / on app.
     */
    public function home()
    {
        return Inertia::render('Manage/Home');
    }
}
