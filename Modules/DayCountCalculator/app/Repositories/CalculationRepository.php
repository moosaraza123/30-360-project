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
        $todayCount = Calculation::whereDate('created_at', today())->count();
        $yesterdayCount = Calculation::whereDate('created_at', today()->subDay())->count();
        $thisWeek = Calculation::where('created_at', '>=', now()->startOfWeek())->count();
        $lastWeek = Calculation::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek(),
        ])->count();

        return [
            'total_calculations' => Calculation::count(),
            'auth_calculations' => Calculation::whereNotNull('user_id')->count(),
            'guest_calculations' => Calculation::whereNull('user_id')->count(),
            'unique_users' => Calculation::whereNotNull('user_id')->distinct('user_id')->count(),
            'unique_sessions' => Calculation::whereNull('user_id')->whereNotNull('session_id')->distinct('session_id')->count(),
            // kept for backward compat
            'total_users' => Calculation::whereNotNull('user_id')->distinct('user_id')->count(),
            'total_sessions' => Calculation::whereNull('user_id')->whereNotNull('session_id')->distinct('session_id')->count(),
            'today' => $todayCount,
            'yesterday' => $yesterdayCount,
            'today_change' => $yesterdayCount > 0 ? round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100) : null,
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'week_change' => $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100) : null,
            'this_month' => Calculation::where('created_at', '>=', now()->startOfMonth())->count(),
            'avg_per_day_30d' => (int) round(
                Calculation::where('created_at', '>=', now()->subDays(30))->count() / 30
            ),
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
