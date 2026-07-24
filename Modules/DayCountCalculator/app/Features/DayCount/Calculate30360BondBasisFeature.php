<?php

namespace Modules\DayCountCalculator\Features\DayCount;

use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\DTOs\CalculationResult;

/**
 * Calculate 30/360 Bond Basis Feature
 *
 * This is an alias for 30/360 US
 * Also known as: 30U/360, Bond Basis
 *
 * Uses the same calculation as Calculate30360USFeature
 */
class Calculate30360BondBasisFeature
{
    private Calculate30360USFeature $calculator;

    public function __construct()
    {
        $this->calculator = new Calculate30360USFeature;
    }

    /**
     * Execute the calculation (delegates to 30/360 US)
     */
    public function execute(CalculationRequest $request): CalculationResult
    {
        $result = $this->calculator->execute($request);

        // Return with Bond Basis naming
        return new CalculationResult(
            days: $result->days,
            dayCountFactor: $result->dayCountFactor,
            interestAmount: $result->interestAmount,
            steps: $result->steps,
            conventionType: '30/360 Bond Basis'
        );
    }
}
