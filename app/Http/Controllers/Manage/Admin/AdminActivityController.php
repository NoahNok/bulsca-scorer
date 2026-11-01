<?php

namespace App\Http\Controllers\Manage\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityFeed;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminActivityController extends Controller
{
    /**
     * The Activity Monitor: every competition's Activity, on the same feed as a competition's Activity page.
     */
    public function show(Request $request)
    {
        $feed = new ActivityFeed($request->query());

        return Inertia::render('Admin/Activity', [
            'url' => route('manage.admin.activity'),
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
