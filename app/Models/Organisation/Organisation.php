<?php

namespace App\Models\Organisation;

use App\Models\AccountInvite;
use App\Models\Competition;
use App\Models\Interfaces\IInvitable;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Organisation extends Model implements IInvitable
{
    use HasFactory;

    public static $accessTypes = [
        'Organisation' => [
            'admin' => 'Admin'
        ],
        'Competition' => [

            'view' => 'Overview',
            'teams' => 'Teams/Competitors',
            'heats_and_draws' => 'Heats and Draws',
            'printables' => 'Printables',
            'serc' => 'SERCs',
            'speed' => 'Speeds',
            'results' => 'Results',
            'serc_writer' => 'SERC Writer',
        ]
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where(function ($query) use ($value) {

            $query->where('id', $value)
                ->orWhere('name', $value);
        })->first();
    }


    public function addAccount(User $account, string|array $access_to = 'view')
    {

        if (is_string($access_to)) {
            $access_to = [$access_to];
        }

        if (in_array('admin', $access_to)) {
            # If given admin it will auto apply all others
            $access_to = ['admin'];
        }

        // Remove any existing access for this user in this competition
        OrganisationUserAccess::where('user', $account->id)
            ->where('organisation', $this->id)
            ->delete();

        // Create an access record for each access type
        foreach ($access_to as $accessType) {
            $access = new OrganisationUserAccess();
            $access->user = $account->id;
            $access->organisation = $this->id;
            $access->access_to = $accessType;
            $access->save();
        }
    }

    public function getLogo(): string
    {
        if (!$this->logo) {
            return "";
        }
        return asset('storage/' . $this->logo);
    }

    public function getCompetitions()
    {
        return $this->hasMany(Competition::class, 'organisation')->orderBy('when', 'desc');
    }

    public function canUser(User $user, array|string $access_to): bool
    {
        // If the user is an admin, they have access
        if ($user->isAdmin()) {
            return true;
        }

        if (is_string($access_to)) {
            $access_to = [$access_to];
        }

        // If access_to is an array, check if the user has any of the specified access types
        $access = OrganisationUserAccess::where('user', $user->id)
            ->where('organisation', $this->id)
            ->get();


        // If 0 the user has no access via the org
        if (count($access) == 0) {
            return false;
        }

        // Check if requested access_to was *, which means any permission is valid
        if (in_array('*', $access_to)) {
            return true;
        }

        // Check if user has admin access
        if ($access->contains('access_to', 'admin') || $access->contains('access_to', 'owner')) {

            return true;
        }

        // Check if user has any of the specified access types
        foreach ($access as $a) {
            if (in_array($a->access_to, $access_to)) {
                return true;
            }
        }


        // If no access found, return false

        return false;
    }

    public function userBelongsToOrganisation(User $user): bool
    {
        $access = OrganisationUserAccess::where('user', $user->id)
            ->where('organisation', $this->id)
            ->first();

        return $access !== null;
    }

    public function getAccounts()
    {

        // Get all users that have access to this competition via access table

        $accounts = [];

        foreach (
            OrganisationUserAccess::where('organisation', $this->id)
                ->get()->groupBy('user') as $user_id => $access
        ) {

            $user = User::find($user_id);

            $mapped_titles = collect(self::$accessTypes)->flatMap(fn($group) => collect($group))->all();
            $mapped_titles['owner'] = 'Owner';

            $accounts[] = [
                'id' => $user->id,
                'name' => $user->name . (Auth::user() == $user ? ' (You)' : ''),
                'email' => $user->email,
                'access' => $access->pluck('access_to')->map(function ($item) use ($mapped_titles) {
                    return $mapped_titles[$item] ?? $item;
                })->toArray(),
            ];
        }

        return $accounts;
    }

    public function editAccount(User $account, array $access_to)
    {
        if (!$this->userBelongsToOrganisation($account)) {
            return "Account does not belong to this organisation.";
        }

        if (in_array('admin', $access_to)) {
            # If given admin it will auto apply all others
            $access_to = ['admin'];
        }

        // Remove all existing access for this user in this competition
        OrganisationUserAccess::where('user', $account->id)
            ->where('organisation', $this->id)
            ->delete();

        // Add new access
        foreach ($access_to as $accessType) {
            $access = new OrganisationUserAccess();
            $access->user = $account->id;
            $access->organisation = $this->id;
            $access->access_to = $accessType;
            $access->save();
        }
    }

    public function getInvites()
    {
        return $this->morphMany(AccountInvite::class, 'to');
    }

    public function applyInvite(AccountInvite $invite)
    {

        $user = $invite->getUser();

        if (!$user) {
            throw new Exception("Expected loggedin user during applyInvite");
        }

        $details = $invite->details;

        if (!array_key_exists('access', $details)) {
            throw new Exception("Organisation invite missing 'access' details");
        }

        $this->addAccount($user, $details['access']);

        return redirect()->route('orgs.show', $this->name);
    }

    public function removeAccount(User $account)
    {
        OrganisationUserAccess::where('user', $account->id)
            ->where('organisation', $this->id)
            ->delete();
    }

    public function getOngoingCompetition(): ?Competition
    {
        return $this->getCompetitions()->whereDate('when', Carbon::today())->first();
    }
}
