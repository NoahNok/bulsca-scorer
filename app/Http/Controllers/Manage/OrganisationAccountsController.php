<?php

namespace App\Http\Controllers\Manage;

use App\Helpers\OrganisationHeader;
use App\Http\Controllers\AccountInviteController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\EditAccoutOrganisationAccess;
use App\Http\Requests\Organisation\InviteAccountToOrganisationRequest;
use App\Models\Organisation\Organisation;
use App\Models\Organisation\OrganisationUserAccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

/**
 * An organisation's Accounts (Svelte, its Organisers only): who can manage it and what they can open,
 * and the invites they haven't accepted yet.
 */
class OrganisationAccountsController extends Controller
{
    public function index(Organisation $organisation)
    {
        $you = Auth::user();
        // Each account's access in the order the page lists it
        $order = array_flip(collect(Organisation::$accessTypes)->flatMap(fn ($group) => array_keys($group))->all());

        $accounts = OrganisationUserAccess::where('organisation', $organisation->id)->get()->groupBy('user')
            ->map(function ($access, $userId) use ($you, $order) {
                $user = User::find($userId);
                $keys = $access->pluck('access_to');

                return $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'access' => $keys->reject(fn ($key) => $key === 'owner')->sortBy(fn ($key) => $order[$key] ?? PHP_INT_MAX)->values(),
                    'owner' => $keys->contains('owner'),
                    'you' => $user->is($you),
                ] : null;
            })
            ->filter()->sortBy('name')->values();

        return Inertia::render('Manage/Organisation/Accounts', [
            'organisation' => OrganisationHeader::for($organisation),
            'accounts' => $accounts,
            'invites' => $organisation->getInvites()->orderBy('email')->get(['id', 'email']),
            // In groups: the organisation's own Admin, then what they can open in its competitions
            'accessTypes' => Organisation::$accessTypes,
        ]);
    }

    public function invite(InviteAccountToOrganisationRequest $request, Organisation $organisation)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        if ($user && $organisation->userBelongsToOrganisation($user)) {
            throw ValidationException::withMessages(['email' => 'They can already manage this organisation.']);
        }

        if ($organisation->getInvites()->where('email', $validated['email'])->exists()) {
            throw ValidationException::withMessages(['email' => "They've already been invited."]);
        }

        AccountInviteController::invite($validated['email'], $organisation, ['access' => $validated['access']]);

        return back()->with('success', 'Invite sent');
    }

    public function update(EditAccoutOrganisationAccess $request, Organisation $organisation, User $account)
    {
        $response = $organisation->editAccount($account, $request->validated('access'));

        if (is_string($response)) {
            throw ValidationException::withMessages(['access' => $response]);
        }

        return back()->with('success', 'Access saved');
    }

    public function destroy(Organisation $organisation, User $account)
    {
        // The page hides Remove for these
        if ($organisation->isOwner($account) || $account->is(Auth::user())) {
            throw ValidationException::withMessages(['account' => "The owner and you can't be removed."]);
        }

        $organisation->removeAccount($account);

        return back()->with('success', 'Account removed');
    }

    public function cancelInvite(Organisation $organisation, string $invite)
    {
        $organisation->getInvites()->whereKey($invite)->firstOrFail()->delete();

        return back()->with('success', 'Invite cancelled');
    }
}
