<?php

namespace Modules\DayCountCalculator\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * Subscriber Entity
 *
 * Manages email subscriptions with verification
 */
class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'verification_token',
        'email_verified_at',
        'is_active',
        'source',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'verification_token',
    ];

    /**
     * Generate verification token
     */
    public static function generateVerificationToken(): string
    {
        return Str::random(60);
    }

    /**
     * Mark email as verified
     */
    public function markAsVerified(): void
    {
        $this->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    /**
     * Check if email is verified
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Scope to get only verified subscribers
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope to get only active subscribers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get subscribers by source
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Deactivate subscription
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate subscription
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
}
