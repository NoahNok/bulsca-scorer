<?php

namespace App\Http\Requests\Organisation\Championship;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateChampionshipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Organisation $org */
        $org = $this->route('organisation');

        return $org->canUser(Auth::user(), 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|unique:championships',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'A championship with this name already exists.'
        ];
    }
}
