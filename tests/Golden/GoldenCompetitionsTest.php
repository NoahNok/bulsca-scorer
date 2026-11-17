<?php

namespace Tests\Golden;

use App\Models\AbstractClasses\Entity;
use App\Scoring\Golden\CompetitionFixture;
use App\Scoring\Golden\CompetitionSnapshot;
use App\Scoring\Golden\GoldenJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Real competitions, scored again: each fixture's `input.json` is a competition as it was entered
 * (competitors made anonymous) and its `expected.json` is everything the engine worked out for it.
 * A change to the engine that moves any points, place, penalty or row order for a competition that
 * has already happened fails here.
 *
 * Record a new or deliberately changed result with `UPDATE_GOLDEN=1 php artisan test --testsuite=Golden`,
 * and read the diff of expected.json before committing it. See docs/scoring-engine.md.
 */
class GoldenCompetitionsTest extends TestCase
{
    use RefreshDatabase;

    private const FIXTURES = __DIR__ . '/fixtures';

    public static function competitions(): array
    {
        $competitions = [];

        foreach (glob(self::FIXTURES . '/*/input.json') as $input) {
            $competitions[basename(dirname($input))] = [dirname($input)];
        }

        return $competitions;
    }

    #[DataProvider('competitions')]
    public function test_the_competition_scores_as_it_did(string $fixture): void
    {
        // Names are remembered per id for the life of the process, and fixtures reuse live ids
        (new \ReflectionProperty(Entity::class, 'nameCache'))->setValue(null, []);

        $comp = CompetitionFixture::load(GoldenJson::read("{$fixture}/input.json"));

        $actual = CompetitionSnapshot::of($comp);

        if (getenv('UPDATE_GOLDEN')) {
            GoldenJson::write("{$fixture}/expected.json", $actual);
        }

        $this->assertFileExists("{$fixture}/expected.json", 'No expected.json yet: record it with UPDATE_GOLDEN=1');

        $differences = GoldenJson::differences(GoldenJson::read("{$fixture}/expected.json"), $actual);

        $this->assertSame([], array_slice($differences, 0, 25), count($differences) . ' results changed for ' . basename($fixture));
    }

    public function test_there_are_fixtures_to_check(): void
    {
        $this->assertNotEmpty(self::competitions());
    }
}
