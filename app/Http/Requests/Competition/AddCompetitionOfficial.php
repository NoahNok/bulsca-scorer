<?php

namespace App\Http\Requests\Competition;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCompetitionOfficial extends FormRequest
{
    // own bag so the officials page knows to reopen the add modal
    protected $errorBag = 'addOfficial';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // handled by route middleware
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'name' => trim((string) $this->input('name')),
        ]);

        // an empty user_id still counts as present to exclude_with
        if (blank($this->input('user_id'))) {
            $this->request->remove('user_id');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // picked from the search, so we never need their email
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'email' => ['exclude_with:user_id', 'required', 'email', 'max:255'],
            // only needed to create an account for someone who doesn't have one yet
            'name' => [
                Rule::requiredIf(fn () => !$this->filled('user_id') && !User::where('email', $this->input('email'))->exists()),
                'nullable', 'string', 'max:255',
            ],
            'role' => ['required', Rule::in(array_keys(Competition::$officialRoles))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => "There's no account for that email yet, so enter their name and we'll create one.",
            'email.required' => 'Pick someone from the search, or enter their email address.',
            'email.email' => "No one found. To add someone new, enter their email address.",
        ];
    }
}
