<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditCompetitionAccount extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            // admins can create accounts for any competition
            return true;
        }

        // get the current compeition
        /** @var Competition $competition */
        $competition = $this->route('comp');

        return $user->competition == $competition->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'access' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'access.required' => 'You must provide at least one access permission.',

        ];
    }
}
