<?php

namespace Modules\DayCountCalculator\DTOs;

use Carbon\Carbon;

/**
 * CalculationRequest DTO
 *
 * Immutable data transfer object for calculation requests
 */
readonly class CalculationRequest
{
    public function __construct(
        public string $conventionType,
        public Carbon $startDate,
        public Carbon $endDate,
        public ?float $principal = null,
        public ?float $interestRate = null,
        public bool $applyEomAdjustment = false
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            conventionType: $data['convention_type'],
            startDate: Carbon::parse($data['start_date']),
            endDate: Carbon::parse($data['end_date']),
            principal: isset($data['principal']) ? (float) $data['principal'] : null,
            interestRate: isset($data['interest_rate']) ? (float) $data['interest_rate'] : null,
            applyEomAdjustment: $data['apply_eom_adjustment'] ?? false
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'convention_type' => $this->conventionType,
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'principal' => $this->principal,
            'interest_rate' => $this->interestRate,
            'apply_eom_adjustment' => $this->applyEomAdjustment,
        ];
    }

    /**
     * Check if interest calculation is requested
     */
    public function hasInterestCalculation(): bool
    {
        return $this->principal !== null && $this->interestRate !== null;
    }

    /**
     * Validate date order
     */
    public function isValidDateRange(): bool
    {
        return $this->endDate->greaterThanOrEqualTo($this->startDate);
    }
}
