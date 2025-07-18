<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountInviteNewAccountRequest;
use App\Mail\AccountInviteMail;
use App\Models\AccountInvite;
use App\Models\Interfaces\IInvitable;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccountInviteController extends Controller
{


    public function show(AccountInvite $invite)
    {

        $user = $invite->getUser();

        if ($user) {

            if (!Auth::check()) {
                return redirect()->guest(route('login'));
            }

            if (Auth::user()->id != $user->id) {
                session()->flash('alert-error', 'You cannot use that invite!');
                return redirect('/');
            }

            return view('account.invite', compact(['invite', 'user']));
        }

        if (Auth::check()) {
            session()->flash('alert-error', 'You cannot use that invite!');
            return redirect('/');
        }

        return view('account.invite-new-account', compact('invite'));
    }

    public function resolve(AccountInvite $invite, string $resolution)
    {
        if (!in_array($resolution, ['accept', 'decline'])) {
            return redirect('/');
        }

        if ($resolution == 'decline') {
            $invite->delete();
            return redirect('/');
        }

        // Accepted
        if (!$invite->to instanceof IInvitable) {
            $type = get_class($invite->to);
            throw new Exception("Model {$type} not instance of IInvitable");
        }
        $redirect = $invite->to->applyInvite($invite);

        $invite->delete();

        return $redirect;
    }

    public function resolveNewAccount(AccountInviteNewAccountRequest $request, AccountInvite $invite, string $resolution)
    {
        if (!in_array($resolution, ['accept', 'decline'])) {
            return redirect('/');
        }

        if ($resolution == 'decline') {
            $invite->delete();
            return redirect('/');
        }

        $validated = $request->validated();

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $invite->email;
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Accepted
        if (!$invite->to instanceof IInvitable) {
            $type = get_class($invite->to);
            throw new Exception("Model {$type} not instance of IInvitable");
        }
        $redirect = $invite->to->applyInvite($invite);

        $invite->delete();

        Auth::login($user, true);

        return $redirect;
    }

    /**
     * Invites the given email to the $to applying any $details
     * Deviates based on if an account already exists to personalise the sent email
     */
    public static function invite(string $email, Model $to, array $details)
    {

        $user = User::where('email', $email)->first();

        $ac = new AccountInvite();
        $ac->email = $email;
        $ac->to_type = get_class($to);
        $ac->to_id = $to->id;
        $ac->details = $details;
        $ac->save();

        Mail::to($email)->send(new AccountInviteMail($to, route('invite.show', $ac->id)));
    }
}
