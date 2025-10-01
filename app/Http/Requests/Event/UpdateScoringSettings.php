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
            'name' => 'nullable|sometimes|string',
            'auto_penalties' => 'array',
            'auto_disqualifications' => 'array',
            'global_variables' => 'array',
            'local_variables' => 'array',
            'equation' => 'required|string',
            'penalty_func' => 'string|sometimes|nullable',
            'rank_higher' => 'boolean|required',
            'rank_equation' => 'string|sometimes|nullable',
            'allow_disqualified_to_rank' => 'boolean|required'
        ];
    }
}
