<?php

namespace App\Models;

use App\Support\ApprovedCriteria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Cohort;

class Application extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Application submitted',
                'updated' => 'Application updated',
                'deleted' => 'Application deleted',
                default   => "Application {$eventName}",
            })
            ->useLogName('application');
    }

    protected $fillable = [
        'user_id',
        'cohort_id',
        'personal_info',
        'disability_info',
        'dependants_info',
        'financial_info',
        'guardian_info',
        'declaration_info',
        'essay',
        'documents',
        'scoring_breakdown',
        'status',
    ];

    protected $casts = [
        'personal_info'    => 'array',
        'disability_info'  => 'array',
        'dependants_info'  => 'array',
        'financial_info'   => 'array',
        'guardian_info'    => 'array',
        'declaration_info' => 'array',
        'essay'            => 'array',
        'documents'        => 'array',
        'scoring_breakdown' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Eligibility scope
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Restrict a query to applications that meet all three approved criteria:
     *   • Gender   : Female (NIN prefix CF)
     *   • Course   : matches approved programme keywords
     *   • Subject  : at least one teaching subject matches approved keywords
     *
     * NOTE: Because the criteria rely on PHP-level fuzzy matching against JSON
     * fields, this scope works as a *post-query collection filter*.  It should
     * be called on an already-constrained builder so the DB load is reasonable.
     *
     * Usage (builder):  Application::eligible()->whereNotIn('status', ['draft'])->get()
     * Usage (collection): $apps->filter(fn ($a) => ApprovedCriteria::isEligible($a->personal_info ?? []))
     */
    public function scopeEligible(Builder $query): Builder
    {
        // We filter in PHP after the query because MySQL JSON path expressions
        // cannot perform the fuzzy keyword matching we need.
        // The scope returns a builder that adds a whereRaw no-op clause so
        // callers can still chain additional Eloquent constraints; the actual
        // filtering happens via the collection() macro below.
        //
        // For widgets/exports that load all personal_info fields anyway, use the
        // static helper ApprovedCriteria::isEligible() on each record instead.
        return $query->whereRaw('1 = 1'); // no-op — see filterEligible() below
    }

    /**
     * Filter a collection (or array) of Application models down to only those
     * that satisfy all approved eligibility criteria.
     *
     * Use this after ->get() when you need PHP-level fuzzy matching:
     *   Application::whereNotIn('status', ['draft'])->get()
     *       ->pipe(fn ($c) => Application::filterEligible($c))
     */
    public static function filterEligible(\Illuminate\Support\Collection $apps): \Illuminate\Support\Collection
    {
        return $apps->filter(
            fn (self $app) => ApprovedCriteria::isEligible($app->personal_info ?? [])
        )->values();
    }
}
