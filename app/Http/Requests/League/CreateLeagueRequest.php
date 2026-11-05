<?php

namespace App\Http\Requests\League;

use App\Models\League;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Adding or renaming a league. Its name has to be new to the competition, ignoring case, so entries
 * can't be put in two leagues that look the same.
 */
class CreateLeagueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['name' => trim((string) $this->input('name'))]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', function (string $attribute, mixed $value, Closure $fail) {
                $renaming = $this->route('league');

                $taken = League::where('competition', $this->route('comp')->id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($value)])
                    ->when($renaming instanceof League, fn ($query) => $query->whereKeyNot($renaming->id))
                    ->exists();

                if ($taken) {
                    $fail('This competition already has a league called that.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Enter a name.',
        ];
    }
}
