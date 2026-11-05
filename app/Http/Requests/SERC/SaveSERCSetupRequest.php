<?php

namespace App\Http\Requests\SERC;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveSERCSetupRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'type' => ['required', 'string', 'in:DRY,WET'],
            'target_entity' => ['required', 'string', 'in:club,team,competitor'],
            'judges' => ['required', 'array'],
            'judges.*.id' => ['nullable', 'integer', 'exists:serc_judges,id'],
            'judges.*.name' => ['required', 'string'],
            'judges.*.hint' => ['nullable', 'string'],
            'judges.*.marking_points' => ['required', 'array'],
            'judges.*.marking_points.*.id' => ['nullable', 'integer', 'exists:serc_marking_points,id'],
            'judges.*.marking_points.*.description' => ['required', 'string'],
            'judges.*.marking_points.*.weight' => ['required', 'numeric'],
            'judges.*.marking_points.*.template_id' => ['required', 'uuid', 'exists:marking_point_templates,id'],
            'use_restricted_judges' => ['sometimes', 'boolean'],
            'confirm_affected_marks' => ['sometimes', 'boolean'],
            'judges.*.restricted_leagues' => ['sometimes', 'array'],
            // only this competition's leagues can be assigned to an objective
            'judges.*.restricted_leagues.*' => ['integer', 'distinct', Rule::exists('leagues', 'id')->where('competition', $this->route('comp')->id)],
            'deleted.judges' => ['array'],
            'deleted.marking_points' => ['array'],


        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!$this->boolean('use_restricted_judges')) {
                    return;
                }

                // each league is marked against exactly one objective
                $leagueIds = collect($this->input('judges', []))->pluck('restricted_leagues')->flatten();

                if ($leagueIds->duplicates()->isNotEmpty()) {
                    $validator->errors()->add('judges', 'A League can only be on one Objective.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'SERC name',
            'type' => 'SERC type',
            'target_entity' => 'target entity',
            'judges' => 'Objectives',
            'judges.*.name' => 'Objective name',
            'judges.*.hint' => 'marking hint',
            'judges.*.marking_points' => 'marking points',
            'judges.*.marking_points.*.description' => 'marking point description',
            'judges.*.marking_points.*.weight' => 'marking point weight',
            'judges.*.marking_points.*.template_id' => 'marking point template',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The SERC name is required.',
            'type.required' => 'The SERC type is required.',
            'type.in' => 'The SERC type must be either DRY or WET.',
            'target_entity.required' => 'The target entity is required.',
            'target_entity.in' => 'The target entity must be either club, team, or competitor.',
            'judges.required' => 'Add at least one Objective.',
            'judges.*.name.required' => 'Give it a name.',
            'judges.*.marking_points.required' => 'Each Objective needs at least one marking point.',
            'judges.*.marking_points.*.description.required' => 'Describe it.',
            'judges.*.marking_points.*.weight.required' => 'Give it a weight.',
            'judges.*.marking_points.*.template_id.required' => 'Pick a template.',
        ];
    }
}
