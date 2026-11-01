<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A page of a list as the Svelte Pagination component reads it (Pagination in manage/types.ts): Admin's
 * Competitions and Users.
 */
class Pagination
{
    public static function props(LengthAwarePaginator $paginator): array
    {
        return [
            'current' => $paginator->currentPage(),
            'last' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
        ];
    }
}
