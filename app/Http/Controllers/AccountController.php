<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function search(string $email)
    {



        if (strlen($email) < 3) {
            return response()->json([]);
        }

        $users = User::where('email', 'like', "{$email}%")
            ->limit(3)
            ->get(['email', 'name']);

        return response()->json($users);
    }
}
