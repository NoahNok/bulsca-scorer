<?php

namespace App\Http\Requests\DigitalJudge;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmJudgeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Judge must belong to the SERC in the route
            'judge' => ['required', Rule::exists('serc_judges', 'id')->where('serc', $this->route('serc')?->id)]
        ];
    }
}
