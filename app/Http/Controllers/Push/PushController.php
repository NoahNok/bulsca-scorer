<?php

namespace App\Http\Controllers\Push;

use App\Http\Controllers\Controller;
use App\Notifications\GenericPush;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class PushController extends Controller
{
    public function store(Request $request)
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
        Notification::send(Auth::user(), new GenericPush("Hello Title", "This is cool"));
    }
}
