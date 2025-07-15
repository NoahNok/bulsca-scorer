<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\CreateOrganisationRequest;
use App\Http\Requests\Organisation\NameSubdomainTakenRequest;
use App\Models\Organisation\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('organisation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrganisationRequest $request)
    {
        $validated = $request->validated();

        $org = new Organisation();
        $org->name = $validated['name'];

        if ($request->hasFile('logo')) {
            $org->logo = $request->file('logo')->store('orgs', 'public');
        }

        $org->save();

        $org->addAccount(Auth::user(), ['owner']);

        return response()->json([
            'url' => route('orgs.show', $org->name)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $organisation)
    {
        $organisation = Organisation::where('name', $organisation)->firstOrFail();

        return view('organisation.show', ['org' => $organisation]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation)
    {
        //
    }
}
