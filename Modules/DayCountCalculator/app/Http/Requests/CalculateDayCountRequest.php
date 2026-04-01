<?php

namespace Modules\DayCountCalculator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CalculateDayCountRequest
 *
 * Validates day count calculation input
 */
class CalculateDayCountRequest extends FormRequest
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
            'convention_type' => [
                'required',
                'string',
                'in:30/360 US,30/360 Bond Basis,30E/360,30E/360 ISDA,Actual/365 Fixed,Actual/360,Actual/364,Actual/Actual,Actual/Actual ISDA'
            ],
            'start_date' => [
                'required',
                'date',
                'before_or_equal:end_date'
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date'
            ],
            'principal' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.99'
            ],
            'interest_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                'required_with:principal'
            ],
            'apply_eom_adjustment' => [
                'nullable',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'convention_type.required' => 'Please select a day count convention.',
            'convention_type.in' => 'Invalid day count convention selected.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'principal.numeric' => 'Principal must be a number.',
            'principal.min' => 'Principal must be greater than or equal to 0.',
            'principal.max' => 'Principal amount is too large.',
            'interest_rate.numeric' => 'Interest rate must be a number.',
            'interest_rate.min' => 'Interest rate must be greater than or equal to 0.',
            'interest_rate.max' => 'Interest rate cannot exceed 100%.',
            'interest_rate.required_with' => 'Interest rate is required when principal is provided.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'convention_type' => 'day count convention',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'principal' => 'principal amount',
            'interest_rate' => 'interest rate',
            'apply_eom_adjustment' => 'end-of-month adjustment',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'apply_eom_adjustment' => $this->boolean('apply_eom_adjustment', false),
        ]);
    }
}
