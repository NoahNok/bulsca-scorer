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

        $comp->save();


        $compUserEmail = Str::replace(" ", "-", Str::lower($comp->name)) . "@bulsca.co.uk";
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

        $comp->save();

        return back();
    }

    public function viewComp(Competition $comp)
    {
        return view('admin.competition-view', ['comp' => $comp]);
    }

    public function updateCompUserPassword(Competition $comp)
    {

        $compUserEmail = Str::replace(" ", "-", Str::lower($comp->name)) . "@bulsca.co.uk";
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
}
