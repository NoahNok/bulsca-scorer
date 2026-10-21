<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Access to the organisation is checked in the controller, so it can say which one
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
            'name' => 'required|string|min:3|max:255',
            'when' => 'required|date_format:Y-m-d',
            'where' => 'required|string|min:3|max:255',
            'lanes' => 'required|integer|min:1|max:10',
            // None makes a Personal Competition
            'organisation' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'when.date_format' => 'Enter the date the competition is on.',
            'lanes.max' => 'A pool has at most 10 lanes.',
        ];
    }
}
