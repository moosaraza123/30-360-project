<?php

namespace Modules\DayCountCalculator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SubscribeEmailRequest
 *
 * Validates email subscription input
 */
class SubscribeEmailRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'source' => [
                'nullable',
                'string',
                'in:calculator,comparison,footer,popup,exit_intent',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already subscribed. Please check your inbox for a verification email.',
            'email.max' => 'Email address is too long.',
            'source.in' => 'Invalid subscription source.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'email address',
            'source' => 'subscription source',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'source' => $this->input('source', 'calculator'),
        ]);
    }
}
