<?php

namespace Modules\DayCountCalculator\Repositories;

use Modules\DayCountCalculator\Entities\Subscriber;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * SubscriberRepository
 *
 * Data access layer for subscribers
 */
class SubscriberRepository
{
    /**
     * Create a new subscriber
     */
    public function create(string $email, string $source = 'calculator'): Subscriber
    {
        $token = Subscriber::generateVerificationToken();

        return Subscriber::create([
            'email' => $email,
            'verification_token' => $token,
            'source' => $source,
            'is_active' => true,
        ]);
    }

    /**
     * Find subscriber by email
     */
    public function findByEmail(string $email): ?Subscriber
    {
        return Subscriber::where('email', $email)->first();
    }

    /**
     * Find subscriber by verification token
     */
    public function findByToken(string $token): ?Subscriber
    {
        return Subscriber::where('verification_token', $token)->first();
    }

    /**
     * Check if email is already subscribed
     */
    public function isSubscribed(string $email): bool
    {
        return Subscriber::where('email', $email)->exists();
    }

    /**
     * Get all active verified subscribers
     */
    public function getActiveVerified(): Collection
    {
        return Subscriber::active()
            ->verified()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get subscribers by source
     */
    public function getBySource(string $source): Collection
    {
        return Subscriber::where('source', $source)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent subscribers
     */
    public function getRecent(int $limit = 20): Collection
    {
        return Subscriber::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get subscriber statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Subscriber::count(),
            'verified' => Subscriber::verified()->count(),
            'unverified' => Subscriber::whereNull('email_verified_at')->count(),
            'active' => Subscriber::active()->count(),
            'inactive' => Subscriber::where('is_active', false)->count(),
            'today' => Subscriber::whereDate('created_at', today())->count(),
            'this_week' => Subscriber::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => Subscriber::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get subscribers growth timeline
     */
    public function getGrowthTimeline(int $days = 30): array
    {
        return Subscriber::select(
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

    /**
     * Get source distribution
     */
    public function getSourceDistribution(): array
    {
        return Subscriber::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'source')
            ->toArray();
    }

    /**
     * Mark subscriber as verified
     */
    public function markAsVerified(Subscriber $subscriber): void
    {
        $subscriber->markAsVerified();
    }

    /**
     * Deactivate subscriber
     */
    public function deactivate(Subscriber $subscriber): void
    {
        $subscriber->deactivate();
    }

    /**
     * Activate subscriber
     */
    public function activate(Subscriber $subscriber): void
    {
        $subscriber->activate();
    }

    /**
     * Delete unverified subscribers older than N days
     */
    public function deleteUnverifiedOld(int $daysOld = 30): int
    {
        return Subscriber::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Search subscribers
     */
    public function search(string $term): Collection
    {
        return Subscriber::where('email', 'like', "%{$term}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get paginated subscribers
     */
    public function paginate(int $perPage = 15)
    {
        return Subscriber::orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
