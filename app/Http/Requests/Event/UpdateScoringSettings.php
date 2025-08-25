<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScoringSettings extends FormRequest
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
            'name' => 'required|string',
            'auto_penalties' => 'array',
            'auto_disqualifications' => 'array',
            'global_variables' => 'array',
            'local_variables' => 'array',
            'equation' => 'required|string',
            'penalty_func' => 'string|sometimes|nullable',
        ];
    }
}
