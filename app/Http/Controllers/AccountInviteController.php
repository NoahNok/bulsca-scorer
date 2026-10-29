<?php

namespace App\Http\Controllers;

use App\Helpers\RouteHelpers;
use App\Http\Requests\AccountInviteNewAccountRequest;
use App\Mail\AccountInviteMail;
use App\Models\AccountInvite;
use App\Models\Organisation\Organisation;
use App\Models\Interfaces\IInvitable;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AccountInviteController extends Controller
{


    public function show(AccountInvite $invite, string $email)
    {

        if ($invite->email != $email) {
            return redirect('/')->with('alert-error', 'Invalid invite');
        }

        $user = $invite->getUser();

        if ($user) {

            if (!Auth::check()) {
                return redirect()->guest(route('login'));
            }

            if (Auth::user()->id != $user->id) {
                session()->flash('alert-error', 'You cannot use that invite!');
                return redirect('/');
            }
        } elseif (Auth::check()) {
            session()->flash('alert-error', 'You cannot use that invite!');
            return redirect('/');
        }

        return Inertia::render($user ? 'Manage/Auth/InviteExistingAccount' : 'Manage/Auth/InviteNewAccount', [
            'invite' => [
                'id' => $invite->id,
                'email' => $invite->email,
                'name' => $invite->getName(),
                'to' => $invite->to instanceof Organisation ? 'organisation' : 'competition',
            ],
        ]);
    }

    public function accept(AccountInvite $invite, string $email)
    {
        if ($redirect = $this->rejectForExistingAccount($invite, $email)) {
            return $redirect;
        }

        if (!$invite->to instanceof IInvitable) {
            $type = get_class($invite->to);
            throw new Exception("Model {$type} not instance of IInvitable");
        }
        $redirect = $invite->to->applyInvite($invite);

        $invite->delete();

        return $redirect;
    }

    public function decline(AccountInvite $invite, string $email)
    {
        if ($redirect = $this->rejectForExistingAccount($invite, $email)) {
            return $redirect;
        }

        $invite->delete();

        return redirect()->route('manage.home')->with('success', 'Invite declined');
    }

    public function acceptNewAccount(AccountInviteNewAccountRequest $request, AccountInvite $invite, string $email)
    {
        if ($redirect = $this->rejectForNewAccount($invite, $email)) {
            return $redirect;
        }

        $validated = $request->validated();

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $invite->email;
        $user->password = Hash::make($validated['password']);
        $user->save();

        if (!$invite->to instanceof IInvitable) {
            $type = get_class($invite->to);
            throw new Exception("Model {$type} not instance of IInvitable");
        }
        $redirect = $invite->to->applyInvite($invite);

        $invite->delete();

        Auth::login($user, true);

        // a full visit, so the page loads fresh for the account they're now signed in to
        return Inertia::location($redirect->getTargetUrl());
    }

    public function declineNewAccount(AccountInvite $invite, string $email)
    {
        if ($redirect = $this->rejectForNewAccount($invite, $email)) {
            return $redirect;
        }

        $invite->delete();

        // they have no account, so back to the public site: / on app. would ask them to sign in
        return Inertia::location(RouteHelpers::mainRoute('home'));
    }

    /**
     * Only the invite's own account, signed in, can accept or decline it.
     */
    private function rejectForExistingAccount(AccountInvite $invite, string $email)
    {
        if ($invite->email != $email) {
            return redirect('/')->with('alert-error', 'Invalid invite');
        }

        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        if (Auth::user()->id != $invite->getUser()?->id) {
            session()->flash('alert-error', 'You cannot use that invite!');
            return redirect('/');
        }

        return null;
    }

    /**
     * Only a guest holding the invite's own link can accept or decline it with a new account.
     */
    private function rejectForNewAccount(AccountInvite $invite, string $email)
    {
        if ($invite->email != $email) {
            return redirect('/')->with('alert-error', 'Invalid invite');
        }

        if (Auth::check()) {
            session()->flash('alert-error', 'You cannot use that invite!');
            return redirect('/');
        }

        return null;
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

        Mail::to($email)->send(new AccountInviteMail($to, route('invite.show', [$ac->id, $email])));
    }
}
