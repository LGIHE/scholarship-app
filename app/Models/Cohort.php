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
        'display_closes_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'opens_at'          => 'datetime',
        'closes_at'         => 'datetime',
        'display_closes_at' => 'date',        // date-only — no time component needed
        'is_active'              => 'boolean',
        'scholarships_available' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

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
     * Returns the effective submission deadline as a Carbon instance.
     * This is ALWAYS driven by closes_at — it is never affected by display_closes_at.
     * Falls back to the legacy config value if no active cohort exists.
     */
    public static function effectiveDeadline(): ?Carbon
    {
        $cohort = static::current();

        if ($cohort && $cohort->closes_at) {
            return $cohort->closes_at->copy()->setTime(23, 59, 59);
        }

        // Legacy fallback — uses APPLICATION_DEADLINE env / config
        $fallback = config('scholarship.application_deadline');
        return $fallback ? Carbon::parse($fallback)->setTime(23, 59, 59) : null;
    }

    /**
     * Whether the submission deadline for this cohort has passed.
     * Based on closes_at only — display_closes_at has no effect here.
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
     * Still governed by closes_at (real cutoff).
     */
    public function isOpen(): bool
    {
        $now = now();

        $afterOpen   = $this->opens_at  ? $now->greaterThanOrEqualTo($this->opens_at)                          : true;
        $beforeClose = $this->closes_at ? $now->lessThanOrEqualTo($this->closes_at->copy()->setTime(23, 59, 59)) : true;

        return $this->is_active && $afterOpen && $beforeClose;
    }

    /**
     * Ensure only one cohort is active at a time.
     */
    public static function activateOnly(self $cohort): void
    {
        static::where('id', '!=', $cohort->id)->update(['is_active' => false]);
        $cohort->is_active = true;
        $cohort->save();
    }

    /**
     * Returns the date that should be SHOWN to the public as the deadline.
     *
     * - If display_closes_at is set, that date is returned.
     * - Otherwise falls back to closes_at.
     *
     * This is purely cosmetic — it never affects submission enforcement.
     */
    public function publicDeadline(): ?Carbon
    {
        if ($this->display_closes_at) {
            return $this->display_closes_at->copy();   // already cast to Carbon date
        }
        return $this->closes_at ? $this->closes_at->copy() : null;
    }

    /**
     * Human-readable deadline string shown to the public, e.g. "15 July 2026".
     * Uses display_closes_at when set, otherwise closes_at.
     */
    public function deadlineLabel(): ?string
    {
        return $this->publicDeadline()?->format('j F Y');
    }

    /**
     * The display deadline as a plain date string (Y-m-d) for Inertia props.
     */
    public function publicDeadlineDateString(): ?string
    {
        return $this->publicDeadline()?->toDateString();
    }
}
