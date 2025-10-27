<?php

namespace App\Http\Requests\Competition;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateCompetitionAccount extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // handled by route middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'access' => 'required|array',
            'access.*' => 'in:' . implode(',', array_keys(Competition::$accessTypes)),
        ];
    }

    public function messages(): array
    {
        return [
            'access.required' => 'You must provide at least one access permission.',

        ];
    }
}
