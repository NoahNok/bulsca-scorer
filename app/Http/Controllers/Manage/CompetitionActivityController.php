<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\CompetitionHeader;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Services\ActivityFeed;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * A competition's Activity (Organisers only), filtered by the same `related`, `by` and `activity` query
 * parameters as the Admin Activity Monitor. "Load older" asks for the next page, which is appended.
 */
class CompetitionActivityController extends Controller
{
    public function show(Competition $comp, Request $request)
    {
        $feed = new ActivityFeed($request->query(), $comp);

        return Inertia::render('Manage/Competition/Activity', [
            'competition' => CompetitionHeader::for($comp),
            'overviewUrl' => route('comps.view', $comp),
            'url' => route('manage.competitions.activity', $comp),
            // Matched on id, so Activity recorded since the first page doesn't show twice
            'entries' => Inertia::merge($feed->entries())->matchOn('id'),
            'olderPage' => $feed->olderPage(),
            'filters' => $feed->filters(),
            'chipGroups' => $feed->chipGroups(),
            'pickers' => $feed->pickers(),
            'history' => $feed->history(),
        ]);
    }
}
