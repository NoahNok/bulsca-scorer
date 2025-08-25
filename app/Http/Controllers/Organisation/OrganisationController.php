<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\AccountInviteController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\UpdateScoringSettings;
use App\Http\Requests\Organisation\CreateOrganisationRequest;
use App\Http\Requests\Organisation\EditAccoutOrganisationAccess;
use App\Http\Requests\Organisation\EditOrganisationRequest;
use App\Http\Requests\Organisation\InviteAccountToOrganisationRequest;
use App\Http\Requests\Organisation\NameSubdomainTakenRequest;
use App\Http\Requests\Organisation\RemoveOrganisationAccountRequest;
use App\Models\Event\ScoringSchema;
use App\Models\Organisation\Organisation;
use App\Models\Organisation\OrganisationUserAccess;
use App\Models\User;
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
    public function show(Organisation $organisation)
    {
        $this->authorize('access', [$organisation, '*']);

        return view('organisation.show', ['org' => $organisation]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation)
    {
        $this->authorize('access', [$organisation, 'admin']);

        return view('organisation.edit', ['org' => $organisation]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditOrganisationRequest $request, Organisation $organisation)
    {
        $this->authorize('access', [$organisation, 'admin']);

        $validated = $request->validated();

        $organisation->name = $validated['name'];

        if ($request->hasFile('logo')) {

            unlink(public_path() . '/storage/' . $organisation->logo);

            $organisation->logo = $request->file('logo')->store('orgs', 'public');
        }

        $organisation->save();

        session()->flash('success', 'Changes saved.');

        return response()->json([
            'url' => route('orgs.show', $organisation->name)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation)
    {
        $this->authorize('access', [$organisation, 'admin']);
        //
    }

    public function accounts(Organisation $organisation)
    {
        $this->authorize('access', [$organisation, 'admin']);

        return view('organisation.accounts', ['org' => $organisation]);
    }

    public function accountsPost(InviteAccountToOrganisationRequest $request, Organisation $organisation)
    {


        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        // if (!$user) {
        //     return response()->json([
        //         'error' => 'No account found with that email.'
        //     ]);
        // }

        if ($user && $organisation->userBelongsToOrganisation($user)) {
            return response()->json([
                'error' => 'Account already part of this organisation.'
            ]);
        }

        if ($organisation->getInvites()->where('email', $validated['email'])->exists()) {
            return response()->json([
                'error' => 'Email has already been invited'
            ]);
        }

        AccountInviteController::invite($validated['email'], $organisation, ['access' => $validated['access']]);
        // $organisation->addAccount($user, $request->input('access'));

        session()->flash('success', "Account invited.");

        return response()->json([]);
    }

    public function account(Organisation $organisation, User $account)
    {
        $this->authorize('access', [$organisation, 'admin']);

        $access = OrganisationUserAccess::where('organisation', $organisation->id)
            ->where('user', $account->id)
            ->get();

        if ($access->isEmpty()) {
            return response()->json(['error' => 'No access found for this user in this competition']);
        }

        return response()->json([
            'id' => $account->id,
            'name' => $account->name,
            'email' => $account->email,
            'access' => $access->pluck('access_to')->toArray(),
        ]);
    }

    public function accountEditPost(Organisation $organisation, User $account, EditAccoutOrganisationAccess $request)
    {


        $request->validated();

        $response = $organisation->editAccount($account, $request->input('access'));

        if (is_string($response)) {
            return response()->json(['error' => $response]);
        }

        session()->flash('success', "Account updated.");

        return response()->json([]);
    }

    public function accountRemove(Organisation $organisation, RemoveOrganisationAccountRequest $request)
    {


        $validated = $request->validated();

        $user = User::findOrFail($validated['id']);

        $organisation->removeAccount($user);

        return redirect()->back()->with('success', 'Account removed.');
    }

    public function cancelInvite(Organisation $organisation, string $inviteId)
    {

        $this->authorize('access', [$organisation, 'admin']);

        $invite = $organisation->getInvites()->where('id', $inviteId)->first();

        if (!$invite) return;

        $invite->delete();

        return redirect()->back()->with('success', 'Invite removed');
    }

    public function scoringSettings(Organisation $organisation)
    {


        return view('organisation.scoring', ['org' => $organisation]);
    }

    public function createScoringSchema(Organisation $organisation)
    {
        return view('organisation.scoring.create', ['org' => $organisation]);
    }

    public function createScoringSchemaPost(Organisation $organisation, UpdateScoringSettings $request)
    {
        $schema = new ScoringSchema();

        $schema->editFromRequest($request);

        $schema->organisation = $organisation->id;
        $schema->save();

        return response()->json(['url' => route('orgs.scoring.edit', ['organisation' => $organisation->name, 'schema' => $schema->id])]);
    }

    public function editScoringSchema(Organisation $organisation, ScoringSchema $schema)
    {


        if ($schema->organisation != $organisation->id) {
            abort(404);
        }



        return view('organisation.scoring.edit', ['org' => $organisation, 'schema' => $schema]);
    }

    public function editScoringSchemaPost(Organisation $organisation, ScoringSchema $schema, UpdateScoringSettings $request)
    {
        if ($schema->organisation != $organisation->id) {
            abort(404);
        }

        $schema->editFromRequest($request);

        $schema->save();

        return response()->json([]);
    }
}
