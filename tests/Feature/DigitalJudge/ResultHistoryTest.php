<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\Activity\Activity;
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

class ResultHistoryTest extends TestCase
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

    private function activities(string $activity)
    {
        return Activity::with('relations')->where('activity', $activity)->get();
    }

    private function assertRelatedTo(Activity $activity, array $models): void
    {
        foreach ($models as $model) {
            $this->assertTrue(
                $activity->relations->contains(fn($r) => $r->related_type === get_class($model) && $r->related_id == $model->id),
                'Activity is not related to ' . class_basename($model) . " {$model->id}"
            );
        }
    }

    // ---------------------------------------------------------------- Marks

    private function sercWithMarkingPoints(): array
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

        $points = collect(['Approach', 'Rescue'])->map(function ($name) use ($serc, $judge) {
            $mp = new SERCMarkingPoint();
            $mp->name = $name;
            $mp->weight = 1;
            $mp->judge = $judge->id;
            $mp->serc = $serc->id;
            $mp->save();

            return $mp;
        });

        return [$serc, $judge, $points];
    }

    private function storeMarks(SERC $serc, SERCJudge $judge, CompetitionTeam $team, array $marks, ?User $as = null)
    {
        $params = ['competition' => $this->comp->getSlug(), 'serc' => $serc];

        $this->actingAs($as ?? $this->official)->post(route('judge.competition.serc.confirm.post', $params), ['judge' => $judge->id]);

        return $this->postJson(route('judge.competition.serc.mark.store', [...$params, 'entity_id' => $team->id]), [
            'marks' => [$judge->id => $marks],
        ]);
    }

    public function test_storing_marks_records_one_activity_with_each_changed_mark(): void
    {
        [$serc, $judge, [$approach, $rescue]] = $this->sercWithMarkingPoints();
        $team = $this->team('A');

        $this->storeMarks($serc, $judge, $team, [$approach->id => 7, $rescue->id => 5])->assertOk();

        $activities = $this->activities('SERC_MARKED');
        $this->assertCount(1, $activities);

        $activity = $activities->first();
        $this->assertEquals($this->official->id, $activity->user_id);
        $this->assertRelatedTo($activity, [$serc, $team, $this->comp]);

        $changes = collect($activity->context['changes'])->keyBy('marking_point');
        $this->assertCount(2, $changes);
        $this->assertNull($changes[$approach->id]['old']);
        $this->assertEquals(7, $changes[$approach->id]['new']);
        $this->assertEquals($judge->id, $changes[$approach->id]['judge']);
        $this->assertEquals('Casualty 1 - Approach', $changes[$approach->id]['name']);
    }

    public function test_only_marks_that_changed_are_recorded(): void
    {
        [$serc, $judge, [$approach, $rescue]] = $this->sercWithMarkingPoints();
        $team = $this->team('A');

        $this->storeMarks($serc, $judge, $team, [$approach->id => 7, $rescue->id => 5]);
        $this->storeMarks($serc, $judge, $team, [$approach->id => 8, $rescue->id => 5], as: $this->referee);

        $changeCounts = $this->activities('SERC_MARKED')
            ->map(fn($a) => count($a->context['changes']))->sort()->values()->all();

        $this->assertEquals([1, 2], $changeCounts);
    }

    public function test_resaving_identical_marks_records_nothing(): void
    {
        [$serc, $judge, [$approach]] = $this->sercWithMarkingPoints();
        $team = $this->team('A');

        $this->storeMarks($serc, $judge, $team, [$approach->id => 7]);
        $this->storeMarks($serc, $judge, $team, [$approach->id => 7], as: $this->referee);

        $this->assertCount(1, $this->activities('SERC_MARKED'));
    }

    // ---------------------------------------------------------------- Times and Places

    private function speedEventWithHeat(array $letters): array
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

    private function storeTimes(CompetitionSpeedEvent $event, array $marks, ?User $as = null)
    {
        return $this->actingAs($as ?? $this->official)->postJson(route('judge.competition.event.time.mark.store', [
            'competition' => $this->comp->getSlug(),
            'event' => $event,
            'heat' => 1,
        ]), ['mark' => $marks]);
    }

    private function storePlaces(CompetitionSpeedEvent $event, array $places, ?User $as = null)
    {
        return $this->actingAs($as ?? $this->official)->postJson(route('judge.competition.event.oof.mark.store', [
            'competition' => $this->comp->getSlug(),
            'event' => $event,
            'heat' => 1,
        ]), collect($places)->map(fn($place, $id) => ['entity' => ['id' => $id], 'oof' => $place])->values()->all());
    }

    public function test_storing_times_records_each_changed_time_with_its_heat(): void
    {
        [$event, [$a, $b]] = $this->speedEventWithHeat(['A', 'B']);

        $this->storeTimes($event, [$a->id => '1:02.50', $b->id => '0:58.10'])->assertOk();

        $activities = $this->activities('SPEED_RESULT_MARKED');
        $this->assertCount(2, $activities);

        $forA = $activities->first(fn($act) => $act->relations->contains(fn($r) => $r->related_type === CompetitionTeam::class && $r->related_id == $a->id));
        $this->assertEquals($this->official->id, $forA->user_id);
        $this->assertRelatedTo($forA, [$event, $a, $this->comp]);
        $this->assertEquals(1, $forA->context['heat']);
        $this->assertEquals('—', $forA->context['changes']['result']['old']);
        $this->assertEquals('01:02.500', $forA->context['changes']['result']['new']);
    }

    public function test_a_dnf_is_recorded_as_a_time_change(): void
    {
        [$event, [$a]] = $this->speedEventWithHeat(['A']);

        $this->storeTimes($event, [$a->id => '1:02.50']);
        $this->storeTimes($event, [$a->id => 'DNF'], as: $this->referee);

        $dnf = $this->activities('SPEED_RESULT_MARKED')->first(fn($act) => $act->context['changes']['result']['new'] === 'DNF');

        $this->assertNotNull($dnf);
        $this->assertEquals('01:02.500', $dnf->context['changes']['result']['old']);
    }

    public function test_resaving_the_same_time_records_nothing(): void
    {
        [$event, [$a]] = $this->speedEventWithHeat(['A']);

        $this->storeTimes($event, [$a->id => 'DNS']);
        $this->storeTimes($event, [$a->id => 'DNS']);

        $this->assertCount(1, $this->activities('SPEED_RESULT_MARKED'));
    }

    public function test_storing_places_records_one_activity_for_the_heat(): void
    {
        [$event, [$a, $b]] = $this->speedEventWithHeat(['A', 'B']);

        $this->storePlaces($event, [$a->id => 1, $b->id => 2])->assertOk();

        $activities = $this->activities('OOF_MARKED');
        $this->assertCount(1, $activities);

        $activity = $activities->first();
        $this->assertEquals($this->official->id, $activity->user_id);
        $this->assertEquals(1, $activity->context['heat']);
        $this->assertRelatedTo($activity, [$event, $a, $b, $this->comp]);

        $changes = collect($activity->context['changes'])->keyBy('entity');
        $this->assertEquals('—', $changes[$a->id]['old']);
        $this->assertEquals(1, $changes[$a->id]['new']);
    }

    public function test_clearing_a_place_is_recorded(): void
    {
        [$event, [$a, $b]] = $this->speedEventWithHeat(['A', 'B']);

        $this->storePlaces($event, [$a->id => 1, $b->id => 2]);
        $this->storePlaces($event, [$a->id => 1, $b->id => null], as: $this->referee);

        $this->assertEquals(0, EventOOF::where('event', $event->id)->where('oof', 2)->count());

        $latest = $this->activities('OOF_MARKED')->first(fn($act) => count($act->context['changes']) === 1);

        $this->assertNotNull($latest);
        $this->assertRelatedTo($latest, [$b]);
        $this->assertFalse($latest->relations->contains(fn($r) => $r->related_type === CompetitionTeam::class && $r->related_id == $a->id));
        $this->assertEquals(2, $latest->context['changes'][0]['old']);
        $this->assertEquals('—', $latest->context['changes'][0]['new']);
    }

    // ---------------------------------------------------------------- Confirmation

    public function test_confirming_results_records_who_confirmed(): void
    {
        [$event] = $this->speedEventWithHeat(['A']);

        $this->actingAs($this->referee)->postJson(route('judge.competition.event.confirm-results.store', [
            'competition' => $this->comp->getSlug(),
            'event' => $event,
        ]), ['check_conf' => true])->assertOk();

        $activities = $this->activities('RESULTS_CONFIRMED');
        $this->assertCount(1, $activities);
        $this->assertEquals($this->referee->id, $activities->first()->user_id);
        $this->assertRelatedTo($activities->first(), [$event, $this->comp]);
    }
}
