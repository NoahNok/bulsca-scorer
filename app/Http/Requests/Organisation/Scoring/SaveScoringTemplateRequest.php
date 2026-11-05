<?php

namespace App\Http\Requests\Organisation\Scoring;

use App\Http\Requests\Event\UpdateScoringSettings;

/**
 * An organisation's Scoring template: what an event's scoring takes, and a name.
 */
class SaveScoringTemplateRequest extends UpdateScoringSettings
{
    public function rules(): array
    {
        return [...parent::rules(), 'name' => 'required|string|max:255', 'global_variables' => 'present|array'];
    }
}
