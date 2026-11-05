<?php

namespace App\Http\Requests\Result;

use App\Support\ScoringExpressions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateResultSchemaScoringSettings extends FormRequest
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
            'name' => 'nullable|sometimes|string',
            'global_variables' => 'array',
            'equation' => 'required|string',
        ];
    }

    /**
     * A badly written expression would break every page that scores with it, so it's caught here
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($error = ScoringExpressions::firstError($this->all())) {
                    $validator->errors()->add('equation', $error);
                }
            },
        ];
    }
}
