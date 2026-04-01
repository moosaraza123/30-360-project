<?php

namespace Modules\DayCountCalculator\DTOs;

use Carbon\Carbon;

/**
 * ComparisonResult DTO
 *
 * Immutable data transfer object for convention comparison results
 */
readonly class ComparisonResult
{
    public function __construct(
        public Carbon $startDate,
        public Carbon $endDate,
        public array $results,  // Array of CalculationResult objects
        public ?float $principal = null,
        public ?float $interestRate = null
    ) {}

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'principal' => $this->principal,
            'interest_rate' => $this->interestRate,
            'results' => array_map(
                fn(CalculationResult $result) => $result->toArray(),
                $this->results
            ),
            'statistics' => $this->getStatistics(),
        ];
    }

    /**
     * Get statistical analysis of results
     */
    public function getStatistics(): array
    {
        if (empty($this->results)) {
            return [];
        }

        $days = array_map(fn($r) => $r->days, $this->results);
        $factors = array_map(fn($r) => $r->dayCountFactor, $this->results);

        return [
            'min_days' => min($days),
            'max_days' => max($days),
            'avg_days' => array_sum($days) / count($days),
            'days_variance' => max($days) - min($days),
            'min_factor' => min($factors),
            'max_factor' => max($factors),
            'avg_factor' => array_sum($factors) / count($factors),
        ];
    }

    /**
     * Get results sorted by days
     */
    public function sortedByDays(bool $ascending = true): array
    {
        $sorted = $this->results;
        usort($sorted, fn($a, $b) => $a->days <=> $b->days);

        return $ascending ? $sorted : array_reverse($sorted);
    }

    /**
     * Get result by convention type
     */
    public function getResultByConvention(string $conventionType): ?CalculationResult
    {
        foreach ($this->results as $result) {
            if ($result->conventionType === $conventionType) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Get conventions with significant differences
     */
    public function getSignificantDifferences(int $threshold = 5): array
    {
        $differences = [];
        $stats = $this->getStatistics();

        foreach ($this->results as $result) {
            $diff = abs($result->days - $stats['avg_days']);
            if ($diff >= $threshold) {
                $differences[] = [
                    'convention' => $result->conventionType,
                    'days' => $result->days,
                    'difference' => $diff,
                ];
            }
        }

        return $differences;
    }

    /**
     * Get result count
     */
    public function getResultCount(): int
    {
        return count($this->results);
    }

    /**
     * Check if includes interest calculations
     */
    public function hasInterestCalculations(): bool
    {
        return $this->principal !== null && $this->interestRate !== null;
    }
}
