<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountInviteNewAccountRequest extends FormRequest
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
            'name' => 'required|string|min:3',
            'password' => 'required|string|min:6|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'password.confirm' => 'Your passwords do not match',
            'password.min' => 'You password must be atleast 6 characters long'
        ];
    }
}
