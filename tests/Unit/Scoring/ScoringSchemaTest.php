<?php

namespace Tests\Unit\Scoring;

use App\Models\Event\ScoringSchema;
use PHPUnit\Framework\TestCase;

class ScoringSchemaTest extends TestCase
{
    public function test_a_formula_can_be_made_with_its_name_and_settings(): void
    {
        $formula = new ScoringSchema(['name' => 'Time as points', 'schema' => ['equation' => 'item.resolvedResult / 1000']]);

        $this->assertSame('Time as points', $formula->name);
        $this->assertSame(['equation' => 'item.resolvedResult / 1000'], $formula->schema);
    }

    public function test_its_settings_are_read_with_their_defaults(): void
    {
        $formula = (new ScoringSchema(['schema' => ['equation' => 'item.resolvedResult', 'rank_higher' => false, 'penalty_func' => '']]))->formula();

        $this->assertSame('item.resolvedResult', $formula->equation);
        $this->assertFalse($formula->rankHigher);
        $this->assertNull($formula->penaltyFunction);
        $this->assertNull($formula->rankEquation);
        $this->assertFalse($formula->allowDisqualifiedToRank);
        $this->assertFalse($formula->forcePenaltyFunction);
        $this->assertSame([], $formula->groupBy);
        $this->assertTrue((new ScoringSchema(['schema' => []]))->formula()->rankHigher);
    }
}
