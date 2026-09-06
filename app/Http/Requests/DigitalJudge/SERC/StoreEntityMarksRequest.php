<?php

namespace App\Http\Requests\DigitalJudge\SERC;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEntityMarksRequest extends FormRequest
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
     * The paylaod on the frontend looks like:
     * {
     *      judge_id: number;
     *      marks: {
     *          marking_point_id: number;
     *          mark: number;
     *      }[];
     *      notes?: string;
     *  }[]
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        // judge_id => marking_point_id => mark

        return [
            'marks' => 'array',
            'marks.*' => 'array',
            'marks.*.*' => 'numeric',
            'notes' => 'array',
            'notes.*' => 'string|nullable',

        ];
    }
}
