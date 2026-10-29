<?php

namespace App\Helpers;

use App\Models\Organisation\Organisation;

/**
 * What the Svelte OrganisationLayout draws above every organisation page: its name, logo and tabs. Every
 * page in that layout sends this as its `organisation` prop.
 */
class OrganisationHeader
{
    public static function for(Organisation $org): array
    {
        return [
            'id' => $org->id,
            'name' => $org->name,
            'logo' => $org->getLogo() ?: null,
            'tabs' => OrganisationTabs::for($org),
        ];
    }
}
