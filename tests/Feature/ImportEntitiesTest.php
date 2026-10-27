<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SpeedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Speed events give entities their results when the event is created, but importing doesn't,
 * so importing into a competition that already has speed events warns the organiser.
 */
class ImportEntitiesTest extends TestCase
{
    use RefreshDatabase;

    private const WARNING = 'This competition already has speed events.';

    private Competition $comp;
    private string $csv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comp = new Competition();
        $this->comp->name = 'Import Competition';
        $this->comp->when = now()->addWeek();
        $this->comp->where = 'Test Pool';
        $this->comp->max_lanes = 8;
        $this->comp->anytimepin = false;
        $this->comp->seed_per_event = false;
        $this->comp->heats_per_event = false;
        $this->comp->use_seeds = false;
        $this->comp->save();

        $this->csv = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($this->csv, "League,Club,Team,Competitor 1\n");
    }

    protected function tearDown(): void
    {
        @unlink($this->csv);

        parent::tearDown();
    }

    public function test_importing_after_speed_events_exist_warns(): void
    {
        $base = new SpeedEvent();
        $base->name = 'Rope Throw';
        $base->save();

        $event = new CompetitionSpeedEvent();
        $event->event = $base->id;
        $event->competition = $this->comp->id;
        $event->record = 0;
        $event->save();

        $this->artisan('import-entities', ['competition_id' => $this->comp->id, 'csv' => $this->csv])
            ->expectsOutputToContain(self::WARNING)
            ->assertSuccessful();
    }

    public function test_importing_before_speed_events_exist_does_not_warn(): void
    {
        $this->artisan('import-entities', ['competition_id' => $this->comp->id, 'csv' => $this->csv])
            ->doesntExpectOutputToContain(self::WARNING)
            ->assertSuccessful();
    }
}
