<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\Competitor;
use App\Models\League;
use App\Models\Orders\EntityEventSeed;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A competition's Entries & Leagues page (Svelte, in CompetitionLayout), where each club, team,
 * competitor and league is added, changed or removed on its own.
 */
class CompetitionEntriesTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    private CompetitionSpeedEvent $event;

    private League $league;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();
        $this->comp = $this->competition('Entries Champs');
        $this->comp->addAccount($this->organiser, ['owner']);

        $base = new SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();

        $this->event = new CompetitionSpeedEvent();
        $this->event->event = $base->id;
        $this->event->competition = $this->comp->id;
        $this->event->record = 0;
        $this->event->save();

        $this->league = $this->comp->getLeagues()->create(['name' => 'Open']);
    }

    private function competition(string $name): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->addWeek();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->show_competition = true;
        $comp->save();

        return $comp;
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl('/comps/' . $this->comp->id . $path);
    }

    public function test_the_page_lists_entries_as_a_tree_with_leagues_and_seeds(): void
    {
        $club = Club::create(['competition' => $this->comp->id, 'name' => 'Cardiff']);
        $team = CompetitionTeam::create(['competition' => $this->comp->id, 'team' => 'A', 'club' => $club->id]);
        $team->leagues()->sync([$this->league->id]);
        EntityEventSeed::create(['speed_event' => $this->event->id, 'entity_type' => 'team', 'entity_id' => $team->id, 'seed' => 83456]);
        Competitor::create(['competition' => $this->comp->id, 'name' => 'Sam', 'team' => $team->id]);
        CompetitionTeam::create(['competition' => $this->comp->id, 'team' => 'Loose']);

        $this->actingAs($this->organiser)->get($this->url('/entries'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/Entries')
                ->where('competition.tabs.1.label', 'Entries & Leagues')
                ->where('competition.tabs.1.current', true)
                ->where('competition.tabs.1.svelte', true)
                ->where('leagues.0.name', 'Open')
                ->where('leagues.0.teams', 1)
                ->where('clubs.0.name', 'Cardiff')
                ->where('clubs.0.teams.0.name', 'A')
                ->where('clubs.0.teams.0.leagues', [$this->league->id])
                ->where('clubs.0.teams.0.seeds.' . $this->event->id, '01:23.456')
                ->where('clubs.0.teams.0.competitors.0.name', 'Sam')
                ->where('teams.0.name', 'Loose')
                ->has('competitors', 0)
                ->where('seeds.perEvent', false)
                ->where('seeds.events.0.id', $this->event->id)
                ->has('heatsUrl')
            );
    }

    public function test_the_old_entities_url_redirects(): void
    {
        $this->actingAs($this->organiser)->get($this->url('/entities'))
            ->assertStatus(301)
            ->assertRedirect(route('manage.competitions.entries', $this->comp));
    }

    public function test_someone_without_teams_access_cant_open_or_change_entries(): void
    {
        $results = User::factory()->create();
        $this->comp->addAccount($results, ['results']);

        $this->actingAs($results)->get($this->url('/entries'))->assertForbidden();
        $this->actingAs($results)->post($this->url('/entries/clubs'), ['name' => 'Nope'])->assertForbidden();
        $this->assertSame(0, Club::count());
    }

    public function test_adding_a_team_and_competitor_saves_their_club_team_leagues_seeds_and_results(): void
    {
        $this->actingAs($this->organiser)->post($this->url('/entries/clubs'), ['name' => ' Cardiff '])
            ->assertRedirect()->assertSessionHas('success', 'Club added');
        $club = Club::sole();
        $this->assertSame('Cardiff', $club->name);

        $this->actingAs($this->organiser)->post($this->url('/entries/teams'), [
            'name' => 'Cardiff A',
            'club' => $club->id,
            'leagues' => [$this->league->id],
            'seeds' => [$this->event->id => '1:23.45'],
        ])->assertSessionHasNoErrors();

        $team = CompetitionTeam::sole();
        $this->assertSame($club->id, (int) $team->club);
        $this->assertSame([$this->league->id], $team->leagues->pluck('id')->all());
        $this->assertSame(83450, (int) $team->getSeeds()->sole()->seed);
        // Speed events score teams, so the team gets an empty result in each
        $this->assertSame(1, SpeedResult::where('entity_type', 'team')->where('entity_id', $team->id)->count());

        $this->actingAs($this->organiser)->post($this->url('/entries/competitors'), ['name' => 'Sam', 'team' => $team->id])
            ->assertSessionHasNoErrors();
        $competitor = Competitor::sole();
        $this->assertSame($team->id, (int) $competitor->team);
        $this->assertSame(0, SpeedResult::where('entity_type', 'competitor')->count());
    }

    public function test_changing_a_team_syncs_its_leagues_and_clears_a_blank_seed(): void
    {
        $team = CompetitionTeam::create(['competition' => $this->comp->id, 'team' => 'A']);
        $team->leagues()->sync([$this->league->id]);
        EntityEventSeed::create(['speed_event' => $this->event->id, 'entity_type' => 'team', 'entity_id' => $team->id, 'seed' => 83456]);

        $this->actingAs($this->organiser)->post($this->url('/entries/teams/' . $team->id), [
            'name' => 'Renamed',
            'seeds' => [$this->event->id => ''],
        ])->assertSessionHasNoErrors();

        $team->refresh();
        $this->assertSame('Renamed', $team->team);
        $this->assertCount(0, $team->leagues);
        $this->assertSame(0, $team->getSeeds()->count());
    }

    public function test_a_team_can_be_saved_with_no_leagues_even_if_a_blank_one_is_sent(): void
    {
        $team = CompetitionTeam::create(['competition' => $this->comp->id, 'team' => 'A']);
        $team->leagues()->sync([$this->league->id]);

        $this->actingAs($this->organiser)->post($this->url('/entries/teams/' . $team->id), ['name' => 'A', 'leagues' => ['']])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Team saved');

        $this->assertCount(0, $team->fresh()->leagues);
    }

    public function test_a_badly_formatted_seed_is_an_error_on_that_field(): void
    {
        $this->actingAs($this->organiser)->post($this->url('/entries/teams'), [
            'name' => 'A',
            'seeds' => [$this->event->id => '83.4'],
        ])->assertSessionHasErrors('seeds.' . $this->event->id);

        $this->assertSame(0, CompetitionTeam::count());
    }

    public function test_removing_a_club_removes_its_teams_competitors_and_their_results(): void
    {
        $club = Club::create(['competition' => $this->comp->id, 'name' => 'Cardiff']);
        $this->actingAs($this->organiser)->post($this->url('/entries/teams'), ['name' => 'A', 'club' => $club->id]);
        $team = CompetitionTeam::sole();
        Competitor::create(['competition' => $this->comp->id, 'name' => 'Sam', 'team' => $team->id]);

        $this->actingAs($this->organiser)->delete($this->url('/entries/clubs/' . $club->id))
            ->assertRedirect()->assertSessionHas('success', 'Club removed');

        $this->assertSame(0, Club::count());
        $this->assertSame(0, CompetitionTeam::count());
        $this->assertSame(0, Competitor::count());
        $this->assertSame(0, SpeedResult::count());
    }

    public function test_another_competitions_entries_cant_be_changed_or_used(): void
    {
        $theirs = $this->competition('Their Champs');
        $theirClub = Club::create(['competition' => $theirs->id, 'name' => 'Theirs']);
        $theirTeam = CompetitionTeam::create(['competition' => $theirs->id, 'team' => 'Theirs']);
        $theirLeague = $theirs->getLeagues()->create(['name' => 'Theirs']);

        $this->actingAs($this->organiser);
        $this->post($this->url('/entries/clubs/' . $theirClub->id), ['name' => 'Taken'])->assertNotFound();
        $this->delete($this->url('/entries/teams/' . $theirTeam->id))->assertNotFound();

        $this->post($this->url('/entries/teams'), ['name' => 'A', 'club' => $theirClub->id, 'leagues' => [$theirLeague->id]])
            ->assertSessionHasErrors(['club', 'leagues.0']);
        $this->post($this->url('/entries/competitors'), ['name' => 'Sam', 'team' => $theirTeam->id])
            ->assertSessionHasErrors('team');

        $this->assertSame('Theirs', $theirClub->fresh()->name);
        $this->assertNotNull($theirTeam->fresh());
    }

    public function test_a_league_name_cant_be_used_twice_in_a_competition(): void
    {
        $this->actingAs($this->organiser);

        $this->post($this->url('/leagues'), ['name' => ' open '])->assertSessionHasErrors('name');
        $this->assertSame(1, $this->comp->getLeagues()->count());

        $novice = $this->comp->getLeagues()->create(['name' => 'Novice']);
        $this->post($this->url('/leagues/' . $novice->id), ['name' => 'OPEN'])->assertSessionHasErrors('name');
        $this->assertSame('Novice', $novice->fresh()->name);

        // Its own name, in another case, is fine
        $this->post($this->url('/leagues/' . $novice->id), ['name' => 'novice'])->assertSessionHasNoErrors();

        // Another competition can have the same name
        $theirs = $this->competition('Their Champs');
        $theirs->addAccount($this->organiser, ['owner']);
        $this->post($this->appUrl('/comps/' . $theirs->id . '/leagues'), ['name' => 'Open'])->assertSessionHasNoErrors();
    }

    public function test_leagues_are_added_renamed_and_deleted(): void
    {
        $this->actingAs($this->organiser);

        $this->post($this->url('/leagues'), ['name' => 'Novice'])->assertSessionHas('success', 'League added');
        $novice = League::where('name', 'Novice')->sole();
        $this->assertSame($this->comp->id, (int) $novice->competition);

        $this->post($this->url('/leagues/' . $novice->id), ['name' => 'Beginners'])->assertSessionHas('success', 'League saved');
        $this->assertSame('Beginners', $novice->fresh()->name);

        $this->delete($this->url('/leagues/' . $novice->id))->assertSessionHas('success', 'League deleted');
        $this->assertNull($novice->fresh());
    }
}
