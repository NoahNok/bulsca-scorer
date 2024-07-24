<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCreateCompRequest;
use App\Http\Requests\AdminDeleteCompetition;
use App\Models\Competition;
use App\Models\SpeedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('admin.index');
    }

    public function createComp()
    {

        return view('admin.competition-create');
    }

    public function createCompPost(AdminCreateCompRequest $request)
    {
        $validated = $request->validated();

        $comp = new Competition();
        $comp->name = $validated['name'];
        $comp->when = $validated['when'];
        $comp->isLeague = $validated['isLeague'];
        $comp->max_lanes = $validated['lanes'];
        $comp->anytimepin = $validated['anytimepin'];
        $comp->scoring_Version = "1.1.0"; // Must forcibly set the updated version 1.1.0 programatically - UPDATE THIS WITH EACH NEW SCORING UPDATE

        if ($validated['brand'] !== 'null') {
            $comp->brand = $validated['brand'] == 'none' ? null : $validated['brand'];
        }

        $comp->save();


        $compUserEmail = Str::replace([" ", "@", "_"], "-", Str::lower($comp->name)) . "." . $comp->id . "@bulsca.co.uk";
        $compUserPasswordRaw =  Str::random(16);
        $compUserPassword = Hash::make($compUserPasswordRaw);

        $compUser = new User();
        $compUser->name = $comp->name;
        $compUser->email = $compUserEmail;
        $compUser->password = $compUserPassword;
        $compUser->competition = $comp->id;
        $compUser->save();

        return view('admin.competiton-created', ['email' => $compUserEmail, 'password' => $compUserPasswordRaw, "comp" => $comp]);
    }

    public function updateCompPost(Competition $comp, AdminCreateCompRequest $request)
    {
        $validated = $request->validated();


        $comp->name = $validated['name'];
        $comp->when = $validated['when'];
        $comp->isLeague = $validated['isLeague'];
        $comp->max_lanes = $validated['lanes'];
        $comp->anytimepin = $validated['anytimepin'];

        if ($validated['season'] !== 'null') {
            $comp->season = $validated['season'];
        }

        if ($validated['brand'] !== 'null') {
            $comp->brand = $validated['brand'] == 'none' ? null : $validated['brand'];
        }

        $comp->save();

        return back()->with('success', "Competition updated!");
    }

    public function viewComp(Competition $comp)
    {
        return view('admin.competition-view', ['comp' => $comp]);
    }

    public function updateCompUserPassword(Competition $comp)
    {

        $compUserEmail = Str::replace(" ", "-", Str::lower($comp->name)) . "." . $comp->id . "@bulsca.co.uk";
        $compUserPasswordRaw =  Str::random(16);
        $compUserPassword = Hash::make($compUserPasswordRaw);

        $compUser = $comp->getUser;

        $compUser->password = $compUserPassword;

        $compUser->save();

        return view('admin.competiton-created', ['email' => $compUserEmail, 'password' => $compUserPasswordRaw, 'comp' => $comp]);
    }

    public function records()
    {
        $se = SpeedEvent::all();
        return view('admin.speed-records', ['events' => $se]);
    }

    public function updateRecords(Request $request)
    {
        $json = json_decode($request->input('data'));
        foreach ($json as $row) {
            $se = SpeedEvent::find($row->id);
            $minSecSplit = explode(":", $row->values->record);



            try {
                $min = $minSecSplit[0];
                $secMillisSplit = explode(".", $minSecSplit[1]);

                $totalMillis = $min * 60000 + $secMillisSplit[0] * 1000 + $secMillisSplit[1];

                $se->record = $totalMillis;
                $se->save();
            } catch (\Throwable $th) {
                continue;
            }
        }
    }


    public function deleteCompPost(AdminDeleteCompetition $request, Competition $comp)
    {
        $data = $request->validated();

        $compId = $data['compId'];
        $compName = $data['compName'];

        $c = Competition::find($compId);

        if ($c != $comp || $c->name != $compName) return redirect()->back()->with('alert-error', "Competition name doesn't match!");

        $c->delete();

        return redirect()->route('admin.index')->with('success', "Competition deleted!");
    }

    public function seasons()
    {
        return view('admin.seasons');
    }

    public function seasonCreate()
    {
        return view('admin.season-create');
    }


    public function seasonCreatePost(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $season = new \App\Models\Season();
        $season->name = $validated['name'];
        $season->save();

        return redirect()->route('admin.seasons')->with('success', "Season created!");
    }

    public function seasonEdit(\App\Models\Season $season)
    {
        return view('admin.season-edit', ['season' => $season]);
    }

    public function seasonEditPost(\App\Models\Season $season, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $season->name = $validated['name'];
        $season->save();

        return redirect()->route('admin.seasons')->with('success', "Season updated!");
    }
}
