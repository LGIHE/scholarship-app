<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;

/**
 * Normalises free-text teaching subject values in personal_info to clean
 * canonical names so they are recognised by ApprovedCriteria and analytics.
 *
 * Affected fields (both are processed):
 *   personal_info → teaching_subjects_1
 *   personal_info → teaching_subjects_2
 *
 * Usage:
 *   php artisan app:normalise-subjects           # dry run (shows what would change)
 *   php artisan app:normalise-subjects --apply   # persist the changes
 */
class NormaliseSubjects extends Command
{
    protected $signature   = 'app:normalise-subjects {--apply : Persist the changes to the database}';
    protected $description = 'Normalise free-text teaching subject names to approved canonical values';

    // ─────────────────────────────────────────────────────────────────────────
    // Canonical subject names — exactly as they should be stored
    // ─────────────────────────────────────────────────────────────────────────

    public const CANONICAL = [
        'Biology',
        'Chemistry',
        'Physics',
        'Mathematics',
        'Agriculture',
        'Computer Studies',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Keyword → canonical mapping (lowercase keywords, order matters)
    //
    // Rules:
    //   - Keys are lowercase substrings to match against the normalised raw value
    //   - More specific patterns come before generic ones
    //   - Diacritic-stripped versions are also covered (see normalise() method)
    // ─────────────────────────────────────────────────────────────────────────

    public const KEYWORD_MAP = [

        // ── Biology ──────────────────────────────────────────────────────────
        'bioliogy'          => 'Biology',   // specific typo first
        'bilogy'            => 'Biology',
        'biolog'            => 'Biology',   // covers "biology", "biologgy", etc.
        'biolgy'            => 'Biology',

        // ── Chemistry ────────────────────────────────────────────────────────
        'chemestry'         => 'Chemistry', // common misspelling
        'chrmistry'         => 'Chemistry', // missing 'e'
        'chem'              => 'Chemistry', // short form & prefix for "chemistry"

        // ── Physics ──────────────────────────────────────────────────────────
        'physic'            => 'Physics',   // covers "physics", "physic"

        // ── Mathematics ──────────────────────────────────────────────────────
        'mathemat'         => 'Mathematics', // covers "mathematics", "mathematical"
        'maths'             => 'Mathematics',
        'math'              => 'Mathematics', // keep after 'maths' (both covered by 'math')

        // ── Agriculture ──────────────────────────────────────────────────────
        // "Double main" is an A-level combination code meaning two agriculture
        // subjects are taken — treat it as Agriculture.
        'double main'       => 'Agriculture',
        'agricult'          => 'Agriculture', // covers "agriculture", "agricultural science"
        'agric'             => 'Agriculture', // short form used in Ugandan schools

        // ── Computer Studies / ICT ────────────────────────────────────────────
        // Specific multi-word patterns before bare single keywords
        'information and communication technology' => 'Computer Studies',
        'information communication technology'     => 'Computer Studies',
        'information computer technology'          => 'Computer Studies',
        'computer/cet'      => 'Computer Studies', // slash variant
        'computers studies' => 'Computer Studies', // plural typo
        'computer studies'  => 'Computer Studies',
        'computer science'  => 'Computer Studies',
        'computer'          => 'Computer Studies', // bare "computer" / "COMPUTER"
        'ict'               => 'Computer Studies',
        'information tech'  => 'Computer Studies',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Command entry point
    // ─────────────────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $apply   = $this->option('apply');
        $changed = 0;
        $skipped = 0;
        $unknown = 0;

        $this->info($apply
            ? '⚡ Running in APPLY mode — changes will be saved.'
            : '🔍 Dry run — no changes will be saved. Pass --apply to persist.');
        $this->newLine();

        Application::whereNotNull('personal_info')
            ->chunkById(100, function ($apps) use (&$changed, &$skipped, &$unknown, $apply) {
                foreach ($apps as $app) {
                    $info  = $app->personal_info ?? [];
                    $dirty = false;

                    foreach (['teaching_subjects_1', 'teaching_subjects_2'] as $field) {
                        $raw = trim($info[$field] ?? '');

                        if ($raw === '') {
                            $skipped++;
                            continue;
                        }

                        // Already one of the canonical values — nothing to do
                        if (in_array($raw, self::CANONICAL, true)) {
                            $skipped++;
                            continue;
                        }

                        $normalised = self::normalise($raw);

                        if ($normalised === null) {
                            $this->line(
                                "  <comment>[ID {$app->id}][{$field}] No match:</comment> {$raw}"
                            );
                            $unknown++;
                            continue;
                        }

                        $this->line(
                            "  <info>[ID {$app->id}][{$field}]</info> \"{$raw}\" → \"{$normalised}\""
                        );

                        if ($apply) {
                            $info[$field] = $normalised;
                            $dirty        = true;
                        }

                        $changed++;
                    }

                    if ($apply && $dirty) {
                        $app->update(['personal_info' => $info]);
                    }
                }
            });

        $this->newLine();
        $this->info('Summary:');
        $this->table(['Action', 'Count'], [
            [$apply ? 'Fields updated' : 'Fields that would update', $changed],
            ['Already canonical / blank', $skipped],
            ['Could not match (manual review needed)', $unknown],
        ]);

        if (! $apply && $changed > 0) {
            $this->newLine();
            $this->warn('Run with --apply to save changes.');
        }

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Static helper — reusable by other classes (e.g. a future Filament page)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Map a raw subject string to its canonical name.
     *
     * Strategy:
     *   1. Strip diacritics (ċhëmïstrÿ → chemistry, mäthëmätïċs → mathematics)
     *   2. Lowercase + trim
     *   3. Keyword map lookup (longest/most-specific match wins via array order)
     *
     * Returns null when no match is found.
     */
    public static function normalise(string $raw): ?string
    {
        if (trim($raw) === '') {
            return null;
        }

        // 1. Strip diacritics so characters like ä ë ï ÿ ċ resolve to ASCII
        $stripped = self::stripDiacritics($raw);

        // 2. Lowercase for case-insensitive matching
        $lower = mb_strtolower(trim($stripped));

        // 3. Walk the keyword map
        foreach (self::KEYWORD_MAP as $keyword => $canonical) {
            if (str_contains($lower, $keyword)) {
                return $canonical;
            }
        }

        return null;
    }

    /**
     * Remove diacritical marks from a UTF-8 string.
     *
     * Uses PHP's intl transliterator when available (ext-intl), otherwise
     * falls back to iconv approximation.
     */
    private static function stripDiacritics(string $input): string
    {
        // Preferred: intl transliterator (handles ċ, ÿ, ë, ï, ä, etc.)
        if (function_exists('transliterator_transliterate')) {
            $result = transliterator_transliterate(
                'Any-Latin; Latin-ASCII; Lower()',
                $input
            );
            if ($result !== false) {
                return $result;
            }
        }

        // Fallback: iconv ASCII//TRANSLIT
        if (function_exists('iconv')) {
            $result = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);
            if ($result !== false) {
                return $result;
            }
        }

        // Last resort: return as-is (matching may still work for ASCII chars)
        return $input;
    }
}
