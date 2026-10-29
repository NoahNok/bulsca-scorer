<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Organisation\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * An organisation's Overview and Settings (Svelte, in OrganisationLayout), and who can open its tabs.
 */
class OrganisationPagesTest extends TestCase
{
    use RefreshDatabase;

    private Organisation $org;

    private User $organiser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = new Organisation();
        $this->org->name = 'Cardiff ULSC';
        $this->org->save();

        $this->organiser = User::factory()->create();
        $this->org->addAccount($this->organiser, 'admin');
    }

    private function makeComp(string $name, $when, ?Organisation $org = null): Competition
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = $when;
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->show_competition = true;
        $comp->organisation = ($org ?? $this->org)->id;
        $comp->save();

        return $comp;
    }

    public function test_the_overview_lists_its_competitions_upcoming_first_then_past(): void
    {
        $this->makeComp('Last Year', now()->subYear());
        $this->makeComp('Next Month', now()->addMonth());
        $this->makeComp('Next Week', now()->addWeek());
        $this->makeComp('Last Month', now()->subMonth());

        $other = new Organisation();
        $other->name = 'Other';
        $other->save();
        $this->makeComp('Not Ours', now()->addWeek(), $other);

        $this->actingAs($this->organiser)->get($this->appUrl('/organisation/' . $this->org->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Overview')
                ->where('organisation.name', 'Cardiff ULSC')
                ->where('organisation.tabs', fn ($tabs) => collect($tabs)->pluck('label')->all() === [
                    'Overview', 'Championships', 'Accounts', 'Officials', 'Scoring', 'Violations', 'Settings',
                ])
                ->where('organisation.tabs.0.current', true)
                ->where('competitions', fn ($comps) => collect($comps)->pluck('name')->all() === [
                    'Next Week', 'Next Month', 'Last Month', 'Last Year',
                ])
                ->where('newCompetitionUrl', route('manage.competitions.create', ['org' => $this->org->id])));
    }

    public function test_it_still_opens_by_name(): void
    {
        $this->actingAs($this->organiser)->get($this->appUrl('/organisation/Cardiff ULSC'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Manage/Organisation/Overview'));
    }

    public function test_a_member_with_view_access_gets_only_the_overview(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($member)->get($this->appUrl('/organisation/' . $this->org->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('organisation.tabs', fn ($tabs) => collect($tabs)->pluck('label')->all() === ['Overview'])
                ->where('newCompetitionUrl', null));

        $championship = $this->org->championships()->create(['name' => 'League', 'start_date' => '2026-10-01', 'end_date' => '2027-03-31']);

        foreach (['edit', 'accounts', 'officials', 'championships', "championships/{$championship->id}", 'scoring', 'scoring/create', 'scoring/result-schemas/create', 'infractions'] as $tab) {
            $this->actingAs($member)->get($this->appUrl("/organisation/{$this->org->id}/{$tab}"))->assertForbidden();
        }
    }

    public function test_settings_has_its_name_and_logo(): void
    {
        $this->actingAs($this->organiser)->get($this->appUrl("/organisation/{$this->org->id}/edit"))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Settings')
                ->where('organisation.tabs.6.current', true)
                ->where('settings.name', 'Cardiff ULSC')
                ->where('settings.logo', null));
    }

    public function test_saving_settings_renames_it_and_comes_back_to_settings(): void
    {
        $this->actingAs($this->organiser)
            ->patch($this->appUrl("/organisation/{$this->org->id}"), ['name' => 'Cardiff Lifesaving'])
            ->assertRedirect(route('manage.organisations.settings', $this->org))
            ->assertSessionHasNoErrors();

        $this->assertSame('Cardiff Lifesaving', $this->org->fresh()->name);
    }

    public function test_settings_wants_a_name_no_other_organisation_has(): void
    {
        $other = new Organisation();
        $other->name = 'Swansea';
        $other->save();

        $this->actingAs($this->organiser)
            ->patch($this->appUrl("/organisation/{$this->org->id}"), ['name' => 'Swansea'])
            ->assertSessionHasErrors(['name' => 'An organisation with this name already exists.']);
        $this->actingAs($this->organiser)
            ->patch($this->appUrl("/organisation/{$this->org->id}"), ['name' => ''])
            ->assertSessionHasErrors('name');

        $this->assertSame('Cardiff ULSC', $this->org->fresh()->name);
    }

    public function test_settings_only_takes_png_jpeg_or_webp_logos(): void
    {
        $this->actingAs($this->organiser)
            ->post($this->appUrl("/organisation/{$this->org->id}"), [
                '_method' => 'PATCH',
                'name' => 'Cardiff ULSC',
                'logo' => UploadedFile::fake()->image('logo.gif'),
            ])
            ->assertSessionHasErrors('logo');

        $this->assertNull($this->org->fresh()->logo);
    }

    public function test_someone_outside_it_is_refused(): void
    {
        $this->actingAs(User::factory()->create())->get($this->appUrl('/organisation/' . $this->org->id))->assertForbidden();
    }
}
