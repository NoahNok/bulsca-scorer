<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SERC;

/**
 * A Results sheet's Formula (how each Event's results become points) and its tie-breaks. A sheet keeps
 * only the equation and variables, not which Formula they came from, so a built-in is recognised by
 * its equation and ranking direction.
 */
class ResultSheetFormulas
{
    /**
     * The built-in Formulas: key => [label, equation, rank higher]
     */
    public const BUILT_IN = [
        'points' => ['Highest points sum wins', 'item.points', true],
        'points-low' => ['Lowest points sum wins', 'item.points', false],
        'position' => ['Highest position sum wins', 'item.position', true],
        'position-low' => ['Lowest position sum wins', 'item.position', false],
        'result' => ['Highest result sum wins', 'item.resolvedResult', true],
        'result-low' => ['Lowest result sum wins', 'item.resolvedResult', false],
    ];

    /**
     * The built-in a sheet's Formula matches, or null for a custom one
     */
    public static function builtInFor(array $schema): ?string
    {
        if (!empty($schema['global_variables'])) {
            return null;
        }

        foreach (self::BUILT_IN as $key => [, $equation, $rankHigher]) {
            if (trim($schema['equation'] ?? '') === $equation && (bool) ($schema['rank_higher'] ?? true) === $rankHigher) {
                return $key;
            }
        }

        return null;
    }

    public static function labelFor(array $schema): string
    {
        $key = self::builtInFor($schema);

        return $key ? self::BUILT_IN[$key][0] : 'Custom';
    }

    /**
     * An Event's type in a sheet's tie-breaks and the form: speed or serc
     */
    public static function typeOf(object $event): string
    {
        return $event instanceof SERC ? 'serc' : 'speed';
    }

    /**
     * Whether a stored tie-break is this Event. Tie-breaks were once bare ids, which can't tell a speed
     * event from a SERC with the same id, so those match on the id alone, as they always did.
     */
    public static function tieBreakIs(int|array $tieBreak, object $event): bool
    {
        if (!is_array($tieBreak)) {
            return $event->id == $tieBreak;
        }

        return $tieBreak['type'] === self::typeOf($event) && $event->id == $tieBreak['id'];
    }

    /**
     * A competition's Event of the given type and id
     */
    public static function findEvent(Competition $comp, string $type, int $id): SERC|CompetitionSpeedEvent|null
    {
        return ($type === 'serc' ? $comp->getSERCs() : $comp->getSpeedEvents())->find($id);
    }
}
