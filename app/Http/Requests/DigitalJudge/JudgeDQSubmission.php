<?php

namespace App\Http\Requests\DigitalJudge;

use Illuminate\Foundation\Http\FormRequest;

class JudgeDQSubmission extends FormRequest
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
            'heat_lane' => 'required',
            'turn' => 'required',
            'length' => 'required',
            'code' => 'required',
            'details' => '',
            'name' => 'required',
            'position' => 'required',
            'seconder_name' => 'required',
            'seconder_position' => 'required',
        ];
    }
}
