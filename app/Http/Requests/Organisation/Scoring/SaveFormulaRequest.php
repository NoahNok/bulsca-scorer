<?php

namespace App\Http\Requests\Organisation\Scoring;

use App\Http\Requests\Result\UpdateResultSchemaScoringSettings;

/**
 * An organisation's Formula: its global variables and result equation, and a name.
 */
class SaveFormulaRequest extends UpdateResultSchemaScoringSettings
{
    public function rules(): array
    {
        return [...parent::rules(), 'name' => 'required|string|max:255', 'global_variables' => 'present|array'];
    }
}
