<?php

namespace App\Http\Requests\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmResultsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return DigitalJudge::isClientHeadJudge($this->route('competition'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'check_conf' => 'accepted',
        ];
    }
}
