<?php

namespace App\Http\Requests\DigitalJudge\Violation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitViolationRequest extends FormRequest
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
            'entity_id' => ['required', 'integer'],

            'event' => ['required', 'array'],
            'event.id' => ['required', 'integer'],
            'event.type' => ['required', 'string'],

            'violation' => ['required', 'array'],
            'violation.id' => ['required', 'integer'],
            'violation.vtype' => ['required', 'in:DQ,PEN'],

            'details' => ['required', 'array'],
            'details.turn' => ['nullable', 'integer'],
            'details.lane' => ['nullable', 'integer'],
            'details.details' => ['nullable', 'string'],

            'submitter' => ['required', 'array'],
            'submitter.name' => ['nullable', 'string'],
            'submitter.position' => ['nullable', 'string'],
            'submitter.user' => ['nullable', 'array'],

            'seconder' => ['required', 'array'],
            'seconder.name' => ['nullable', 'string'],
            'seconder.position' => ['nullable', 'string'],
            'seconder.user' => ['nullable', 'array'],
        ];
    }
}
