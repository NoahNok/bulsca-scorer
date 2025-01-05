<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BrandEditCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        $comp = $this->route('comp');

        $brand = $comp->getBrand()->first();

        return $brand && $brand->isBrandRole($user, 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'when' => 'required',
            'where' => 'required',
            'lanes' => 'required',
            'isLeague' => 'required|boolean',
            'anytimepin' => 'required|boolean',
            'scoring_type' => 'required',
        ];
    }
}
