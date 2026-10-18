<?php

namespace App\Http\Controllers\DigitalJudge;

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
     * since the live events only reach officials who are online.
     */
    public function index(Competition $competition)
    {
        return $competition->announcements()
            ->with('sender')
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

    /**
     * Withdrawing deletes it, so it's gone for everyone, referees included.
     */
    public function withdraw(Competition $competition, Announcement $announcement)
    {
        abort_unless($announcement->competition_id === $competition->id, 404);

        $announcement->delete();

        AnnouncementWithdrawn::dispatch($announcement);

        return response()->noContent();
    }
}
