<?php

use App\DigitalJudge\DigitalJudge;
use App\Helpers\RouteHelpers;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('judge.competition.{id}', function ($connection, $id) {
    // get digital judge user competition id
    $competition = DigitalJudge::getClientCompetition();



    if (!$competition || $competition->id != $id) {
        return false;
    }

    return [
        'id' => DigitalJudge::getClientId(),
        'name' => DigitalJudge::getClientName(),
        'role' => DigitalJudge::isClientHeadJudge() ? 'headJudge' : 'judge',

    ];
}, ['guards' => ['canJudge']]);

// Officials online in the Svelte judge app. Members are keyed by user id, so an
// official signed in on two devices shows once.
Broadcast::channel('judge.officials.{competition}', function (User $user, Competition $competition) {
    // The Management app's Officials page joins from app. to see who's online. A presence channel
    // can't be watched without joining, so Organisers join marked as watching, and the judge app
    // leaves them out of its Online list
    if (request()->getHost() === RouteHelpers::domainRemap('app.')) {
        if (!Gate::forUser($user)->allows('access', [$competition, 'admin'])) {
            return false;
        }

        return ['id' => $user->id, 'name' => $user->name, 'watching' => true];
    }

    if (!$competition->isOfficiatedBy($user)) {
        return false;
    }

    $role = $competition->officials()->whereKey($user->id)->first()?->pivot->role;

    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => Competition::officialRoleName($role),
        'photo' => $user->profilePhotoUrls(),
    ];
});

// New and resubmitted DQs/penalties for the competition's referees to review
Broadcast::channel('judge.referees.{competition}', function (User $user, Competition $competition) {
    return $competition->officialHasRole($user, 'referee');
});
