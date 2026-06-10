<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlertRuleRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'source_type' => [
                'required',
                Rule::in([
                    'github',
                    'stripe',
                    'monitoring',
                    'custom',
                ]),
            ],

            'event_type' => [
                'required',
                'string',
                'max:255',
            ],

            'conditions' => [
                'nullable',
                'array',
            ],

            'action' => [
                'required',
                Rule::in([
                    'notify',
                    'escalate',
                    'digest',
                ]),
            ],

            'priority' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'critical',
                ]),
            ],

            'is_active' => [
                'boolean',
            ],
        ];
    }
}
