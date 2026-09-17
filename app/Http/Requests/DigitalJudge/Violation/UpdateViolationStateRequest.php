<?php

namespace App\Http\Requests\DigitalJudge\Violation;

use App\DigitalJudge\DigitalJudge;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateViolationStateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $competition = $this->route('competition');

        return DigitalJudge::isClientHeadJudge($competition);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'state' => ['required', Rule::in(ViolationSubmission::$STATES)]
        ];
    }
}
