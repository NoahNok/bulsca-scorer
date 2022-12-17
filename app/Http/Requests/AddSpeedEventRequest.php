<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AddSpeedEventRequest extends FormRequest
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
            'event' => ['required', Rule::notIn(['null'])],
            'weight' => 'required|numeric',
            'record' => 'regex:/[0-9]{1,2}:[0-9]{1,2}.[0-9]{3}/'

        ];
    }

    public function messages()
    {
        return [
            'record' => 'Expected a time in the form 00:00.000',
        ];
    }
}
