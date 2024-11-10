<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\Controller;
use App\Jobs\WebPush;
use App\Models\Competition;
use App\Notifications\BrandBasePushNotification;
use App\Notifications\GenericPush;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class PushController extends Controller
{
    public function store(Competition $comp, Request $request)
    {
        $this->validate($request, [
            'endpoint'    => 'required',
            'keys.auth'   => 'required',
            'keys.p256dh' => 'required'
        ]);
        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        $user = Auth::user();
        $user->updatePushSubscription($endpoint, $key, $token);

        Notification::send(Auth::user(), new GenericPush("Notifications On", "You have enabled notifications. You can turn them off at any time!"));

        return response()->json(['success' => true], 200);
    }

    public function push()
    {


        $n = new BrandBasePushNotification(Competition::find(53), "BrandBase", "hello world");

        WebPush::dispatch($n, ['admin'], true);
    }

    public function userSettingsPage(Competition $comp)
    {
        return view('competition.user-notifications', compact('comp'));
    }
}
