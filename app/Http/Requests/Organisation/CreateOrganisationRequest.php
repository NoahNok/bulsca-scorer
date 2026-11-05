<?php

namespace App\Http\Requests\Organisation;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrganisationRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:1|unique:organisations',
            'logo' => 'sometimes|image|mimes:jpeg,png,webp|max:10240'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'An organisation with this name already exists.',
            'logo.image' => 'Choose a PNG, JPG or WebP image.',
            'logo.mimes' => 'Choose a PNG, JPG or WebP image.',
            'logo.max' => 'Choose an image up to 10 MB.',
        ];
    }
}
