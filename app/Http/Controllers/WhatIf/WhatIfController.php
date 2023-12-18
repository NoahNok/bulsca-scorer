<?php

namespace App\Http\Controllers\WhatIf;

use App\Http\Controllers\Controller;
use App\Http\Requests\WhatIf\WhatIfBeginRequest;
use App\Http\Requests\WhatIf\WhatIfResumeRequest;
use App\Http\Requests\WhatIf\WhatIfUpdateSercResult;
use App\Models\Competition;
use App\Models\ResultSchema;
use App\Models\User;
use App\WhatIf\CompetitionCloner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class WhatIfController extends Controller
{
    public function index()
    {
        return view('whatif.index');
    }

    public function cloneAndStart(WhatIfBeginRequest $request)
    {





        if (Session::has('whatif.competition')) {
            return redirect()->route('whatif.editor');
        }

        $validated = $request->validated();

        $comp = Competition::find($validated['competition']);

        if (!$comp) return "invalid comp";

        $cloner = new CompetitionCloner();
        $newCompId = $cloner->clone($comp);



        Config::set('database.default', 'whatif');



        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $user = new User();
            $user->name = $validated['email'];
            $user->email = $validated['email'];
            $user->password = bcrypt($validated['password']);
            $user->competition = $newCompId;
            $user->save();
        } else {
            $user = auth()->user();
            $user->competition = $newCompId;
            $user->save();
        }


        Auth::login($user);


        return redirect()->route('whatif.editor');
    }

    public function resume(WhatIfResumeRequest $request)
    {
        $validated = $request->validated();


        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return back()->withFragment('#resume')->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        return redirect()->route('whatif.editor');
    }

    public function editorIndex()
    {

        $comp = auth()->user()->getCompetition;


        return view('whatif.editor', [
            'comp' => $comp
        ]);
    }

    public function editorResults(int $schema)
    {

        $rs = ResultSchema::find($schema);
        $results = $rs->getDetailedPrint();
        dump($results);
    }


    public function updateSercResult(WhatIfUpdateSercResult $request)
    {
        $validated = $request->validated();

        $result = $validated['result'];
        $id = $validated['id'];


        $result = DB::update("UPDATE serc_results SET result=? WHERE id=?", [$result, $id]);

        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }
}
