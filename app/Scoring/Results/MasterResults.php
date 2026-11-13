<?php

namespace App\Scoring\Results;

/**
 * A Master sheet's rows, best placed first, and who its Results sheets hold that counted for no
 * Club.
 */
final class MasterResults
{
    /**
     * @param MasterRow[] $rows
     * @param array<string, string[]> $withoutClub By sheet name: the names of the Entries left out for having no Club
     */
    public function __construct(
        public readonly array $rows,
        public readonly array $withoutClub = [],
    ) {}
}
