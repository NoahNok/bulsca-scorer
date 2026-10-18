<?php

namespace App\Http\Requests\SERC;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMarkingPointTemplateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('marking_point_templates', 'name')->ignore($this->route('marking_point'))],
            'settings' => ['required', 'array'],
            'settings.mode' => ['required', 'string', Rule::in(['default', 'choice'])],

            'settings.min' => ['exclude_unless:settings.mode,default', 'required_if:settings.mode,default', 'numeric'],
            'settings.max' => ['exclude_unless:settings.mode,default', 'required_if:settings.mode,default', 'numeric', 'gte:settings.min'],
            'settings.step' => ['exclude_unless:settings.mode,default', 'required_if:settings.mode,default', 'numeric', 'min:0.5'],
            'settings.use_toggle_for_half' => ['exclude_unless:settings.mode,default', 'required_if:settings.mode,default', 'boolean'],

            'settings.choice' => ['exclude_unless:settings.mode,choice', 'required_if:settings.mode,choice', 'array', 'min:1'],
            'settings.choice.*.label' => ['required_with:settings.choice', 'string', 'max:255'],
            'settings.choice.*.value' => ['required_with:settings.choice', 'numeric', 'distinct'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'settings.min' => 'minimum mark',
            'settings.max' => 'maximum mark',
            'settings.step' => 'mark step',
            'settings.use_toggle_for_half' => 'use toggle for half marks',
            'settings.choice' => 'choices',
            'settings.choice.*.label' => 'choice label',
            'settings.choice.*.value' => 'choice value',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A marking point template with this name already exists.',
            'settings.choice.*.value.distinct' => 'Each choice value must be unique.',
            'settings.choice.*.value.numeric' => 'Each choice value must be a number.',
            'settings.choice.*.label.required_with' => 'Each choice must have a label.',
            'settings.choice.*.value.required_with' => 'Each choice must have a value.',
            'settings.choice.*.label.string' => 'Each choice label must be a string.',
            'settings.choice.required_if' => 'Choices are required when mode is set to choice.',
            'settings.min.required_if' => 'Minimum mark is required when mode is set to default.',
            'settings.max.required_if' => 'Maximum mark is required when mode is set to default.',
            'settings.step.required_if' => 'Mark step is required when mode is set to default.',
            'settings.use_toggle_for_half.required_if' => 'The use toggle for half marks field is required when mode is set to default.',
        ];
    }
}
