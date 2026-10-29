<?php

namespace Tests\Feature;

use App\Models\Championship;
use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * An organisation's Championships (Svelte): the list, making one, and adding and removing its competitions.
 */
class OrganisationChampionshipsTest extends TestCase
{
    use RefreshDatabase;

    private Organisation $org;

    private User $organiser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = $this->makeOrg('Cardiff ULSC');

        $this->organiser = User::factory()->create();
        $this->org->addAccount($this->organiser, 'admin');
    }

    private function makeOrg(string $name): Organisation
    {
        $org = new Organisation();
        $org->name = $name;
        $org->save();

        return $org;
    }

    private function makeChampionship(string $name, ?Organisation $org = null): Championship
    {
        return ($org ?? $this->org)->championships()->create(['name' => $name, 'start_date' => '2026-10-01', 'end_date' => '2027-03-31']);
    }

    private function makeComp(string $name, ?Organisation $org = null): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addMonth();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->organisation = ($org ?? $this->org)->id;
        $comp->save();

        return $comp;
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl("/organisation/{$this->org->id}/championships{$path}");
    }

    public function test_the_list_has_its_championships_with_their_dates_and_competitions(): void
    {
        $league = $this->makeChampionship('Student League');
        $this->makeComp('Round 1')->championship()->associate($league)->save();
        $this->makeChampionship('Other League', $this->makeOrg('Swansea'));

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Championships')
                ->where('organisation.tabs.1.current', true)
                ->where('championships', [[
                    'id' => $league->id,
                    'name' => 'Student League',
                    'start' => '2026-10-01',
                    'end' => '2027-03-31',
                    'competitions' => 1,
                    'url' => route('manage.organisations.championships.show', [$this->org, $league]),
                ]]));
    }

    public function test_making_one_opens_it(): void
    {
        $this->actingAs($this->organiser)
            ->post($this->url(), ['name' => 'Student League', 'start_date' => '2026-10-01', 'end_date' => '2027-03-31'])
            ->assertRedirect(route('manage.organisations.championships.show', [$this->org, Championship::sole()]));

        $this->assertSame($this->org->id, Championship::sole()->organisation_id);
    }

    public function test_it_must_end_after_it_starts(): void
    {
        $this->actingAs($this->organiser)
            ->post($this->url(), ['name' => 'Student League', 'start_date' => '2026-10-01', 'end_date' => '2026-09-01'])
            ->assertSessionHasErrors('end_date');

        $this->assertSame(0, Championship::count());
    }

    public function test_its_page_has_its_competitions_and_the_ones_that_could_be_added(): void
    {
        $league = $this->makeChampionship('Student League');
        $round = $this->makeComp('Round 1');
        $round->championship()->associate($league);
        $round->type = 'QUALIFIER';
        $round->save();
        $free = $this->makeComp('Freshers');
        $elsewhere = $this->makeComp('In Another');
        $elsewhere->championship()->associate($this->makeChampionship('Other'))->save();
        $this->makeComp('Not Ours', $this->makeOrg('Swansea'));

        $this->actingAs($this->organiser)->get($this->url("/{$league->id}"))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Championship')
                ->where('organisation.tabs.1.current', true)
                ->where('championship.name', 'Student League')
                ->where('competitions.0.name', 'Round 1')
                ->where('competitions.0.type', 'QUALIFIER')
                ->where('available', [['id' => $free->id, 'name' => 'Freshers', 'when' => $free->when->toDateString()]])
                ->where('types', ['REGIONAL' => 'Regional', 'QUALIFIER' => 'Qualifier', 'FINAL' => 'Final']));
    }

    public function test_a_competition_can_be_added_with_its_type_and_removed(): void
    {
        $league = $this->makeChampionship('Student League');
        $comp = $this->makeComp('Round 1');

        $this->actingAs($this->organiser)
            ->post($this->url("/{$league->id}/competitions"), ['competition_id' => $comp->id, 'type' => 'FINAL'])
            ->assertSessionHasNoErrors();
        $this->assertSame([$league->id, 'FINAL'], [$comp->fresh()->championship_id, $comp->fresh()->type]);

        $this->actingAs($this->organiser)
            ->delete($this->url("/{$league->id}/competitions/{$comp->id}"))
            ->assertSessionHasNoErrors();
        $this->assertSame([null, 'STANDALONE'], [$comp->fresh()->championship_id, $comp->fresh()->type]);
    }

    public function test_only_its_organisations_free_competitions_can_be_added(): void
    {
        $league = $this->makeChampionship('Student League');
        $theirs = $this->makeComp('Not Ours', $this->makeOrg('Swansea'));
        $taken = $this->makeComp('Taken');
        $taken->championship()->associate($this->makeChampionship('Other'))->save();
        $comp = $this->makeComp('Round 1');

        $add = fn (array $data) => $this->actingAs($this->organiser)->post($this->url("/{$league->id}/competitions"), $data);

        $add(['competition_id' => $theirs->id, 'type' => 'FINAL'])->assertSessionHasErrors('competition_id');
        $add(['competition_id' => $taken->id, 'type' => 'FINAL'])->assertSessionHasErrors('competition_id');
        $add(['competition_id' => $comp->id, 'type' => 'STANDALONE'])->assertSessionHasErrors('type');

        $this->assertSame(0, $league->competitions()->count());
    }

    public function test_deleting_one_keeps_its_competitions_as_standalone(): void
    {
        $league = $this->makeChampionship('Student League');
        $comp = $this->makeComp('Round 1');
        $comp->championship()->associate($league);
        $comp->type = 'FINAL';
        $comp->save();

        $this->actingAs($this->organiser)->delete($this->url("/{$league->id}"))
            ->assertRedirect(route('manage.organisations.championships', $this->org))
            ->assertSessionHas('success', 'Student League deleted');

        $this->assertSame(0, Championship::count());
        $this->assertSame([null, 'STANDALONE'], [$comp->fresh()->championship_id, $comp->fresh()->type]);
    }

    public function test_only_its_organisers_can_delete_one_of_its_championships(): void
    {
        $theirs = $this->makeChampionship('Their League', $this->makeOrg('Swansea'));
        $this->actingAs($this->organiser)->delete($this->url("/{$theirs->id}"))->assertNotFound();

        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');
        $ours = $this->makeChampionship('Student League');
        $this->actingAs($member)->delete($this->url("/{$ours->id}"))->assertForbidden();

        $this->assertSame(2, Championship::count());
    }

    public function test_an_old_link_by_its_hyphenated_name_still_opens_it(): void
    {
        $this->makeChampionship('Student League');

        $this->actingAs($this->organiser)->get($this->url('/Student-League'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('championship.name', 'Student League'));
    }

    public function test_another_organisations_championship_is_not_found_here(): void
    {
        $theirs = $this->makeChampionship('Their League', $this->makeOrg('Swansea'));

        $this->actingAs($this->organiser)->get($this->url("/{$theirs->id}"))->assertNotFound();
    }

    public function test_members_without_admin_access_cannot_make_one(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($member)
            ->post($this->url(), ['name' => 'Student League', 'start_date' => '2026-10-01', 'end_date' => '2027-03-31'])
            ->assertForbidden();
        $this->assertSame(0, Championship::count());
    }
}
