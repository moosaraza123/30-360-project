<?php

namespace Modules\DayCountCalculator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CompareConventionsRequest
 *
 * Validates comparison tool input
 */
class CompareConventionsRequest extends FormRequest
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
            'conventions' => [
                'nullable',
                'array',
                'min:2'
            ],
            'conventions.*' => [
                'string',
                'in:30/360 US,30/360 Bond Basis,30E/360,30E/360 ISDA,Actual/365 Fixed,Actual/360,Actual/364,Actual/Actual,Actual/Actual ISDA'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
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
            'conventions.array' => 'Conventions must be an array.',
            'conventions.min' => 'Please select at least 2 conventions to compare.',
            'conventions.*.in' => 'Invalid convention selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'start_date' => 'start date',
            'end_date' => 'end date',
            'principal' => 'principal amount',
            'interest_rate' => 'interest rate',
            'conventions' => 'conventions',
        ];
    }

    /**
     * Get the selected conventions or all if none selected.
     */
    public function getSelectedConventions(): array
    {
        if ($this->has('conventions') && is_array($this->input('conventions'))) {
            return $this->input('conventions');
        }

        // Return all conventions if none selected
        return [
            '30/360 US',
            '30/360 Bond Basis',
            '30E/360',
            '30E/360 ISDA',
            'Actual/365 Fixed',
            'Actual/360',
            'Actual/364',
            'Actual/Actual',
            'Actual/Actual ISDA',
        ];
    }
}
