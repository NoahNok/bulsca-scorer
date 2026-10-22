<?php

namespace App\Helpers;

use App\Models\Competition;

/**
 * What the Svelte CompetitionLayout draws above every competition page: its name, date, venue, badges,
 * organisation, status message and tabs. Every page in that layout sends this as its `competition` prop.
 */
class CompetitionHeader
{
    public static function for(Competition $comp): array
    {
        $org = $comp->getOrganisation;
        $today = $comp->when->isToday();

        return [
            'id' => $comp->id,
            'name' => $comp->name,
            'when' => $comp->when->toDateString(),
            'where' => $comp->where,
            'organisation' => $org ? [
                'name' => $org->name,
                'logo' => $org->getLogo() ?: null,
                'url' => route('orgs.show', $org->name),
            ] : null,
            'championship' => $org && $comp->championship ? [
                'name' => $comp->championship->name,
                'url' => route('orgs.championship.view', [$org->name, $comp->championship->slug()]),
            ] : null,
            'private' => !$comp->show_competition,
            // As on the Management home: no flag says a competition is running
            'live' => $today && (bool) $comp->can_be_live,
            'statusMessage' => $comp->status_message ?: null,
            'tabs' => CompetitionTabs::for($comp),
        ];
    }
}
