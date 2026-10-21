<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use App\Models\UserCompetitionAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * New competition (Management): a Personal Competition, or one under an organisation you're a full
 * Organiser of.
 */
class CreateCompetitionTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();
    }

    private function makeOrg(string $name, string|array|null $access): Organisation
    {
        $org = new Organisation();
        $org->name = $name;
        $org->save();

        if ($access) {
            $org->addAccount($this->organiser, $access);
        }

        return $org;
    }

    private function create(array $fields)
    {
        return $this->actingAs($this->organiser)->post($this->appUrl('/create'), [
            'name' => 'Cardiff Classic',
            'when' => '2026-11-14',
            'where' => 'Cardiff Pool',
            'lanes' => 8,
            ...$fields,
        ]);
    }

    public function test_the_page_lists_only_organisations_you_are_a_full_organiser_of(): void
    {
        $full = $this->makeOrg('Full Org', 'admin');
        $this->makeOrg('Limited Org', 'results');
        $this->makeOrg('Not Ours', null);

        $this->actingAs($this->organiser)->get($this->appUrl('/create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competitions/Create')
                ->has('organisations', 1)
                ->where('organisations.0', ['id' => $full->id, 'name' => 'Full Org']));
    }

    public function test_an_organisation_can_be_preselected_only_if_you_can_add_to_it(): void
    {
        $full = $this->makeOrg('Full Org', 'admin');
        $limited = $this->makeOrg('Limited Org', 'results');

        $this->actingAs($this->organiser)->get($this->appUrl('/create?org=' . $full->id))
            ->assertInertia(fn (Assert $page) => $page->where('selected', $full->id));

        $this->actingAs($this->organiser)->get($this->appUrl('/create?org=' . $limited->id))
            ->assertInertia(fn (Assert $page) => $page->where('selected', null));
    }

    public function test_a_personal_competition_makes_you_its_owner_and_opens_it(): void
    {
        $response = $this->create([]);

        $comp = Competition::sole();
        $response->assertRedirect(route('comps.view', $comp));
        $this->assertSame(['Cardiff Classic', '2026-11-14', 'Cardiff Pool', 8, null], [
            $comp->name, $comp->when->toDateString(), $comp->where, $comp->max_lanes, $comp->organisation,
        ]);
        // CompetitionObserver adds the scoring settings, once
        $this->assertSame(1, $comp->getScoringSettings()->count());
        $this->assertTrue($this->organiser->can('access', [$comp, 'owner']));
    }

    public function test_an_inertia_visit_is_sent_on_to_the_competition_page(): void
    {
        $this->actingAs($this->organiser)
            ->post($this->appUrl('/create'), ['name' => 'Cardiff Classic', 'when' => '2026-11-14', 'where' => 'Cardiff Pool', 'lanes' => 8], ['X-Inertia' => 'true'])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', route('comps.view', Competition::sole()));
    }

    public function test_a_competition_can_go_under_an_organisation_you_are_a_full_organiser_of(): void
    {
        $org = $this->makeOrg('Full Org', 'admin');

        $this->create(['organisation' => $org->id])->assertRedirect();

        $comp = Competition::sole();
        $this->assertSame($org->id, $comp->organisation);
        // Its Organisers manage it through the organisation, not a direct grant
        $this->assertSame(0, UserCompetitionAccess::where('competition', $comp->id)->count());
    }

    public function test_a_limited_organiser_cannot_add_a_competition_to_their_organisation(): void
    {
        $org = $this->makeOrg('Limited Org', 'results');

        $this->create(['organisation' => $org->id])->assertSessionHasErrors('organisation');

        $this->assertSame(0, Competition::count());
    }

    public function test_each_field_says_what_is_wrong_with_it(): void
    {
        $this->create(['name' => 'ab', 'when' => '14/11/2026', 'where' => '', 'lanes' => 11])
            ->assertSessionHasErrors(['name', 'when', 'where', 'lanes']);

        $this->assertSame(0, Competition::count());
    }

    public function test_a_competition_can_be_in_the_past(): void
    {
        $this->create(['when' => '2020-03-01'])->assertSessionHasNoErrors();

        $this->assertSame('2020-03-01', Competition::sole()->when->toDateString());
    }
}
