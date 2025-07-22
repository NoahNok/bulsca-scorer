<?php

namespace App\Http\Requests\Organisation;

use App\Models\Organisation\Organisation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InviteAccountToOrganisationRequest extends FormRequest
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
            'email' => 'required|email',
            'access' => 'required|array',
            'access.*' => 'in:' . implode(',', collect(Organisation::$accessTypes)->flatMap(fn($group) => array_keys($group))->all()),
        ];
    }

    public function messages(): array
    {
        return [
            'access.required' => 'You must provide at least one access permission.',

        ];
    }
}
