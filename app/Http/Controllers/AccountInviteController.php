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

        if (!self::isUsable($invite, $email)) {
            return self::invalidInvite();
        }

        $user = $invite->getSignInUser();

        if ($user) {
            // invites sent before official-only accounts were made full when invited
            $user->makeFullAccount();

            if (!Auth::check()) {
                return redirect()->guest(route('login'));
            }

            if (Auth::user()->id != $user->id) {
                return self::wrongAccount();
            }
        } elseif (Auth::check()) {
            return self::wrongAccount();
        }

        return Inertia::render($user ? 'Manage/Auth/InviteExistingAccount' : 'Manage/Auth/InviteNewAccount', [
            'invite' => [
                'id' => $invite->id,
                'email' => $invite->email,
                'name' => $invite->getName(),
                'to' => $invite->to instanceof Organisation ? 'organisation' : 'competition',
            ],
            // an unclaimed official-only account's name, which the new account form starts with
            'accountName' => $user ? null : $invite->getUser()?->name,
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

        // an unclaimed official-only account becomes theirs: the invite reached their inbox, so they own the email
        $user = $invite->getUser() ?? new User();
        $user->name = $validated['name'];
        $user->email = $invite->email;
        $user->password = Hash::make($validated['password']);
        $user->judge_only = false;
        $user->email_verified_at ??= now();
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
        if (!self::isUsable($invite, $email)) {
            return self::invalidInvite();
        }

        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        if (Auth::user()->id != $invite->getSignInUser()?->id) {
            return self::wrongAccount();
        }

        return null;
    }

    /**
     * Only a guest holding the invite's own link can accept or decline it with a new account.
     */
    private function rejectForNewAccount(AccountInvite $invite, string $email)
    {
        if (!self::isUsable($invite, $email)) {
            return self::invalidInvite();
        }

        // the email has an account they can sign in to, so the invite is accepted from that
        if ($invite->getSignInUser()) {
            return redirect()->route('invite.show', [$invite, $email]);
        }

        if (Auth::check()) {
            return self::wrongAccount();
        }

        return null;
    }

    /**
     * The link's email is the invite's, and what it invites them to still exists
     */
    private static function isUsable(AccountInvite $invite, string $email): bool
    {
        return $invite->email == $email && $invite->to !== null;
    }

    /**
     * A link to an invite that's gone (accepted, declined, cancelled, or its competition or organisation deleted)
     * or never matched: signed in, to their home with a toast; signed out, to the public home, which toasts the
     * notice. The session cookie is per host, so a flash set on app. wouldn't reach the main domain.
     * Also the route group's `missing` handler, for invites that no longer exist.
     */
    public static function invalidInvite()
    {
        if (Auth::check()) {
            return redirect()->route('manage.home')->with('alert-error', 'That invite is no longer valid');
        }

        // Inertia::location, as the accept and decline forms post with Inertia and the home is on another host
        return Inertia::location(RouteHelpers::mainRoute('home', ['notice' => 'invite-invalid']));
    }

    /**
     * Signed in to another account than the invite's (or signed in at all, for an invite to a new account)
     */
    private static function wrongAccount()
    {
        return redirect()->route('manage.home')->with('alert-error', 'That invite is for a different account');
    }

    /**
     * Invites the given email to the $to applying any $details
     * Deviates based on if an account already exists to personalise the sent email
     */
    public static function invite(string $email, Model $to, array $details)
    {

        // an official-only account can't sign in to the Management app to accept, so it's made a full one.
        // An unclaimed one is left: it has no password to sign in with, so it's claimed from the invite page.
        $user = User::where('email', $email)->first();
        if ($user && !$user->isUnclaimed()) {
            $user->makeFullAccount();
        }

        $ac = new AccountInvite();
        $ac->email = $email;
        $ac->to_type = get_class($to);
        $ac->to_id = $to->id;
        $ac->details = $details;
        $ac->save();

        Mail::to($email)->send(new AccountInviteMail($to, route('invite.show', [$ac->id, $email])));
    }
}
