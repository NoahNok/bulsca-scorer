<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Events\Announcement\AnnouncementSent;
use App\Events\Announcement\AnnouncementWithdrawn;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\DigitalJudge\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Newest first. The app fetches this on start and whenever it reconnects,
     * since the live events only reach officials who are online. Withdrawn
     * ones are only kept for the referees.
     */
    public function index(Competition $competition)
    {
        return $competition->announcements()
            ->with('sender', 'withdrawnBy')
            ->when(!DigitalJudge::isClientHeadJudge($competition), fn($query) => $query->whereNull('withdrawn_at'))
            ->latest()
            ->latest('id')
            ->get()
            ->map->payload();
    }

    public function store(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:280',
        ]);

        $announcement = $competition->announcements()->create([
            'sender_id' => $request->user()->id,
            'text' => trim($validated['text']),
        ]);

        AnnouncementSent::dispatch($announcement);

        return response()->json($announcement->payload(), 201);
    }

    public function withdraw(Request $request, Competition $competition, Announcement $announcement)
    {
        abort_unless($announcement->competition_id === $competition->id, 404);

        // already withdrawn: keep who did it first
        if (!$announcement->withdrawn_at) {
            $announcement->update([
                'withdrawn_at' => now(),
                'withdrawn_by_id' => $request->user()->id,
            ]);

            AnnouncementWithdrawn::dispatch($announcement);
        }

        return response()->json($announcement->payload());
    }
}
