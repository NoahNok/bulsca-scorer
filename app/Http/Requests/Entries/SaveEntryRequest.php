<?php

namespace App\Http\Requests\Entries;

use App\Services\CompetitionEntries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A club, team or competitor from the Entries & Leagues page. Each takes what applies to it: a club only
 * its name, a team its club, a competitor its team, and both leagues and seed times (by speed event id).
 */
class SaveEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled by route middleware
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['name' => trim((string) $this->input('name'))]);

        // No leagues is sent as none at all, but drop blanks in case a form sends an empty one
        if (is_array($this->input('leagues'))) {
            $this->merge(['leagues' => array_values(array_filter($this->input('leagues'), 'filled'))]);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $comp = $this->route('comp')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'club' => ['nullable', 'integer', Rule::exists('clubs', 'id')->where('competition', $comp)],
            'team' => ['nullable', 'integer', Rule::exists('competition_teams', 'id')->where('competition', $comp)],
            'leagues' => ['nullable', 'array'],
            'leagues.*' => ['integer', Rule::exists('leagues', 'id')->where('competition', $comp)],
            'seeds' => ['nullable', 'array'],
            'seeds.*' => ['nullable', 'string', 'regex:' . CompetitionEntries::SEED_FORMAT],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Enter a name.',
            'seeds.*.regex' => 'Enter a time like 01:23.456, or leave it empty.',
            'club.exists' => "That club isn't in this competition.",
            'team.exists' => "That team isn't in this competition.",
            'leagues.*.exists' => "That league isn't in this competition.",
        ];
    }
}
