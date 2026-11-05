<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddSpeedEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'event' => ['required', 'integer', 'exists:speed_events,id'],
            'target_entity' => ['required', 'in:club,team,competitor'],
        ];
    }

    public function messages(): array
    {
        return [
            'event.required' => 'Pick an event.',
            'event.exists' => 'Pick an event from the list.',
        ];
    }
}
