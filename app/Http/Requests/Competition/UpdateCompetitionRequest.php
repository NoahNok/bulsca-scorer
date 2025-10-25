<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|min:3|max:255',
            'when' => 'sometimes|date|date_format:Y-m-d',
            'where' => 'sometimes|string|min:3|max:255',
            'max_lanes' => 'sometimes|numeric|min:1',
            'serc_start_time' => 'sometimes|nullable|date',
            'can_be_live' => 'sometimes|boolean',
            'timezone' => 'sometimes|timezone',
            'team_format' => 'sometimes|string',
            'competitor_format' => 'sometimes|string',
            'ss:use_tanks' => 'sometimes|boolean',
            'heats_per_event' => 'sometimes|boolean',
            'seed_per_event' => 'sometimes|boolean',
            'use_seeds' => 'sometimes|boolean',
            'status_message' => 'sometimes|nullable|string',

        ];
    }
}
