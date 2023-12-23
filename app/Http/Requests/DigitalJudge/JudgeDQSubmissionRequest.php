<?php

namespace App\Http\Requests\DigitalJudge;

use Illuminate\Foundation\Http\FormRequest;

class JudgeDQSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'event' => 'required',
            'heat_lane' => '',
            'turn' => '',
            'length' => '',
            'code' => 'required',
            'details' => '',
            'name' => 'required',
            'position' => 'required',
            'seconder_name' => '',
            'seconder_position' => '',
        ];
    }
}
