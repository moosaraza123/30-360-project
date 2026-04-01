<?php

namespace Modules\DayCountCalculator\DTOs;

/**
 * CalculationResult DTO
 *
 * Immutable data transfer object for calculation results
 */
readonly class CalculationResult
{
    public function __construct(
        public int $days,
        public float $dayCountFactor,
        public ?float $interestAmount,
        public array $steps,
        public string $conventionType
    ) {}

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'days' => $this->days,
            'day_count_factor' => $this->dayCountFactor,
            'interest_amount' => $this->interestAmount,
            'steps' => $this->steps,
            'convention_type' => $this->conventionType,
        ];
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            days: $data['days'],
            dayCountFactor: $data['day_count_factor'],
            interestAmount: $data['interest_amount'] ?? null,
            steps: $data['steps'] ?? [],
            conventionType: $data['convention_type']
        );
    }

    /**
     * Get formatted day count factor
     */
    public function getFormattedFactor(int $decimals = 10): string
    {
        return number_format($this->dayCountFactor, $decimals, '.', '');
    }

    /**
     * Get formatted interest amount
     */
    public function getFormattedInterest(): ?string
    {
        return $this->interestAmount !== null
            ? '$' . number_format($this->interestAmount, 2)
            : null;
    }

    /**
     * Check if calculation includes interest
     */
    public function hasInterestCalculation(): bool
    {
        return $this->interestAmount !== null;
    }

    /**
     * Get applied steps only
     */
    public function getAppliedSteps(): array
    {
        return array_filter($this->steps, fn($step) => $step['applied'] ?? false);
    }

    /**
     * Get step count
     */
    public function getStepCount(): int
    {
        return count($this->steps);
    }
}
