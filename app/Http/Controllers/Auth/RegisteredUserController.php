<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Open sign-up is off: accounts come from an Invite (AccountInviteController).
 */
class RegisteredUserController extends Controller
{
    /**
     * Explains that accounts come by Invite (Svelte).
     */
    public function create(): Response
    {
        return Inertia::render('Manage/Auth/Register');
    }

    public function store(): never
    {
        abort(403);
    }
}
