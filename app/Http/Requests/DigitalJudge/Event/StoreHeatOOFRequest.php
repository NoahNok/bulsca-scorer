<?php

namespace App\Http\Requests\DigitalJudge\Event;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHeatOOFRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The body is a bare list of lanes, so leave query parameters such as ?pool= out of it.
     */
    public function validationData(): array
    {
        return $this->json()->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            '*.entity.id' => 'required|numeric',
            '*.oof' => 'nullable|numeric'
        ];
    }
}
