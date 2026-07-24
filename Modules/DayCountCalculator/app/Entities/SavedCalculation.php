<?php

namespace Modules\DayCountCalculator\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SavedCalculation Entity
 *
 * Allows authenticated users to save calculations with custom names and notes
 */
class SavedCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calculation_id',
        'name',
        'notes',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    /**
     * Get the user that owns the saved calculation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the calculation
     */
    public function calculation(): BelongsTo
    {
        return $this->belongsTo(Calculation::class);
    }

    /**
     * Scope to get favorites
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to search by name
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('notes', 'like', "%{$term}%");
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(): void
    {
        $this->update(['is_favorite' => ! $this->is_favorite]);
    }

    /**
     * Mark as favorite
     */
    public function markAsFavorite(): void
    {
        $this->update(['is_favorite' => true]);
    }

    /**
     * Unmark as favorite
     */
    public function unmarkAsFavorite(): void
    {
        $this->update(['is_favorite' => false]);
    }

    /**
     * Check if owned by user
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
