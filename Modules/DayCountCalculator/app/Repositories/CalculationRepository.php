<?php

namespace Modules\DayCountCalculator\Repositories;

use Modules\DayCountCalculator\Entities\Calculation;
use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\DTOs\CalculationResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * CalculationRepository
 *
 * Data access layer for calculations
 */
class CalculationRepository
{
    /**
     * Store a new calculation
     */
    public function store(
        CalculationResult $result,
        CalculationRequest $request,
        ?string $sessionId = null,
        ?int $userId = null,
        ?string $ipAddress = null
    ): Calculation {
        return Calculation::create([
            'convention_type' => $result->conventionType,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'days_calculated' => $result->days,
            'day_count_factor' => $result->dayCountFactor,
            'principal' => $request->principal,
            'interest_rate' => $request->interestRate,
            'interest_amount' => $result->interestAmount,
            'calculation_steps' => $result->steps,
            'session_id' => $sessionId,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Get recent calculations by session ID
     */
    public function getRecentBySession(string $sessionId, int $limit = 10): Collection
    {
        return Calculation::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent calculations by user ID
     */
    public function getRecentByUser(int $userId, int $limit = 20): Collection
    {
        return Calculation::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular conventions (most used in last N days)
     */
    public function getPopularConventions(int $days = 30): array
    {
        return Calculation::select('convention_type', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('convention_type')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'convention_type')
            ->toArray();
    }

    /**
     * Get calculation statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_calculations' => Calculation::count(),
            'total_users' => Calculation::whereNotNull('user_id')->distinct('user_id')->count(),
            'total_sessions' => Calculation::whereNotNull('session_id')->distinct('session_id')->count(),
            'today' => Calculation::whereDate('created_at', today())->count(),
            'this_week' => Calculation::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => Calculation::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Find calculation by ID
     */
    public function findById(int $id): ?Calculation
    {
        return Calculation::find($id);
    }

    /**
     * Get calculations by convention type
     */
    public function getByConvention(string $conventionType, int $limit = 50): Collection
    {
        return Calculation::where('convention_type', $conventionType)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get calculations within date range
     */
    public function getByDateRange(string $startDate, string $endDate, int $limit = 100): Collection
    {
        return Calculation::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Delete old guest calculations (cleanup)
     */
    public function deleteOldGuestCalculations(int $daysOld = 90): int
    {
        return Calculation::whereNull('user_id')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Get calculations count by convention for chart
     */
    public function getConventionDistribution(): array
    {
        return Calculation::select('convention_type', DB::raw('count(*) as count'))
            ->groupBy('convention_type')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'convention_type')
            ->toArray();
    }

    /**
     * Get calculations timeline (for charts)
     */
    public function getCalculationsTimeline(int $days = 30): array
    {
        return Calculation::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
    }
}
