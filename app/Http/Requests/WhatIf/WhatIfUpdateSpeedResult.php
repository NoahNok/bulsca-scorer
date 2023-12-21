<?php

namespace App\Http\Requests\WhatIf;

use Illuminate\Foundation\Http\FormRequest;

class WhatIfUpdateSpeedResult extends FormRequest
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
            'value' => '',
            'id' => 'required',
            'type' => 'required'
        ];
    }
}
