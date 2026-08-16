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
        return [
            '*.judge_id' => ['required', 'integer', 'exists:serc_judges,id'],
            '*.marks' => ['required', 'array'],
            '*.marks.*.marking_point_id' => ['required', 'integer', 'exists:serc_marking_points,id'],
            '*.marks.*.mark' => ['required', 'numeric', 'min:0', 'max:10'],
            '*.notes' => ['nullable', 'string'],
        ];
    }
}
