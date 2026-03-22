<?php

namespace App\Observers;

use App\Models\Competition;
use App\Models\Competition\CompetitionScoringSettings;

class CompetitionObserver
{
    /**
     * Handle the Competition "created" event.
     */
    public function created(Competition $competition): void
    {
        $css = new CompetitionScoringSettings();
        $css->competition = $competition->id;
        $css->save();
    }

    /**
     * Handle the Competition "updated" event.
     */
    public function updated(Competition $competition): void
    {
        //
    }

    /**
     * Handle the Competition "deleted" event.
     */
    public function deleted(Competition $competition): void
    {
        //
    }

    /**
     * Handle the Competition "restored" event.
     */
    public function restored(Competition $competition): void
    {
        //
    }

    /**
     * Handle the Competition "force deleted" event.
     */
    public function forceDeleted(Competition $competition): void
    {
        //
    }
}
