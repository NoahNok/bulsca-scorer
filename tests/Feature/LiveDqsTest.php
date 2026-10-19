<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\SERC;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The live site only shows competitions the organiser has made live or whose results are public,
 * so nobody can view another competition's DQs or progress by putting its id in the url.
 */
class LiveDqsTest extends TestCase
{
    use RefreshDatabase;

    private Competition $comp;
    private SERC $serc;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Test Competition';
        $this->comp->when = now();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = true;
        $this->comp->use_seeds = true;
        $this->comp->can_be_live = false;
        $this->comp->public_results = false;
        $this->comp->save();

        $this->serc = new SERC();
        $this->serc->name = 'Dry SERC';
        $this->serc->competition = $this->comp->id;
        $this->serc->scorable_entity = 'team';
        $this->serc->save();
    }

    private function eventDqsUrl(): string
    {
        return route('live.dqs.event', ['event' => 'se-' . $this->serc->id]) . '?comp=' . $this->comp->id;
    }

    public function test_dqs_for_a_competition_that_isnt_live_cant_be_viewed(): void
    {
        $this->get($this->eventDqsUrl())->assertNotFound();
        $this->get(route('live.dqs') . '?comp=' . $this->comp->id)->assertNotFound();
        $this->get(route('live') . '?comp=' . $this->comp->id)->assertNotFound();
        $this->get(route('live.data', ['comp' => $this->comp]))->assertNotFound();
    }

    public function test_dqs_for_a_live_competition_can_be_viewed(): void
    {
        $this->comp->can_be_live = true;
        $this->comp->save();

        $this->get($this->eventDqsUrl())->assertOk();
    }

    public function test_dqs_for_a_past_competition_with_public_results_can_be_viewed(): void
    {
        $this->comp->public_results = true;
        $this->comp->save();

        $this->get($this->eventDqsUrl())->assertOk();
    }

    public function test_dqs_for_another_competitions_event_cant_be_viewed(): void
    {
        $other = $this->comp->replicate();
        $other->can_be_live = true;
        $other->save();

        $this->get(route('live.dqs.event', ['event' => 'se-' . $this->serc->id]) . '?comp=' . $other->id)->assertNotFound();
    }
}
