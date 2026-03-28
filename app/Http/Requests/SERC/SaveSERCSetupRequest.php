<?php

namespace App\Http\Requests\SERC;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'deleted.judges' => ['array'],
            'deleted.marking_points' => ['array'],


        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'SERC name',
            'type' => 'SERC type',
            'target_entity' => 'target entity',
            'judges' => 'judges',
            'judges.*.name' => 'judge name',
            'judges.*.hint' => 'judge hint',
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
            'judges.required' => 'At least one judge is required.',
            'judges.*.name.required' => 'Each judge must have a name.',
            'judges.*.marking_points.required' => 'Each judge must have at least one marking point.',
            'judges.*.marking_points.*.description.required' => 'Each marking point must have a description.',
            'judges.*.marking_points.*.weight.required' => 'Each marking point must have a weight.',
        ];
    }
}
