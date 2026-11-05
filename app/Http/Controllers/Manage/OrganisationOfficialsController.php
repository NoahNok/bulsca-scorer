<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\OrganisationHeader;
use App\Http\Controllers\Controller;
use App\Mail\CompetitionOfficialAddedMail;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * An organisation's Officials (Svelte, its Organisers only): everyone who has officiated at one of its
 * competitions. The page searches and filters them itself; the CSV export takes the same filters.
 */
class OrganisationOfficialsController extends Controller
{
    public function index(Organisation $organisation)
    {
        return Inertia::render('Manage/Organisation/Officials', [
            'organisation' => OrganisationHeader::for($organisation),
            'officials' => $organisation->officials()->map(fn (array $official) => [
                ...$official,
                'last' => [
                    'name' => $official['last']['name'],
                    'url' => route('manage.competitions.officials', $official['last']['id']),
                    'when' => $official['last']['when']?->toDateString(),
                ],
            ]),
            'roles' => Competition::$officialRoles,
            'upcomingCompetitions' => $this->upcomingCompetitions($organisation)->map(fn (Competition $comp) => [
                'id' => $comp->id,
                'name' => $comp->name,
                'when' => $comp->when->toDateString(),
            ]),
        ]);
    }

    /**
     * The officials as a CSV, with the page's search (q) and role filter.
     */
    public function export(Request $request, Organisation $organisation)
    {
        $q = trim((string) $request->query('q'));
        $role = (string) $request->query('role');
        $role = array_key_exists($role, Competition::$officialRoles) ? $role : '';

        $officials = $organisation->officials()
            ->filter(fn ($official) => $q === ''
                || Str::contains($official['name'], $q, true)
                || Str::contains($official['email'], $q, true))
            ->filter(fn ($official) => $role === '' || $official['roles']->contains($role))
            ->values();

        return response()->streamDownload(function () use ($officials) {
            $out = fopen('php://output', 'w');
            // the BOM tells Excel it's UTF-8, so accented names come out right
            fwrite($out, "\u{FEFF}");
            // escape is passed explicitly: leaving it out is deprecated on PHP 8.4+, and the notice ends up in the file
            fputcsv($out, ['Name', 'Email', 'Roles', 'Competitions', 'Last competition', 'Last competition date'], escape: '');

            foreach ($officials as $official) {
                fputcsv($out, [
                    $official['name'],
                    $official['email'],
                    $official['roles']->map(fn ($role) => Competition::officialRoleName($role))->implode(', '),
                    $official['count'],
                    $official['last']['name'],
                    $official['last']['when']?->format('Y-m-d'),
                ], escape: '');
            }

            fclose($out);
        }, Str::slug($organisation->name) . '-officials.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Add one of the organisation's past officials to one of its upcoming competitions.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'competition_id' => ['required', 'integer'],
            'role' => ['required', Rule::in(array_keys(Competition::$officialRoles))],
        ]);

        $official = $organisation->officials()->contains('id', (int) $validated['user_id'])
            ? User::find($validated['user_id'])
            : null;
        if (!$official) {
            throw ValidationException::withMessages(['user_id' => "Pick someone who has officiated for {$organisation->name}."]);
        }

        $comp = $this->upcomingCompetitions($organisation)->firstWhere('id', (int) $validated['competition_id']);
        if (!$comp) {
            throw ValidationException::withMessages(['competition_id' => 'Pick one of your upcoming competitions.']);
        }

        if (!$comp->addOfficial($official, $validated['role'])) {
            throw ValidationException::withMessages(['competition_id' => "{$official->name} is already an official for {$comp->name}."]);
        }

        // admin-made accounts stay unverified until the official first signs in with a PIN
        Mail::to($official->email)->send(new CompetitionOfficialAddedMail(
            $comp,
            $validated['role'],
            $official->isJudgeOnly() && !$official->hasVerifiedEmail(),
        ));

        return back()->with('success', "{$official->name} added to {$comp->name} as " . ($validated['role'] === 'referee' ? 'a referee' : 'an official'));
    }

    /**
     * The organisation's competitions from today on that the current user can admin, soonest first.
     */
    private function upcomingCompetitions(Organisation $organisation): Collection
    {
        return Competition::where('organisation', $organisation->id)
            ->where('when', '>=', today())
            ->orderBy('when')
            ->get()
            ->filter(fn (Competition $comp) => Gate::allows('access', [$comp, 'admin']))
            ->values();
    }
}
