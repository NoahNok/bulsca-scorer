<?php

namespace Tests\Feature\Scoring;

use App\Scoring\CompetitionResults;
use App\Models\Club;
use App\Models\CompetitionTeam;
use App\Models\MasterSchema;
use App\Models\ResultSchema;

/**
 * How a Master sheet ranks Clubs across Results sheets: each Club takes its best place in every
 * sheet, the places are added up, and the fewest wins.
 *
 * In the A League sheet Cardiff's A is 1st, Swansea's C 2nd and Bangor's E 3rd. In the B League
 * sheet Swansea's D is 1st and Cardiff's B 2nd; Bangor has nobody.
 */
class MasterSheetScoringTest extends ScoringTestCase
{
    private Club $swansea;

    private Club $bangor;

    /** @var ResultSchema[] */
    private array $sheets;

    protected function setUp(): void
    {
        parent::setUp();

        $this->swansea = $this->club('Swansea');
        $this->bangor = $this->club('Bangor');
        $a = $this->league('A League');
        $b = $this->league('B League');

        $event = $this->speedEvent(self::RAW, [
            [$this->team('A', $this->club, $a), 1000],
            [$this->team('B', $this->club, $b), 700],
            [$this->team('C', $this->swansea, $a), 900],
            [$this->team('D', $this->swansea, $b), 950],
            [$this->team('E', $this->bangor, $a), 800],
        ]);

        $this->sheets = [
            $this->sheet(['league' => $a->id], [$event]),
            $this->sheet(['league' => $b->id], [$event]),
        ];
    }

    private function rows(MasterSchema $master): array
    {
        return array_map(fn($r) => [$r->club->name, $r->position, $r->total, array_values($r->sheetResults)], app(CompetitionResults::class)->master($master)->rows);
    }

    public function test_each_clubs_places_are_added_up_and_the_fewest_wins(): void
    {
        $master = $this->master([], $this->sheets);

        // Bangor isn't in the B League sheet, so takes the default (10) there
        $this->assertSame([
            ['Cardiff', 1, 3.0, [1, 2]],
            ['Swansea', 1, 3.0, [2, 1]],
            ['Bangor', 3, 13.0, [3, 10]],
        ], $this->rows($master));
    }

    public function test_a_club_counts_its_best_placed_entry_in_each_sheet(): void
    {
        $extra = CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $this->bangor->id, 'team' => 'F']);
        $extra->leagues()->attach($this->sheets[1]->schema['league']);
        $this->time($this->comp->getSpeedEvents->first(), $extra, 990);
        $second = CompetitionTeam::create(['competition' => $this->comp->id, 'club' => $this->bangor->id, 'team' => 'G']);
        $second->leagues()->attach($this->sheets[1]->schema['league']);
        $this->time($this->comp->getSpeedEvents->first(), $second, 100);

        $master = $this->master([], $this->sheets);

        // Bangor's F wins the B League sheet (G is last in it): Bangor 3 + 1, Swansea 2 + 2, Cardiff 1 + 3
        $this->assertSame([
            ['Cardiff', 1, 4.0, [1, 3]],
            ['Swansea', 1, 4.0, [2, 2]],
            ['Bangor', 1, 4.0, [3, 1]],
        ], $this->rows($master));
    }

    public function test_excluded_clubs_are_left_out(): void
    {
        $master = $this->master(['exclude' => [$this->bangor->id]], $this->sheets);

        $this->assertSame(['Cardiff', 'Swansea'], array_map(fn($r) => $r->club->name, app(CompetitionResults::class)->master($master)->rows));
    }
}
