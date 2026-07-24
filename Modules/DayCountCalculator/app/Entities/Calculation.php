<?php

namespace Modules\DayCountCalculator\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Calculation Entity
 *
 * Stores day count convention calculation results with step-by-step breakdown
 */
class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'convention_type',
        'start_date',
        'end_date',
        'days_calculated',
        'day_count_factor',
        'principal',
        'interest_rate',
        'interest_amount',
        'calculation_steps',
        'session_id',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_calculated' => 'integer',
        'day_count_factor' => 'decimal:10',
        'principal' => 'decimal:2',
        'interest_rate' => 'decimal:6',
        'interest_amount' => 'decimal:2',
        'calculation_steps' => 'array',
    ];

    /**
     * Get the user that owns the calculation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get saved calculations for this calculation
     */
    public function savedCalculations(): HasMany
    {
        return $this->hasMany(SavedCalculation::class);
    }

    /**
     * Scope to filter by convention type
     */
    public function scopeByConvention($query, string $convention)
    {
        return $query->where('convention_type', $convention);
    }

    /**
     * Scope to filter by session
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope to get recent calculations
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get human-readable convention name
     */
    public function getConventionNameAttribute(): string
    {
        return $this->convention_type;
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute(): string
    {
        return $this->start_date->format('M d, Y').' - '.$this->end_date->format('M d, Y');
    }

    /**
     * Check if calculation belongs to a user
     */
    public function isOwnedBy(?User $user): bool
    {
        return $user && $this->user_id === $user->id;
    }
}
