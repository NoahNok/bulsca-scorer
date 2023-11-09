<?php

namespace App\Http\Requests\DigitalJudge;

use App\DigitalJudge\DigitalJudge;
use Illuminate\Foundation\Http\FormRequest;

class DQRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return DigitalJudge::isClientHeadJudge();
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
            'team' => 'required',
            'type' => 'required',
            'code' => '',
        ];
    }
}
