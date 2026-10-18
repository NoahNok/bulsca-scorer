<?php

namespace App\Http\Controllers;

use App\Http\Requests\Competition\AddCompetitionOfficial;
use App\Mail\CompetitionOfficialAddedMail;
use App\Models\Competition;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompetitionOfficialsController extends Controller
{
    public function index(Competition $comp)
    {
        $officials = $comp->officials()->orderBy('name')->get();
        $refereeCount = $officials->filter(fn (User $official) => $official->pivot->isOfficialRole('referee'))->count();

        // what stays behind if they're removed, for the confirm message
        $submitted = ViolationSubmission::where('competition_id', $comp->id)
            ->whereIn('submitter_id', $officials->modelKeys())
            ->get(['submitter_id', 'status'])
            ->groupBy('submitter_id');
        $seconded = ViolationSubmission::where('competition_id', $comp->id)
            ->whereIn('seconder_id', $officials->modelKeys())
            ->get(['seconder_id'])
            ->countBy('seconder_id');

        $officials = $officials->map(function (User $official) use ($refereeCount, $submitted, $seconded) {
            $submissions = $submitted->get($official->id, collect());

            return [
                'id' => $official->id,
                'name' => $official->name,
                'email' => $official->email,
                'role' => $official->pivot->role,
                // admin-made accounts stay unverified until the official first signs in with a PIN
                'neverSignedIn' => $official->isJudgeOnly() && !$official->hasVerifiedEmail(),
                'joined' => $official->pivot->created_at,
                'online' => $official->pivot->isOnline(),
                'lastSeen' => $official->pivot->last_seen_at,
                'onlyReferee' => $refereeCount === 1 && $official->pivot->isOfficialRole('referee'),
                'removeMessage' => $this->removeMessage(
                    $official,
                    $refereeCount === 1 && $official->pivot->isOfficialRole('referee'),
                    $submissions->count(),
                    $submissions->where('status', 'SUBMITTED')->count(),
                    $seconded->get($official->id, 0),
                ),
            ];
        });

        return view('competition.officials', [
            'comp' => $comp,
            'officials' => $officials,
            'hasReferee' => $refereeCount > 0,
            'suggestions' => $this->suggestions($comp),
            'previousCompetitions' => $this->previousCompetitions($comp),
        ]);
    }

    /**
     * The confirm message for removing an official, saying what they leave behind.
     */
    private function removeMessage(User $official, bool $onlyReferee, int $submissions, int $pending, int $seconded): string
    {
        $left = collect([
            $submissions ? "{$submissions} DQ/penalty " . Str::plural('submission', $submissions) . ($pending ? " ({$pending} waiting for review)" : '') : null,
            $seconded ? "{$seconded} they seconded" : null,
        ])->filter();

        return collect([
            "Remove {$official->name}? They will lose access to this competition on Digital Judge, and can only rejoin with the PIN.",
            $official->pivot->isOnline() ? "They're using Digital Judge right now." : null,
            $onlyReferee ? "They're the only referee, so no one will be able to review DQs and penalties or confirm results." : null,
            $left->isNotEmpty() ? 'Their ' . $left->implode(' and ') . ' will stay.' : null,
        ])->filter()->implode(' ');
    }

    /**
     * People who've officiated the organisation's other competitions, most recent first, for the
     * add modal to offer before anything's typed. Emails are masked like the search's.
     */
    private function suggestions(Competition $comp): Collection
    {
        $org = $comp->getOrganisation;
        if (!$org) {
            return collect();
        }

        $officialIds = $comp->officials()->pluck('users.id');

        return $org->officials()
            ->reject(fn ($official) => $officialIds->contains($official['id']))
            ->sortByDesc(fn ($official) => $official['last']['when']?->timestamp ?? 0)
            ->take(8)
            ->map(fn ($official) => [
                'id' => $official['id'],
                'name' => $official['name'],
                'email' => $this->maskEmail($official['email']),
                'last' => $official['last']['name'],
            ])
            ->values();
    }

    /**
     * The organisation's other competitions with officials, most recent first, to copy
     * officials from.
     */
    private function previousCompetitions(Competition $comp): Collection
    {
        if (!$comp->organisation) {
            return collect();
        }

        return Competition::where('organisation', $comp->organisation)
            ->whereKeyNot($comp->id)
            ->whereHas('officials')
            ->withCount('officials')
            ->orderByDesc('when')
            ->limit(10)
            ->get(['id', 'name', 'when']);
    }

    /**
     * Find accounts by name or email for the add official modal. Emails are masked unless the
     * search is the full address, so a name search can't be used to look up someone's email.
     */
    public function search(Request $request, Competition $comp)
    {
        $q = trim((string) $request->query('q'));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'exactEmail' => false]);
        }

        $like = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
        $officialIds = $comp->officials()->pluck('users.id');

        $results = User::where(fn ($query) => $query
            ->where('name', 'like', "%{$like}%")
            ->orWhere('email', 'like', "{$like}%"))
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'email'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => strcasecmp($user->email, $q) === 0 ? $user->email : $this->maskEmail($user->email),
                'official' => $officialIds->contains($user->id),
            ]);

        return response()->json([
            'results' => $results,
            'exactEmail' => User::where('email', $q)->exists(),
        ]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($local, 0, 1) . '***@' . $domain;
    }

    public function store(AddCompetitionOfficial $request, Competition $comp)
    {
        $validated = $request->validated();

        $official = isset($validated['user_id'])
            ? User::find($validated['user_id'])
            : User::where('email', $validated['email'])->first();
        $newAccount = false;

        if (!$official) {
            // they sign in to Digital Judge with an emailed PIN, and can claim the account by registering
            $official = new User();
            $official->name = $validated['name'];
            $official->email = $validated['email'];
            $official->password = Hash::make(Str::random(40));
            $official->judge_only = true;
            $official->save();

            $newAccount = true;
        }

        if (!$comp->addOfficial($official, $validated['role'])) {
            return back()->withInput()->withErrors(['email' => "{$official->name} is already an official for this competition."], 'addOfficial');
        }

        Mail::to($official->email)->send(new CompetitionOfficialAddedMail($comp, $validated['role'], $newAccount));

        return back()->with('success', "{$official->name} added as " . ($validated['role'] === 'referee' ? 'a referee' : 'an official'));
    }

    /**
     * Add everyone who officiated one of the organisation's other competitions, with the role
     * they had there. Anyone already here is left alone.
     */
    public function copy(Request $request, Competition $comp)
    {
        $validated = $request->validateWithBag('copyOfficials', [
            'from' => ['required', 'integer'],
        ]);

        $from = $comp->organisation
            ? Competition::where('organisation', $comp->organisation)->whereKeyNot($comp->id)->find($validated['from'])
            : null;

        if (!$from) {
            return back()->withErrors(['from' => 'Pick one of your organisation\'s other competitions.'], 'copyOfficials');
        }

        $added = 0;
        foreach ($from->officials as $official) {
            if ($comp->addOfficial($official, $official->pivot->role)) {
                Mail::to($official->email)->send(new CompetitionOfficialAddedMail($comp, $official->pivot->role));
                $added++;
            }
        }

        if ($added === 0) {
            return back()->with('success', "Everyone from {$from->name} is already an official here");
        }

        return back()->with('success', "Added {$added} " . Str::plural('official', $added) . " from {$from->name}");
    }

    /**
     * Send the "you've been added" email again, for officials who haven't opened the competition yet.
     */
    public function resend(Competition $comp, User $official)
    {
        $this->ensureOfficiates($comp, $official);

        $pivot = $comp->officials()->whereKey($official->id)->first()->pivot;
        $key = "resend-official:{$comp->id}:{$official->id}";

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $minutes = ceil(RateLimiter::availableIn($key) / 60);

            return back()->with('alert-error', "{$official->name} was emailed just now. You can send it again in {$minutes} " . Str::plural('minute', $minutes) . '.');
        }

        RateLimiter::hit($key, 5 * 60);

        Mail::to($official->email)->send(new CompetitionOfficialAddedMail(
            $comp,
            $pivot->role,
            $official->isJudgeOnly() && !$official->hasVerifiedEmail(),
        ));

        return back()->with('success', "Email sent to {$official->name} again");
    }

    public function update(Request $request, Competition $comp, User $official)
    {
        $this->ensureOfficiates($comp, $official);

        $validated = $request->validate([
            'role' => ['required', Rule::in(array_keys(Competition::$officialRoles))],
        ]);

        $comp->setOfficialRole($official, $validated['role']);

        return back()->with('success', "{$official->name} is now " . ($validated['role'] === 'referee' ? 'a referee' : 'an official'));
    }

    public function destroy(Competition $comp, User $official)
    {
        $this->ensureOfficiates($comp, $official);

        $comp->removeOfficial($official);

        return back()->with('success', "{$official->name} removed");
    }

    private function ensureOfficiates(Competition $comp, User $official): void
    {
        if (!$comp->officials()->whereKey($official->id)->exists()) {
            abort(404);
        }
    }
}
