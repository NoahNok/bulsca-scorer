<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\DigitalJudge\JudgeDQSubmission;
use App\Models\Event\Disqualification;
use App\Models\Orders\Heat;
use App\Models\SERC;
use App\Models\SpeedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DQ details, judge notes and SERC breakdowns on the public results pages stay hidden until the
 * organiser releases that event's results, and can't be reached through another competition's urls.
 */
class ResultsReleaseTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;
    private CompetitionTeam $team;
    private Disqualification $dq;

    protected function setUp(): void
    {
        parent::setUp();

        ['comp' => $this->comp, 'serc' => $this->serc, 'team' => $this->team, 'dq' => $this->dq] = $this->competition('Our Competition');
    }

    private function competition(string $name): array
    {
        $comp = new Competition();
        $comp->name = $name;
        $comp->when = now()->subDay();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = true;
        $comp->use_seeds = true;
        $comp->save();

        $serc = new SERC();
        $serc->name = 'Dry SERC';
        $serc->competition = $comp->id;
        $serc->scorable_entity = 'team';
        $serc->save();

        $team = new CompetitionTeam();
        $team->competition = $comp->id;
        $team->team = 'A';
        $team->save();

        $dq = new Disqualification();
        $dq->code = 101;
        $dq->entity()->associate($team);
        $dq->event()->associate($serc);
        $dq->save();

        return compact('comp', 'serc', 'team', 'dq');
    }

    private function release(Competition $comp, SERC $serc): void
    {
        $comp->public_results = true;
        $comp->save();
        $serc->viewable = true;
        $serc->save();
    }

    private function violationUrl(Competition $comp, Disqualification $dq): string
    {
        return route('landing.competition.results.get.violation', ['comp' => $comp, 'violation_id' => $dq->id, 'violation_type' => 'dq']);
    }

    public function test_dq_details_are_hidden_until_results_are_released(): void
    {
        $this->get($this->violationUrl($this->comp, $this->dq))->assertNotFound();

        $this->release($this->comp, $this->serc);

        $this->get($this->violationUrl($this->comp, $this->dq))->assertOk()->assertJsonPath('code', 'DQ101');
    }

    public function test_dq_details_cant_be_reached_through_another_competitions_released_results(): void
    {
        $theirs = $this->competition('Their Competition');
        $this->release($theirs['comp'], $theirs['serc']);

        $this->get($this->violationUrl($theirs['comp'], $this->dq))->assertNotFound();
    }

    public function test_serc_judge_notes_are_hidden_until_results_are_released(): void
    {
        $url = route('landing.competition.results.notes.serc', ['comp' => $this->comp, 'serc' => $this->serc, 'entity_id' => $this->team->id]);

        $this->get($url)->assertNotFound();

        $this->release($this->comp, $this->serc);

        $this->get($url)->assertOk()->assertJsonPath('name', $this->team->getName($this->comp));
    }

    public function test_serc_breakdown_is_hidden_until_results_are_released(): void
    {
        $url = route('landing.competition.results.breakdown.serc', ['comp' => $this->comp, 'serc' => $this->serc]);

        $this->get($url)->assertNotFound();

        $this->release($this->comp, $this->serc);

        $this->get($url)->assertOk();
    }

    public function test_serc_notes_and_breakdown_cant_be_reached_through_another_competitions_released_results(): void
    {
        $theirs = $this->competition('Their Competition');
        $this->release($theirs['comp'], $theirs['serc']);
        $this->release($this->comp, $this->serc);
        $this->comp->public_results = false;
        $this->comp->save();

        $this->get(route('landing.competition.results.notes.serc', ['comp' => $theirs['comp'], 'serc' => $this->serc, 'entity_id' => $this->team->id]))->assertNotFound();
        $this->get(route('landing.competition.results.breakdown.serc', ['comp' => $theirs['comp'], 'serc' => $this->serc]))->assertNotFound();
    }
    public function test_event_results_cant_be_reached_through_another_competitions_released_results(): void
    {
        $theirs = $this->competition('Their Competition');
        $this->release($theirs['comp'], $theirs['serc']);
        $this->serc->viewable = true;
        $this->serc->save();

        $this->get(route('landing.competition.results.get', ['comp' => $theirs['comp'], 'league' => 'all', 'event' => $this->serc->id, 'type' => 'serc']))->assertForbidden();
    }

    public function test_results_site_hides_unreleased_events_and_their_notes(): void
    {
        $this->comp->public_results = true;
        $this->comp->save();
        $this->serc->viewable = false;
        $this->serc->save();

        $this->get(route('public.results.serc', ['comp' => $this->comp->resultsSlug(), 'event' => $this->serc]))->assertNotFound();
        $this->get(route('public.results.serc', ['comp' => $this->comp->resultsSlug(), 'event' => $this->serc, 'dlCSV' => 1]))->assertNotFound();
        $this->get(route('public.results.serc.entity-notes', ['comp' => $this->comp->resultsSlug(), 'event' => $this->serc, 'entity_id' => $this->team->id]))->assertNotFound();

        $this->release($this->comp, $this->serc);

        $this->get(route('public.results.serc.entity-notes', ['comp' => $this->comp->resultsSlug(), 'event' => $this->serc, 'entity_id' => $this->team->id]))->assertOk();
    }

    public function test_results_site_cant_reach_another_competitions_events(): void
    {
        $theirs = $this->competition('Their Competition');
        $this->release($theirs['comp'], $theirs['serc']);
        $this->serc->viewable = true;
        $this->serc->save();

        $this->get(route('public.results.serc', ['comp' => $theirs['comp']->resultsSlug(), 'event' => $this->serc]))->assertNotFound();
        $this->get(route('public.results.serc.entity-notes', ['comp' => $theirs['comp']->resultsSlug(), 'event' => $this->serc, 'entity_id' => $this->team->id]))->assertNotFound();
    }
    private function speedDqSubmission(?bool $resolved, bool $viewable): JudgeDQSubmission
    {
        $base = new SpeedEvent();
        $base->name = 'Swim & Tow';
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $this->comp->id;
        $event->record = 0;
        $event->viewable = $viewable;
        $event->save();

        $heat = new Heat();
        $heat->speed_event = $event->id;
        $heat->entity()->associate($this->team);
        $heat->heat = 1;
        $heat->lane = 1;
        $heat->save();

        $submission = new JudgeDQSubmission();
        $submission->competition = $this->comp->id;
        $submission->event_id = $event->id;
        $submission->event_type = CompetitionSpeedEvent::class;
        $submission->heat_lane = $heat->id;
        $submission->code = 'DQ012';
        $submission->name = 'Reporting Judge';
        $submission->position = 'Turn judge';
        $submission->resolved = $resolved;
        $submission->save();

        $this->comp->public_results = true;
        $this->comp->save();

        return $submission;
    }

    private function dqPenUrl(): string
    {
        return route('public.results.dq-pen', [$this->comp->resultsSlug(), $this->team, 'DQ012']);
    }

    public function test_results_site_shows_an_accepted_dq_from_a_released_event(): void
    {
        $this->speedDqSubmission(resolved: true, viewable: true);

        $this->get($this->dqPenUrl())->assertOk()->assertSee('Reporting Judge');
    }

    public function test_results_site_doesnt_show_who_reported_a_dq_in_a_hidden_event(): void
    {
        $this->speedDqSubmission(resolved: true, viewable: false);

        $this->get($this->dqPenUrl())->assertOk()->assertDontSee('Reporting Judge');
    }

    public function test_results_site_doesnt_show_dq_submissions_that_werent_accepted(): void
    {
        $this->speedDqSubmission(resolved: null, viewable: true);

        $this->get($this->dqPenUrl())->assertOk()->assertDontSee('Reporting Judge');
    }
}
