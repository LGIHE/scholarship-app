<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cohort extends Model
{
    protected $fillable = [
        'name',
        'academic_year',
        'slug',
        'scholarships_available',
        'opens_at',
        'closes_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'opens_at'               => 'datetime',
        'closes_at'              => 'datetime',
        'is_active'              => 'boolean',
        'scholarships_available' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Scope to the single active cohort.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    /**
     * Returns the currently active cohort, or null if none is set.
     */
    public static function current(): ?self
    {
        return static::active()->first();
    }

    /**
     * Returns the effective application deadline as a Carbon instance.
     * Falls back to the legacy config value if no active cohort exists.
     */
    public static function effectiveDeadline(): ?Carbon
    {
        $cohort = static::current();

        if ($cohort && $cohort->closes_at) {
            // closes_at stores the exact datetime; honour it as-is
            return $cohort->closes_at->copy()->setTime(23, 59, 59);
        }

        // Legacy fallback — uses APPLICATION_DEADLINE env / config
        $fallback = config('scholarship.application_deadline');
        return $fallback ? Carbon::parse($fallback)->setTime(23, 59, 59) : null;
    }

    /**
     * Whether the application window for this cohort has closed.
     */
    public function isDeadlinePassed(): bool
    {
        if (! $this->closes_at) {
            return false;
        }
        return now()->greaterThan($this->closes_at->copy()->setTime(23, 59, 59));
    }

    /**
     * Whether applications are currently open for this cohort.
     */
    public function isOpen(): bool
    {
        $now = now();

        $afterOpen  = $this->opens_at  ? $now->greaterThanOrEqualTo($this->opens_at)  : true;
        $beforeClose = $this->closes_at ? $now->lessThanOrEqualTo($this->closes_at->copy()->setTime(23, 59, 59)) : true;

        return $this->is_active && $afterOpen && $beforeClose;
    }

    /**
     * Ensure only one cohort is active at a time.
     * Deactivates all other cohorts before saving the active one.
     */
    public static function activateOnly(self $cohort): void
    {
        static::where('id', '!=', $cohort->id)->update(['is_active' => false]);
        $cohort->is_active = true;
        $cohort->save();
    }

    /**
     * Human-readable deadline string, e.g. "15 July 2026".
     */
    public function deadlineLabel(): ?string
    {
        return $this->closes_at?->format('j F Y');
    }
}
