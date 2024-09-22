<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Brands\Brand;
use Illuminate\Validation\Rule;

class CreateBrandUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        /** @var Brand $brand */
        $brand = $this->route('brand');
        $user = Auth::user();

        return $brand->isBrandRole($user, 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'accountEmail' => 'required|email|unique:users,email',
            'accountName' => 'required|min:3|max:255',
            'accountRole' => ['required', Rule::in(['admin', 'welfare'])]
        ];
    }
}
