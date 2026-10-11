<?php

namespace App\Http\Controllers;

use App\Http\Requests\Competition\AddCompetitionOfficial;
use App\Mail\CompetitionOfficialAddedMail;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompetitionOfficialsController extends Controller
{
    public function index(Competition $comp)
    {
        $officials = $comp->officials()->orderBy('name')->get()->map(fn (User $official) => [
            'id' => $official->id,
            'name' => $official->name,
            'email' => $official->email,
            'role' => $official->pivot->role,
            // admin-made accounts stay unverified until the official first signs in with a PIN
            'neverSignedIn' => $official->isJudgeOnly() && !$official->hasVerifiedEmail(),
            'joined' => $official->pivot->created_at,
        ]);

        return view('competition.officials', [
            'comp' => $comp,
            'officials' => $officials,
        ]);
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
