<?php

namespace App\Http\Controllers\Brands;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandCreateCompetitionRequest;
use App\Http\Requests\Brand\BrandDeleteCompetitionRequest;
use App\Http\Requests\Brand\BrandEditCompetitionRequest;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BrandHomeController extends Controller
{
    public function index()
    {
        return view('brand.index');
    }

    public function createCompetition()
    {
        return view('brand.create-competition');
    }

    public function storeCompetition(BrandCreateCompetitionRequest $request)
    {
        $validated = $request->validated();

        $targetBrand = Auth::user()->getBrands->first();

        $comp = new Competition();
        $comp->name = $validated['name'];
        $comp->when = $validated['when'];
        $comp->where = $validated['where'];
        $comp->isLeague = $validated['isLeague'];
        $comp->max_lanes = $validated['lanes'];
        $comp->anytimepin = $validated['anytimepin'];
        $comp->scoring_Version = "1.1.0"; // Must forcibly set the updated version 1.1.0 programatically - UPDATE THIS WITH EACH NEW SCORING UPDATE
        $comp->brand = $targetBrand->id; // Use brand of the admin user
        $comp->scoring_type = $validated['scoring_type'];
        $comp->save();

        $compUserEmail = Str::replace([" ", "@", "_"], "-", Str::lower($comp->name)) . "." . $comp->id . "@" . $targetBrand->website;
        $compUserPasswordRaw =  Str::random(16);
        $compUserPassword = Hash::make($compUserPasswordRaw);

        $compUser = new User();
        $compUser->name = $comp->name;
        $compUser->email = $compUserEmail;
        $compUser->password = $compUserPassword;
        $compUser->competition = $comp->id;
        $compUser->save();

        $compUser->getBrands()->attach($targetBrand);


        return view('brand.competition-created', ['email' => $compUserEmail, 'password' => $compUserPasswordRaw, "comp" => $comp]);
    }

    public function editCompetition(Competition $comp)
    {
        return view('brand.competition-edit', compact('comp'));
    }

    public function updateCompetition(Competition $comp, BrandEditCompetitionRequest $request)
    {
        $validated = $request->validated();

        $comp->name = $validated['name'];
        $comp->when = $validated['when'];
        $comp->where = $validated['where'];
        $comp->isLeague = $validated['isLeague'];
        $comp->max_lanes = $validated['lanes'];
        $comp->anytimepin = $validated['anytimepin'];
        $comp->scoring_type = $validated['scoring_type'];
        $comp->save();

        return back()->with('success', "Competition updated!");
    }

    public function deleteCompetition(Competition $comp, BrandDeleteCompetitionRequest $request)
    {

        $data = $request->validated();

        $compId = $data['compId'];
        $compName = $data['compName'];

        $c = Competition::find($compId);

        if ($c != $comp || $c->name != $compName) return redirect()->back()->with('alert-error', "Competition name doesn't match!");

        $c->delete();

        return redirect()->route('brand.index')->with('success', "Competition deleted!");
    }
}
