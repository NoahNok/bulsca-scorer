<?php

use App\DigitalJudge\DigitalJudge;
use Illuminate\Support\Facades\Broadcast;

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
