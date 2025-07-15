<?php

namespace App\Models\Organisation;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use HasFactory;


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

        // Send email - update to new org invite
        // $accessDisplay = collect($access_to)->map(function ($type) {
        //     return self::$accessTypes[$type] ?? $type;
        // })->toArray();

        // if (in_array('owner', $access_to)) {
        //     return;
        // }

        // uPDATE TO NEW ORG INVITE
        //Mail::to($account)->send(new CompetitionAccountInvite($this, $accessDisplay));
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
        return $this->hasMany(Competition::class, 'organisation');
    }
}
