<?php

namespace App\Http\Controllers\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\LoginRequest;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\DigitalJudge\BetterJudgeLog;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\DigitalJudge\JudgeLog;
use App\Models\SERC;
use App\Models\SERCJudge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DigitalJudgeController extends Controller
{
    function index()
    {
        if (DigitalJudge::canClientJudge()) return redirect()->route('dj.home');
        return view('digitaljudge.index');
    }

    function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $pin = $validated['pin'];
        $clientName = strip_tags($validated['judgeName']);

        $comp = Competition::where('digitalJudgePin', $pin)->where('digitalJudgeEnabled', true)->where(function($query) {
            $query->where('anytimepin', true)->orWhere('when', DB::raw('CURDATE()'));
        })->first();
   

        if ($comp) $this->startJudging($comp, false, $clientName);

        $headComp = Competition::where('digitalJudgeHeadPin', $pin)->where('digitalJudgeEnabled', true)->where(function($query) {
            $query->where('anytimepin', true)->orWhere('when', DB::raw('CURDATE()'));
        })->first();

        if ($headComp) $this->startJudging($headComp, true, $clientName);

        Session::put('_old_input.pin', $pin);
        return redirect()->route('dj.index')->withErrors(['pin' => 'Invalid pin']);
    }

    function logout()
    {
        DigitalJudge::stopClientFromJudging();
        return redirect()->route('dj.index');
    }

    private function startJudging(Competition $comp, bool $isHead, $clientName)
    {
        DigitalJudge::allowClientToJudge($comp);
        DigitalJudge::setClientHeadJudge($isHead);
        DigitalJudge::setClientName($clientName);

        return redirect()->route('dj.home');
    }

    function home()
    {
        return view('digitaljudge.home', ['comp' => DigitalJudge::getClientCompetition(), 'head' => DigitalJudge::isClientHeadJudge()]);
    }


    function toggle(Competition $comp)
    {

        $wasState = $comp->digitalJudgeEnabled;
        $comp->digitalJudgeEnabled = !$wasState;
        $comp->save();
        if ($wasState) return redirect()->back(); // Stop here as we have turned DJ off

        $comp->digitalJudgePin = $comp->digitalJudgePin ?: mt_rand(111111, 999999);
        $comp->digitalJudgeHeadPin = $comp->digitalJudgeHeadPin ?: mt_rand(111111, 999999);

        $comp->save();

        if ($wasState == false) {
            return redirect()->route('dj.settings', $comp)->with('success', 'Digital Judge Enabled');
        }

        return redirect()->back();
    }

    function sercToggle(Competition $comp, SERC $serc)
    {
        $serc->digitalJudgeEnabled = !$serc->digitalJudgeEnabled;
        $serc->save();
        return redirect()->back();
    }


    function speedToggle(Competition $comp, CompetitionSpeedEvent $event)
    {

        $event->digitalJudgeEnabled = !$event->digitalJudgeEnabled;
        $event->save();
        return redirect()->back();
    }

    function confirmResults(SERC $serc)
    {
        if ($serc->digitalJudgeConfirmed) return redirect()->route('dj.home')->with('alert-error', 'Results already confirmed!');;
        return view('digitaljudge.confirm-results', ['serc' => $serc]);
    }

    function confirmResultsPost(SERC $serc)
    {

        if ($serc->digitalJudgeConfirmed) return redirect()->route('dj.home')->with('success', 'Results Confirmed');

        $serc->digitalJudgeConfirmed = true;
        $serc->save();
        return redirect()->route('dj.home')->with('success', 'Results Confirmed');
    }

    function confirmSpeedResults(CompetitionSpeedEvent $speed)
    {
        if ($speed->digitalJudgeConfirmed) return redirect()->route('dj.home')->with('alert-error', 'Results already confirmed!');;
        return view('digitaljudge.speeds.confirm-speeds', ['speed' => $speed, 'comp' => DigitalJudge::getClientCompetition()]);
    }

    function confirmSpeedResultsPost(CompetitionSpeedEvent $speed)
    {
        if ($speed->digitalJudgeConfirmed) return redirect()->route('dj.home')->with('success', 'Results Confirmed');

        $speed->digitalJudgeConfirmed = true;
        $speed->save();
        return redirect()->route('dj.home')->with('success', 'Results Confirmed');
    }

    function judgeLog(Request $request, Competition $comp)
    {





        $log = JudgeLog::WHERE('competition', $comp->id);

        if ($request->filled('filterEvent')) {
            if (str_starts_with($request->input('filterEvent'), 'se')) {
                $log = $log->where('judge', substr($request->input('filterEvent'), 2));
            } else {
                $log = $log->where('speed_event', substr($request->input('filterEvent'), 2));
            }
        }

        if ($request->filled('filterJudge')) {
            $log = $log->where('judgeName', $request->input('filterJudge'));
        }

        if ($request->filled('filterTeam')) {
            $log = $log->where('team', $request->input('filterTeam'));
        }



        $log = $log->orderBy('created_at', 'DESC')->paginate(15);

        if ($request->filled('filterEvent')) {
            $log->appends(['filterEvent' => $request->input('filterEvent')]);
        }
        if ($request->filled('filterJudge')) {
            $log->appends(['filterJudge' => $request->input('filterJudge')]);
        }
        if ($request->filled('filterTeam')) {
            $log->appends(['filterTeam' => $request->input('filterTeam')]);
        }



        return view('digitaljudge.judge-log', ['comp' => $comp, 'log' => $log]);
    }

    function betterJudgeLog(Request $request, Competition $comp)
    {


        $log = BetterJudgeLog::WHERE('competition', $comp->id);

        if ($request->filled('filterType')) {
            if (str_starts_with($request->input('filterType'), 'se')) {
                $log = $log->whereHasMorph('associated_with', SERCJudge::class, function ($query) use ($request) {
                    $query->where('id', substr($request->input('filterType'), 2));
                });
            } else if (str_starts_with($request->input('filterType'), 'sp')) {
                $log = $log->whereHasMorph('associated_with', CompetitionSpeedEvent::class, function ($query) use ($request) {
                    $query->where('id', substr($request->input('filterType'), 2));
                });
            } else {

                $rawType = substr($request->input('filterType'), 2);

                if ($rawType == 'pending') {
                    $log = $log->where('loggable_type', JudgeDQSubmission::class)->whereNot('loggable_data', 'LIKE', '%resolved%');
                } else {
                    $type = $rawType == 'accepted' ? true : false;

                    $log = $log->where('loggable_type', JudgeDQSubmission::class)->whereJsonContains('loggable_data->resolved', $type);
                }
            }
        }

        if ($request->filled('filterJudge')) {
            $log = $log->where('judge_name', $request->input('filterJudge'));
        }

        if ($request->filled('filterTeam')) {
            $log = $log->where('team', $request->input('filterTeam'));
        }

        $log = $log->orderBy('created_at', 'DESC')->paginate(15);

        if ($request->filled('filterJudge')) {
            $log->appends(['filterJudge' => $request->input('filterJudge')]);
        }
        if ($request->filled('filterType')) {
            $log->appends(['filterType' => $request->input('filterType')]);
        }
        if ($request->filled('filterTeam')) {
            $log->appends(['filterTeam' => $request->input('filterTeam')]);
        }

        return view('digitaljudge.better-judge-log', ['comp' => $comp, 'log' => $log]);
    }

    public function help()
    {
        return view('digitaljudge.help', ['comp' => DigitalJudge::getClientCompetition()]);
    }

    public function settings(Competition $comp)
    {
        return view('digitaljudge.settings', ['comp' => $comp]);
    }

    public function settingsPost(Competition $comp, Request $request)
    {
        foreach ($comp->getSERCs as $serc) {
            $serc->digitalJudgeEnabled = $request->input('se:' . $serc->id) == 'on';
            $serc->save();
        }

        foreach ($comp->getSpeedEvents as $speed) {
            $speed->digitalJudgeEnabled = $request->input('sp:' . $speed->id) == 'on';
            $speed->save();
        }

        $comp->max_lanes = $request->input('lanes', $comp->max_lanes);
        $comp->serc_start_time = $request->input('serc_start_time', $comp->serc_start_time);
        $comp->can_be_live = $request->has('can_be_live');

        $comp->save();

        return redirect()->back()->with('success', 'Settings saved');
    }
}
