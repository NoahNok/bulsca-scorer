<?php

namespace Tests\Feature;

use App\Http\Controllers\AccountInviteController;
use App\Models\AccountInvite;
use App\Models\Competition;
use App\Models\User;
use App\Models\UserCompetitionAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * A competition's Overview and Settings (Svelte, in CompetitionLayout), and the saves Settings makes.
 */
class CompetitionDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $organiser;

    private Competition $comp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organiser = User::factory()->create();

        $this->comp = new Competition();
        $this->comp->name = 'Dashboard Champs';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->use_seeds = true;
        $this->comp->show_competition = true;
        $this->comp->save();

        $this->comp->addAccount($this->organiser, ['owner']);
    }

    public function test_an_organiser_gets_the_overview_with_every_tab_and_digital_judge(): void
    {
        $this->actingAs($this->organiser)->get($this->appUrl('/comps/' . $this->comp->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/Overview')
                ->where('competition.name', 'Dashboard Champs')
                ->where('competition.private', false)
                ->where('competition.tabs', fn ($tabs) => collect($tabs)->pluck('label')->all() === [
                    'Overview', 'Entries & Leagues', 'Events', 'Heats & Draws', 'Printables', 'Results', 'Officials', 'Settings',
                ])
                ->where('competition.tabs.0.current', true)
                ->where('competition.tabs.0.svelte', true)
                ->where('competition.tabs.1.svelte', true)
                ->where('competition.tabs.2.svelte', true)
                ->where('competition.tabs.3.svelte', true)
                ->where('competition.tabs.4.svelte', true)
                ->where('competition.tabs.5.svelte', true)
                ->where('digitalJudge.enabled', false)
                ->has('links.settingsUrl')
                ->has('events', 0)
                ->missing('leagues'));
    }

    public function test_a_results_only_account_gets_no_digital_judge_settings_or_events(): void
    {
        $results = User::factory()->create();
        $this->comp->addAccount($results, ['view', 'results']);

        $this->actingAs($results)->get(route('comps.view', $this->comp))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('competition.tabs', fn ($tabs) => collect($tabs)->pluck('label')->all() === ['Overview', 'Results'])
                ->where('digitalJudge', null)
                ->where('links', null)
                ->where('events', null));

        $this->get(route('manage.competitions.settings', $this->comp))->assertForbidden();
    }

    public function test_settings_lists_the_accounts_with_their_access(): void
    {
        $this->actingAs($this->organiser)->get(route('manage.competitions.settings', $this->comp))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Competition/Settings')
                ->where('competition.tabs.7.current', true)
                ->where('settings.name', 'Dashboard Champs')
                ->where('settings.use_seeds', true)
                ->has('accounts', 1)
                ->where('accounts.0.owner', true)
                ->where('accounts.0.you', true)
                ->where('accounts.0.access', []));
    }

    public function test_saving_settings_updates_the_competition_and_switches_save_off_too(): void
    {
        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->post(route('comps.settings', $this->comp), [
                'name' => 'Renamed Champs',
                'when' => '2030-03-02',
                'where' => 'New Pool',
                'max_lanes' => 6,
                'serc_start_time' => '2030-03-02T09:30',
                'timezone' => 'Europe/London',
                'show_competition' => '0',
                'can_be_live' => '1',
                'select_league_first' => '0',
                'ss:use_tanks' => '1',
                'use_seeds' => '0',
                'heats_per_event' => '1',
                'seed_per_event' => '0',
                'team_format' => ':C :N',
                'competitor_format' => ':N',
                'status_message' => '',
            ])
            ->assertRedirect(route('manage.competitions.settings', $this->comp))
            ->assertSessionHas('success', 'Settings saved');

        $comp = $this->comp->fresh();
        $this->assertSame('Renamed Champs', $comp->name);
        $this->assertSame('2030-03-02', $comp->when->toDateString());
        $this->assertSame(6, (int) $comp->max_lanes);
        $this->assertFalse((bool) $comp->show_competition);
        $this->assertFalse((bool) $comp->use_seeds);
        $this->assertTrue((bool) $comp->heats_per_event);
        $this->assertTrue((bool) $comp->getScoringSettings->use_tanks);
        // 9:30 in London in March is 9:30 UTC
        $this->assertSame('2030-03-02 09:30', $comp->serc_start_time->format('Y-m-d H:i'));
    }

    public function test_saving_one_part_of_settings_leaves_the_others_alone(): void
    {
        $before = $this->comp->fresh();

        // Details is its own form, so only its fields are sent
        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->post(route('comps.settings', $this->comp), [
                'name' => 'Renamed Champs',
                'when' => '2030-03-02',
                'where' => 'New Pool',
            ])
            ->assertSessionHas('success', 'Settings saved');

        $comp = $this->comp->fresh();
        $this->assertSame('Renamed Champs', $comp->name);
        $this->assertSame($before->max_lanes, $comp->max_lanes);
        $this->assertSame((bool) $before->use_seeds, (bool) $comp->use_seeds);
        $this->assertSame($before->team_format, $comp->team_format);
    }

    public function test_a_bad_setting_comes_back_as_a_field_error(): void
    {
        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->post(route('comps.settings', $this->comp), ['name' => 'ab'])
            ->assertSessionHasErrors('name');
    }

    public function test_digital_judge_settings_save(): void
    {
        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->post(route('dj.settings', $this->comp), ['show_teams_to_judges' => 'on'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue((bool) $this->comp->fresh()->show_teams_to_judges);
    }

    public function test_digital_judge_turns_on_with_a_pin_and_off_again(): void
    {
        $this->actingAs($this->organiser)->from(route('comps.view', $this->comp))
            ->post(route('dj.toggle', $this->comp))->assertRedirect(route('comps.view', $this->comp));

        $comp = $this->comp->fresh();
        $this->assertTrue((bool) $comp->digitalJudgeEnabled);
        $this->assertNotNull($comp->digitalJudgePin);

        $this->post(route('dj.toggle', $this->comp));
        $this->assertFalse((bool) $this->comp->fresh()->digitalJudgeEnabled);
    }

    public function test_inviting_someone_twice_is_an_error(): void
    {
        Mail::fake();

        $invite = fn () => $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->post(route('comps.accounts.invite', $this->comp), ['email' => 'new@example.com', 'access' => ['results']]);

        $invite()->assertSessionHasNoErrors()->assertSessionHas('success', 'Invite sent');
        $invite()->assertSessionHasErrors('email');
    }

    public function test_settings_lists_pending_invites_and_one_can_be_cancelled(): void
    {
        Mail::fake();
        AccountInviteController::invite('new@example.com', $this->comp, ['access' => ['results']]);
        $invite = AccountInvite::sole();

        $this->actingAs($this->organiser)->get(route('manage.competitions.settings', $this->comp))
            ->assertInertia(fn (Assert $page) => $page
                ->where('invites', [['id' => $invite->id, 'email' => 'new@example.com']]));

        $other = $this->makeOtherComp();
        AccountInviteController::invite('theirs@example.com', $other, ['access' => ['results']]);
        $theirs = AccountInvite::where('email', 'theirs@example.com')->sole();
        $this->actingAs($this->organiser)
            ->delete($this->appUrl("/comps/{$this->comp->id}/invites/{$theirs->id}"))
            ->assertNotFound();

        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->delete($this->appUrl("/comps/{$this->comp->id}/invites/{$invite->id}"))
            ->assertSessionHas('success', 'Invite cancelled');
        $this->assertSame(['theirs@example.com'], AccountInvite::pluck('email')->all());
    }

    public function test_an_accounts_access_can_be_changed(): void
    {
        $helper = User::factory()->create();
        $this->comp->addAccount($helper, ['results']);

        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->post(route('comps.accounts.edit', [$this->comp, $helper]), ['access' => ['teams', 'printables']])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Access saved');

        $this->assertEqualsCanonicalizing(['teams', 'printables'], UserCompetitionAccess::where('competition', $this->comp->id)
            ->where('user', $helper->id)->pluck('access_to')->all());
    }

    public function test_the_owner_and_yourself_cant_be_removed(): void
    {
        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->delete(route('comps.accounts.delete', [$this->comp, $this->organiser]))
            ->assertSessionHasErrors('account');

        $this->assertTrue($this->comp->userBelongsToCompetition($this->organiser));
    }

    public function test_deleting_needs_the_name_and_then_goes_home(): void
    {
        $this->actingAs($this->organiser)
            ->from(route('manage.competitions.settings', $this->comp))
            ->post(route('comps.delete', $this->comp), ['name' => 'Wrong'])
            ->assertSessionHasErrors('name');

        $this->assertNotNull($this->comp->fresh());

        $this->post(route('comps.delete', $this->comp), ['name' => 'Dashboard Champs'])
            ->assertRedirect(route('manage.home'))
            ->assertSessionHas('success', 'Competition deleted');

        $this->assertNull(Competition::find($this->comp->id));
    }

    private function makeOtherComp(): Competition
    {
        $comp = $this->comp->replicate();
        $comp->name = 'Other Champs';
        $comp->save();

        return $comp;
    }
}
