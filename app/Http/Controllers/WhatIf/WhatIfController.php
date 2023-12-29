<?php

namespace App\Http\Controllers\WhatIf;

use App\Http\Controllers\Controller;
use App\Http\Requests\WhatIf\WhatIfBeginRequest;
use App\Http\Requests\WhatIf\WhatIfResumeRequest;
use App\Http\Requests\WhatIf\WhatIfUpdateSercResult;
use App\Http\Requests\WhatIf\WhatIfUpdateSpeedResult;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\ResultSchema;
use App\Models\SERC;
use App\Models\SERCResult;
use App\Models\SpeedResult;
use App\Models\User;
use App\WhatIf\CompetitionCloner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class WhatIfController extends Controller
{
    public function index()
    {


        if (auth()->user()) {
            return redirect()->route('whatif.editor');
        }

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

        DB::update('UPDATE competitions SET wi_user=? WHERE id=?', [$user->id, $newCompId]);


        Auth::login($user);


        return redirect()->route('whatif.editor');
    }

    public function resume(WhatIfResumeRequest $request)
    {
        $validated = $request->validated();
        Config::set('database.default', 'whatif');

        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return back()->withFragment('#resume')->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        return redirect()->route('whatif.editor');
    }

    public function editorIndex()
    {


        if (!auth()->user()) {
            return redirect()->route('whatif');
        }

        $comp = auth()->user()->getCompetition;

        if (!$comp) {
            return redirect()->route('whatif.select');
        }

        $comp->updated_at = now();
        $comp->save();
        return view('whatif.editor', [
            'comp' => $comp
        ]);
    }

    public function editorResults(int $schema)
    {

        $rs = ResultSchema::find($schema);


        $results = Cache::rememberForever('result_comp_' . $rs->competition . '_schema_' . $rs->id, function () use ($rs) {
            return $rs->getDetailedPrint();
        });



        if (!auth()->user()) {
            return redirect()->route('whatif');
        }

        return view('whatif.results-viewier', [
            'comp' => auth()->user()->getCompetition,
            'schema' => $rs,
            'results' => $results
        ]);
    }


    public function updateSercResult(WhatIfUpdateSercResult $request)
    {





        $validated = $request->validated();

        $result = $validated['result'];
        $id = $validated['id'];


        $result = DB::update("UPDATE serc_results SET result=? WHERE id=?", [$result, $id]);

        $serc = SERCResult::find($id)->getSERC();
        Cache::forget('serc_results_' . $serc->id);
        $this->clearResultsCache();

        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }

    public function updateSpeedResult(WhatIfUpdateSpeedResult $request)
    {
        $validated = $request->validated();

        $value = $validated['value'];
        $id = $validated['id'];
        $type = $validated['type'];

        $result = null;
        if ($type == 'result') {


            $minSecSplit = explode(":", $value);

            if (SpeedResult::find($id)->getEvent->getName() == "Rope Throw" && count($minSecSplit) == 1) {
                $value = $minSecSplit[0];
            } else {



                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);


                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];


                $value = $totalMillis;
            }


            $result = DB::update("UPDATE speed_results SET result=? WHERE id=?", [$value, $id]);
        } else if ($type == 'dq') {
            $result = DB::update("UPDATE speed_results SET disqualification=? WHERE id=?", [($value !== "" ? $value : null), $id]);
        } else if ($type == 'pen') {

            $penaltiesSplit = explode(",", $value);


            $valid = [];
            foreach ($penaltiesSplit as $penalty) {
                $penalty = trim($penalty);
                if (preg_match("/^P[0-9]{3}$/", $penalty) == 0) {
                    break;
                }
                array_push($valid, $penalty);
            }

            // yeet all the old penalties

            DB::delete("DELETE FROM speed_result_penalties WHERE speed_result=?", [$id]);

            foreach ($valid as $penalty) {
                DB::insert("INSERT INTO speed_result_penalties (speed_result, code) VALUES (?, ?)", [$id, $penalty]);
            }
        } else {
            return response()->json([
                'success' => false,
                'error' => "Unknown update type: '" . $type . "'"
            ]);
        }


        $sr = SpeedResult::find($id);
        Cache::forget('speed_results_' . $sr->getEvent->id);
        $this->clearResultsCache();

        return response()->json([
            'success' => true,
            'result' => $value
        ]);
    }

    public function getSpeedResults(int $speed)
    {
        $cse = CompetitionSpeedEvent::find($speed);


        $results = Cache::rememberForever('speed_results_' . $cse->id, function () use ($cse) {
            return $cse->getResults();
        });





        return response()->json($results);
    }

    public function getSercResults(int $serc)
    {
        $serc = SERC::find($serc);

        $results = Cache::rememberForever('serc_results_' . $serc->id, function () use ($serc) {
            return $serc->getResults();
        });





        return response()->json($results);
    }

    public function switchOpenEditor(int $comp)
    {



        $comp = Competition::find($comp);

        if ($comp->wi_user != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'error' => "You do not have access to this editor!"
            ]);
        }

        $currentComp = auth()->user()->getCompetition;
        if ($currentComp) {
            $currentComp->updated_at = now();
            $currentComp->save();
        }


        $user = auth()->user();
        $user->competition = $comp->id;
        $user->save();

        return redirect()->route('whatif.editor');
    }

    public function loggedInCloneAndSwitch(Request $request)
    {


        $compId = $request->input('competition', '');

        if ($compId == '') {
            return response()->json([
                'success' => false,
                'error' => "No competition specified!"
            ]);
        }


        Config::set('database.default', 'mysql');

        $comp = Competition::find($compId);

        if (!$comp) {
            return response()->json([
                'success' => false,
                'error' => "Invalid competition!"
            ]);
        }

        $cloner = new CompetitionCloner();
        $newCompId = $cloner->clone($comp);

        Config::set('database.default', 'whatif');

        $c = Competition::find($newCompId);
        $c->wi_user = auth()->user()->id;
        $c->save();

        $user = auth()->user();
        $user->competition = $newCompId;
        $user->save();

        return redirect()->route('whatif.editor');
    }

    public function deleteEditor(Request $request)
    {


        if (!auth()->user()->getCompetition) {
            return redirect()->route('whatif.select');
        }

        $compId = auth()->user()->getCompetition->id;

        if ($compId == '') {
            return response()->json([
                'success' => false,
                'error' => "No competition specified!"
            ]);
        }

        $comp = Competition::find($compId);

        if (!$comp) {
            return response()->json([
                'success' => false,
                'error' => "Invalid competition!"
            ]);
        }

        if ($comp->wi_user != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'error' => "You do not have access to this editor!"
            ]);
        }

        $comp->delete();

        $user = auth()->user();
        $user->competition = null;
        $user->save();

        return redirect()->route('whatif.select');
    }

    public function resetCurrentCompetition()
    {

        $comp = auth()->user()->getCompetition;

        Config::set('database.default', 'mysql');

        $targetComp = Competition::where('name', $comp->name)->first();

        $cloner = new CompetitionCloner();
        $newCompId = $cloner->clone($targetComp);
        Config::set('database.default', 'whatif');

        $comp->delete();

        DB::update('UPDATE competitions SET wi_user=? WHERE id=?', [auth()->user()->id, $newCompId]);

        $user = auth()->user();
        $user->competition = $newCompId;
        $user->save();

        return response()->json([
            'success' => true,
            'result' => 'Reset competition'
        ]);
    }

    public function select()
    {
        return view('whatif.select-competition');
    }

    public function logout()
    {


        if (!auth()->user()) {
            return redirect()->route('whatif');
        }

        $comp = auth()->user()->getCompetition;
        if ($comp) {
            $comp->updated_at = now();
            $comp->save();
        }

        auth()->logout();

        return redirect()->route('whatif');
    }

    private function clearResultsCache()
    {
        $comp = auth()->user()->getCompetition;

        if (!$comp) return;

        foreach ($comp->getResultSchemas as $rs) {
            Cache::forget('result_comp_' . $rs->competition . '_schema_' . $rs->id);
        }
    }

    public function adminIndex()
    {
        if (!auth()->user()) {
            return redirect()->route('whatif');
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('whatif.editor');
        }

        return view('whatif.admin', [
            'comps' => Competition::orderBy('created_at', 'DESC')->paginate(12)
        ]);
    }
}
