<?php

namespace Tests\Feature;

use App\Models\DQCode;
use App\Models\EventCode;
use App\Models\Organisation\Organisation;
use App\Models\PenaltyCode;
use App\Models\SpeedEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * An organisation's Violations (Svelte): its DQ and penalty codes, and making, editing and deleting them
 * with the kind of call each is for every speed event and SERC.
 */
class OrganisationViolationsTest extends TestCase
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

        $event = new SpeedEvent();
        $event->name = 'Swim & Tow';
        $event->record = 0;
        $event->save();
    }

    private function makeOrg(string $name): Organisation
    {
        $org = new Organisation();
        $org->name = $name;
        $org->save();

        return $org;
    }

    private function makeDq(int $code, string $description, ?Organisation $org = null): DQCode
    {
        $dq = new DQCode();
        $dq->code = $code;
        $dq->description = $description;
        $dq->organisation = ($org ?? $this->org)->id;
        $dq->save();

        return $dq;
    }

    private function eventCode($code, string $event, string $type): EventCode
    {
        $eventCode = new EventCode();
        $eventCode->event = $event;
        $eventCode->type = $type;
        $eventCode->pendq_id = $code->id;
        $eventCode->pendq_type = get_class($code);
        $eventCode->save();

        return $eventCode;
    }

    private function url(string $path = ''): string
    {
        return $this->appUrl("/organisation/{$this->org->id}/infractions{$path}");
    }

    public function test_violations_lists_its_dq_and_penalty_codes_with_their_calls(): void
    {
        $dq = $this->makeDq(12, 'Late start');
        $this->eventCode($dq, 'SERC', 'GENERIC');
        $this->makeDq(3, 'Early start');
        $this->makeDq(4, 'Not ours', $this->makeOrg('Swansea'));

        $pen = new PenaltyCode();
        $pen->code = 900;
        $pen->description = 'Hand off';
        $pen->organisation = $this->org->id;
        $pen->save();

        $this->actingAs($this->organiser)->get($this->url())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Violations')
                ->where('organisation.tabs.5.label', 'Violations')
                ->where('organisation.tabs.5.current', true)
                ->where('events', ['Swim & Tow', 'SERC'])
                // By code
                ->where('dqs.0.code', 3)
                ->where('dqs.1.code', 12)
                ->where('dqs.1.description', 'Late start')
                ->where('dqs.1.events', ['Swim & Tow' => null, 'SERC' => 'GENERIC'])
                ->has('dqs', 2)
                ->where('penalties.0.code', 900)
                ->has('penalties', 1)
                ->has('kinds', 9));
    }

    public function test_adding_a_code_saves_it_with_its_calls(): void
    {
        $this->actingAs($this->organiser)->from($this->url())->post($this->url(), [
            'type' => 'pen',
            'code' => 901,
            'description' => 'Rope wrapped',
            'events' => ['Swim & Tow' => 'TURN', 'SERC' => null],
        ])->assertRedirect($this->url())->assertSessionHasNoErrors();

        $pen = PenaltyCode::where('organisation', $this->org->id)->sole();
        $this->assertSame(901, (int) $pen->code);
        $this->assertSame('Rope wrapped', $pen->description);
        $this->assertSame(['Swim & Tow' => 'TURN'], $pen->eventCodes->pluck('type', 'event')->all());
    }

    public function test_a_new_code_wants_a_positive_number_no_other_code_of_its_kind_has(): void
    {
        $this->makeDq(12, 'Late start');
        // Another organisation's, and a penalty code, can share the number
        $this->makeDq(13, 'Not ours', $this->makeOrg('Swansea'));

        foreach ([[null, 'code'], [0, 'code'], [12, 'code']] as [$code, $field]) {
            $this->actingAs($this->organiser)->from($this->url())
                ->post($this->url(), ['type' => 'dq', 'code' => $code, 'description' => 'Again', 'events' => []])
                ->assertRedirect($this->url())
                ->assertSessionHasErrors($field);
        }

        $this->actingAs($this->organiser)->post($this->url(), ['type' => 'dq', 'code' => 13, 'description' => 'Ours too', 'events' => []])
            ->assertSessionHasNoErrors();
        $this->actingAs($this->organiser)->post($this->url(), ['type' => 'pen', 'code' => 12, 'description' => 'A penalty', 'events' => []])
            ->assertSessionHasNoErrors();

        $this->actingAs($this->organiser)->post($this->url(), ['type' => 'dq', 'code' => 14, 'description' => '', 'events' => []])
            ->assertSessionHasErrors('description');
    }

    public function test_saving_a_code_changes_its_description_and_calls(): void
    {
        $dq = $this->makeDq(12, 'Late start');
        $this->eventCode($dq, 'SERC', 'GENERIC');
        $this->eventCode($dq, 'Swim & Tow', 'LANE');

        $this->actingAs($this->organiser)->from($this->url())->patch($this->url("/dq/{$dq->id}"), [
            'description' => 'Very late start',
            // SERC changes, Swim & Tow goes, and an event that isn't one is ignored
            'events' => ['Swim & Tow' => null, 'SERC' => 'STARTER', 'Made up' => 'LANE'],
        ])->assertRedirect($this->url())->assertSessionHasNoErrors();

        $dq->refresh();
        $this->assertSame('Very late start', $dq->description);
        $this->assertSame(['SERC' => 'STARTER'], $dq->eventCodes->pluck('type', 'event')->all());
    }

    public function test_a_call_must_be_one_of_the_kinds(): void
    {
        $dq = $this->makeDq(12, 'Late start');

        $this->actingAs($this->organiser)->from($this->url())
            ->patch($this->url("/dq/{$dq->id}"), ['description' => 'Late', 'events' => ['SERC' => 'SHOUTING']])
            ->assertSessionHasErrors('events.SERC');
    }

    public function test_deleting_a_code_deletes_its_calls(): void
    {
        $dq = $this->makeDq(12, 'Late start');
        $call = $this->eventCode($dq, 'SERC', 'GENERIC');

        $this->actingAs($this->organiser)->from($this->url())->delete($this->url("/dq/{$dq->id}"))->assertRedirect($this->url());

        $this->assertModelMissing($dq);
        $this->assertModelMissing($call);
    }

    public function test_another_organisations_code_is_not_found(): void
    {
        $dq = $this->makeDq(12, 'Not ours', $this->makeOrg('Swansea'));

        $this->actingAs($this->organiser)->patch($this->url("/dq/{$dq->id}"), ['description' => 'Ours', 'events' => []])->assertNotFound();
        $this->actingAs($this->organiser)->delete($this->url("/dq/{$dq->id}"))->assertNotFound();
        $this->assertModelExists($dq);
    }

    public function test_a_member_with_view_access_is_refused_violations(): void
    {
        $member = User::factory()->create();
        $this->org->addAccount($member, 'view');

        $this->actingAs($member)->get($this->url())
            ->assertForbidden()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Organisation/Forbidden')
                ->where('organisation.tabs', fn ($tabs) => collect($tabs)->pluck('label')->all() === ['Overview']));
        $this->actingAs($member)->post($this->url(), ['type' => 'dq', 'code' => 1, 'description' => 'No', 'events' => []])->assertForbidden();
        $this->assertSame(0, DQCode::count());
    }
}
