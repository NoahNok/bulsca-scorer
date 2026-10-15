<?php

namespace App\Http\Requests\DigitalJudge\Violation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'details.length' => ['nullable', 'integer'],
            'details.details' => ['nullable', 'string'],

            'submitter' => ['required', 'array'],
            'submitter.name' => ['nullable', 'string'],
            'submitter.position' => ['nullable', 'string'],
            'submitter.user' => ['nullable', 'array'],

            'seconder' => ['required', 'array'],
            // The seconder has to be another official on this competition
            'seconder.user_id' => [
                'nullable',
                'integer',
                Rule::exists('competition_officials', 'user_id')->where('competition_id', $this->route('competition')->id),
                Rule::notIn([$this->user()?->id]),
            ],
            'seconder.position' => ['nullable', 'string'],
        ];
    }
}
