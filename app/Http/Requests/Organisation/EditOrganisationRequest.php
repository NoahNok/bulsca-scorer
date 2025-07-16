<?php

namespace App\Http\Requests\Organisation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditOrganisationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'logo' => 'sometimes|image',
        ];

        $name = $this->input('name');

        if ($name && $name !== $this->route('organisation')->name) {
            $rules['name'] = [
                'required',
                'string',
                'min:1',
                Rule::unique('organisations')->ignore($this->route('organisation')->id)
            ];
        } else {
            $rules['name'] = [
                'required',
                'string',
                'min:1'
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.unique' => 'An organisation with this name already exists.'
        ];
    }
}
