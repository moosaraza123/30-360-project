<?php

namespace Modules\DayCountCalculator\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\DayCountCalculator\Entities\SavedCalculation;
use Modules\DayCountCalculator\Repositories\CalculationRepository;
use Modules\DayCountCalculator\Repositories\SubscriberRepository;

/**
 * AdminController
 *
 * Handles admin analytics and management
 */
class AdminController extends Controller
{
    public function __construct(
        private CalculationRepository $calculationRepository,
        private SubscriberRepository $subscriberRepository
    ) {
        // Add admin middleware when implemented
        // $this->middleware(['auth', 'admin']);
    }

    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $calculationStats = $this->calculationRepository->getStatistics();
        $subscriberStats = $this->subscriberRepository->getStatistics();
        $popularConventions = $this->calculationRepository->getPopularConventions(30);
        $conventionDistribution = $this->calculationRepository->getConventionDistribution();
        $calculationsTimeline = $this->calculationRepository->getCalculationsTimeline(30);
        $subscriberGrowth = $this->subscriberRepository->getGrowthTimeline(30);

        $userStats = $this->getUserStats();

        return view('daycountcalculator::admin.dashboard', [
            'calculationStats' => $calculationStats,
            'subscriberStats' => $subscriberStats,
            'userStats' => $userStats,
            'popularConventions' => $popularConventions,
            'conventionDistribution' => $conventionDistribution,
            'calculationsTimeline' => $calculationsTimeline,
            'subscriberGrowth' => $subscriberGrowth,
        ]);
    }

    /**
     * Gather registered user statistics
     */
    private function getUserStats(): array
    {
        $total = User::where('role', User::ROLE_USER)->count();
        $totalToday = User::where('role', User::ROLE_USER)->whereDate('created_at', today())->count();
        $totalThisWeek = User::where('role', User::ROLE_USER)->where('created_at', '>=', now()->startOfWeek())->count();
        $totalThisMonth = User::where('role', User::ROLE_USER)->where('created_at', '>=', now()->startOfMonth())->count();
        $lastWeek = User::where('role', User::ROLE_USER)->whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek(),
        ])->count();

        return [
            'total' => $total,
            'today' => $totalToday,
            'this_week' => $totalThisWeek,
            'this_month' => $totalThisMonth,
            'week_change' => $lastWeek > 0 ? round((($totalThisWeek - $lastWeek) / $lastWeek) * 100) : null,
            'saved_calculations' => SavedCalculation::count(),
        ];
    }

    /**
     * Display calculations analytics
     */
    public function calculations()
    {
        $stats = $this->calculationRepository->getStatistics();
        $popularConventions = $this->calculationRepository->getPopularConventions(30);
        $timeline = $this->calculationRepository->getCalculationsTimeline(90);
        $distribution = $this->calculationRepository->getConventionDistribution();

        return view('daycountcalculator::admin.calculations', [
            'stats' => $stats,
            'popularConventions' => $popularConventions,
            'timeline' => $timeline,
            'distribution' => $distribution,
        ]);
    }

    /**
     * Display subscribers management
     */
    public function subscribers()
    {
        $stats = $this->subscriberRepository->getStatistics();
        $recent = $this->subscriberRepository->getRecent(50);
        $growth = $this->subscriberRepository->getGrowthTimeline(90);
        $sourceDistribution = $this->subscriberRepository->getSourceDistribution();

        return view('daycountcalculator::admin.subscribers', [
            'stats' => $stats,
            'recent' => $recent,
            'growth' => $growth,
            'sourceDistribution' => $sourceDistribution,
        ]);
    }

    /**
     * Export analytics data
     */
    public function export(string $type)
    {
        if (!in_array($type, ['calculations', 'subscribers'])) {
            abort(400, 'Invalid export type');
        }

        if ($type === 'calculations') {
            return $this->exportCalculations();
        }

        return $this->exportSubscribers();
    }

    /**
     * Export calculations data to CSV
     */
    private function exportCalculations()
    {
        $filename = 'calculations_export_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $stats = $this->calculationRepository->getStatistics();
        $distribution = $this->calculationRepository->getConventionDistribution();

        $callback = function () use ($stats, $distribution) {
            $file = fopen('php://output', 'w');

            // Statistics section
            fputcsv($file, ['Calculation Statistics']);
            fputcsv($file, ['Total Calculations', $stats['total_calculations']]);
            fputcsv($file, ['Unique Users', $stats['total_users']]);
            fputcsv($file, ['Unique Sessions', $stats['total_sessions']]);
            fputcsv($file, ['Today', $stats['today']]);
            fputcsv($file, ['This Week', $stats['this_week']]);
            fputcsv($file, ['This Month', $stats['this_month']]);
            fputcsv($file, []);

            // Convention distribution
            fputcsv($file, ['Convention Distribution']);
            fputcsv($file, ['Convention', 'Count']);
            foreach ($distribution as $convention => $count) {
                fputcsv($file, [$convention, $count]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export subscribers data to CSV
     */
    private function exportSubscribers()
    {
        $filename = 'subscribers_export_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $stats = $this->subscriberRepository->getStatistics();
        $sourceDistribution = $this->subscriberRepository->getSourceDistribution();

        $callback = function () use ($stats, $sourceDistribution) {
            $file = fopen('php://output', 'w');

            // Statistics section
            fputcsv($file, ['Subscriber Statistics']);
            fputcsv($file, ['Total Subscribers', $stats['total']]);
            fputcsv($file, ['Verified', $stats['verified']]);
            fputcsv($file, ['Unverified', $stats['unverified']]);
            fputcsv($file, ['Active', $stats['active']]);
            fputcsv($file, ['Inactive', $stats['inactive']]);
            fputcsv($file, ['Today', $stats['today']]);
            fputcsv($file, ['This Week', $stats['this_week']]);
            fputcsv($file, ['This Month', $stats['this_month']]);
            fputcsv($file, []);

            // Source distribution
            fputcsv($file, ['Source Distribution']);
            fputcsv($file, ['Source', 'Count']);
            foreach ($sourceDistribution as $source => $count) {
                fputcsv($file, [$source, $count]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
