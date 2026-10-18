<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\EventOOF;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SERCResult;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Once a Mark, Time or Place has been entered, only a Referee can change it.
 */
class JudgeResultLocksTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private User $official;
    private User $referee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Test Competition';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = true;
        $this->comp->use_seeds = true;
        $this->comp->sercs_released_at = now();
        $this->comp->save();

        $this->official = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->official);

        $this->referee = User::factory()->create(['judge_only' => true]);
        $this->comp->addOfficial($this->referee, 'referee');
    }

    private function team(string $letter): CompetitionTeam
    {
        $team = new CompetitionTeam();
        $team->competition = $this->comp->id;
        $team->team = $letter;
        $team->save();

        return $team;
    }

    // ---------------------------------------------------------------- Marks

    private function serc(): array
    {
        $serc = new SERC();
        $serc->name = 'Dry SERC';
        $serc->competition = $this->comp->id;
        $serc->digitalJudgeEnabled = true;
        $serc->save();

        $judge = new SERCJudge();
        $judge->name = 'Casualty 1';
        $judge->description = '';
        $judge->serc = $serc->id;
        $judge->save();

        $mp = new SERCMarkingPoint();
        $mp->name = 'Approach';
        $mp->weight = 1;
        $mp->judge = $judge->id;
        $mp->serc = $serc->id;
        $mp->save();

        return [$serc, $judge, $mp];
    }

    private function selectJudge(SERC $serc, SERCJudge $judge, User $as): array
    {
        $params = ['competition' => $this->comp->getSlug(), 'serc' => $serc];

        $this->actingAs($as)->post(route('judge.competition.serc.confirm.post', $params), ['judge' => $judge->id]);

        return $params;
    }

    private function storeMark(SERC $serc, SERCJudge $judge, SERCMarkingPoint $mp, CompetitionTeam $team, int $mark, User $as)
    {
        $params = $this->selectJudge($serc, $judge, $as);

        return $this->postJson(route('judge.competition.serc.mark.store', [...$params, 'entity_id' => $team->id]), [
            'marks' => [$judge->id => [$mp->id => $mark]],
        ]);
    }

    private function markFor(SERCMarkingPoint $mp, CompetitionTeam $team)
    {
        return SERCResult::where('marking_point', $mp->id)->whereMorphedTo('entity', $team)->value('result');
    }

    public function test_an_official_cannot_overwrite_submitted_marks(): void
    {
        [$serc, $judge, $mp] = $this->serc();
        $team = $this->team('A');

        $this->storeMark($serc, $judge, $mp, $team, 7, as: $this->official)->assertOk();

        $this->storeMark($serc, $judge, $mp, $team, 3, as: $this->official)
            ->assertStatus(409)
            ->assertJson(['message' => 'Already marked — ask a referee to change it.']);

        $this->assertEquals(7, $this->markFor($mp, $team));
    }

    public function test_a_referee_can_override_submitted_marks(): void
    {
        [$serc, $judge, $mp] = $this->serc();
        $team = $this->team('A');

        $this->storeMark($serc, $judge, $mp, $team, 7, as: $this->official);
        $this->storeMark($serc, $judge, $mp, $team, 3, as: $this->referee)->assertOk();

        $this->assertEquals(3, $this->markFor($mp, $team));
    }

    public function test_an_official_is_sent_on_from_a_team_already_marked(): void
    {
        [$serc, $judge, $mp] = $this->serc();
        $team = $this->team('A');

        $this->storeMark($serc, $judge, $mp, $team, 7, as: $this->official);

        $params = $this->selectJudge($serc, $judge, $this->official);

        $this->get(route('judge.competition.serc.mark.entity', [...$params, 'entity_id' => $team->id]))
            ->assertRedirect(route('judge.competition.serc.mark.next', ['competition' => $this->comp, 'serc' => $serc]));
    }

    // ---------------------------------------------------------------- Times and Places

    private function heat(array $letters): array
    {
        $speedEvent = new SpeedEvent();
        $speedEvent->name = 'Swim & Tow';
        $speedEvent->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $speedEvent->id;
        $event->competition = $this->comp->id;
        $event->record = 0;
        $event->save();

        $teams = collect($letters)->map(function ($letter, $i) use ($event) {
            $team = $this->team($letter);

            $lane = new Heat();
            $lane->speed_event = $event->id;
            $lane->entity()->associate($team);
            $lane->heat = 1;
            $lane->lane = $i + 1;
            $lane->save();

            $result = new SpeedResult();
            $result->event = $event->id;
            $result->entity()->associate($team);
            $result->save();

            return $team;
        });

        return [$event, $teams];
    }

    private function params(CompetitionSpeedEvent $event): array
    {
        return ['competition' => $this->comp->getSlug(), 'event' => $event, 'heat' => 1];
    }

    private function storeTimes(CompetitionSpeedEvent $event, array $marks, User $as)
    {
        return $this->actingAs($as)->postJson(route('judge.competition.event.time.mark.store', $this->params($event)), ['mark' => $marks]);
    }

    private function storePlaces(CompetitionSpeedEvent $event, array $places, User $as)
    {
        return $this->actingAs($as)->postJson(
            route('judge.competition.event.oof.mark.store', $this->params($event)),
            collect($places)->map(fn($place, $id) => ['entity' => ['id' => $id], 'oof' => $place])->values()->all()
        );
    }

    private function timeFor(CompetitionSpeedEvent $event, CompetitionTeam $team)
    {
        return SpeedResult::whereMorphedTo('entity', $team)->where('event', $event->id)->value('result');
    }

    private function placeFor(CompetitionSpeedEvent $event, CompetitionTeam $team)
    {
        return EventOOF::where('event', $event->id)
            ->whereIn('heat_lane', Heat::whereMorphedTo('entity', $team)->pluck('id'))
            ->value('oof');
    }

    public function test_an_official_can_fill_empty_lanes_alongside_existing_times(): void
    {
        [$event, [$a, $b]] = $this->heat(['A', 'B']);

        $this->storeTimes($event, [$a->id => '1:02.50'], as: $this->official)->assertOk();

        // the page sends A's time back as it showed it
        $this->storeTimes($event, [$a->id => '01:02.50', $b->id => '0:58.10'], as: $this->official)->assertOk();

        $this->assertEquals(62500, $this->timeFor($event, $a));
        $this->assertEquals(58100, $this->timeFor($event, $b));
    }

    public function test_an_official_cannot_change_an_existing_time_and_nothing_is_saved(): void
    {
        [$event, [$a, $b]] = $this->heat(['A', 'B']);

        $this->storeTimes($event, [$a->id => '1:02.50'], as: $this->official);

        $this->storeTimes($event, [$a->id => '1:10.00', $b->id => '0:58.10'], as: $this->official)
            ->assertStatus(409)
            ->assertJson(['message' => 'That lane already has a time — ask a referee to change it.']);

        $this->assertEquals(62500, $this->timeFor($event, $a));
        $this->assertNull($this->timeFor($event, $b));
    }

    public function test_a_referee_can_change_an_existing_time(): void
    {
        [$event, [$a]] = $this->heat(['A']);

        $this->storeTimes($event, [$a->id => '1:02.50'], as: $this->official);
        $this->storeTimes($event, [$a->id => '1:10.00'], as: $this->referee)->assertOk();

        $this->assertEquals(70000, $this->timeFor($event, $a));
    }

    public function test_a_fully_timed_heat_is_closed_to_officials_but_open_to_referees(): void
    {
        [$event, [$a]] = $this->heat(['A']);

        $this->storeTimes($event, [$a->id => '1:02.50'], as: $this->official);

        $this->actingAs($this->official)->get(route('judge.competition.event.time.mark', $this->params($event)))
            ->assertRedirect(route('judge.competition.event.time.select-heat', ['competition' => $this->comp->getSlug(), 'event' => $event]));

        $this->actingAs($this->referee)->get(route('judge.competition.event.time.mark', $this->params($event)))
            ->assertOk();
    }

    public function test_an_official_cannot_change_places_once_saved(): void
    {
        [$event, [$a, $b]] = $this->heat(['A', 'B']);

        $this->storePlaces($event, [$a->id => 1, $b->id => 2], as: $this->official)->assertOk();

        $this->storePlaces($event, [$a->id => 2, $b->id => 1], as: $this->official)
            ->assertStatus(409)
            ->assertJson(['message' => 'This heat has places — ask a referee to change them.']);

        $this->assertEquals(1, $this->placeFor($event, $a));

        $this->actingAs($this->official)->get(route('judge.competition.event.oof.mark', $this->params($event)))
            ->assertRedirect(route('judge.competition.event.oof.select-heat', ['competition' => $this->comp->getSlug(), 'event' => $event]));
    }

    public function test_a_referee_can_change_places(): void
    {
        [$event, [$a, $b]] = $this->heat(['A', 'B']);

        $this->storePlaces($event, [$a->id => 1, $b->id => 2], as: $this->official);
        $this->storePlaces($event, [$a->id => 2, $b->id => 1], as: $this->referee)->assertOk();

        $this->assertEquals(2, $this->placeFor($event, $a));
    }

    public function test_places_for_a_team_outside_the_heat_are_rejected_and_nothing_is_saved(): void
    {
        [$event, [$a]] = $this->heat(['A']);
        $outsider = $this->team('B');

        $this->storePlaces($event, [$a->id => 1, $outsider->id => 2], as: $this->official)->assertStatus(422);
        $this->storePlaces($event, [$a->id => 1, 999999 => 2], as: $this->official)->assertStatus(422);

        $this->assertNull($this->placeFor($event, $a));
    }
}
