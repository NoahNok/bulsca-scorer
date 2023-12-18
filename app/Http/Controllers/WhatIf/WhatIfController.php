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


        return response()->json([
            'success' => true,
            'result' => $value
        ]);
    }

    public function getSpeedResults(int $speed)
    {
        $cse = CompetitionSpeedEvent::find($speed);

        $results = $cse->getResults();

        return response()->json($results);
    }

    public function getSercResults(int $serc)
    {
        $serc = SERC::find($serc);

        $results = $serc->getResults();

        return response()->json($results);
    }
}
