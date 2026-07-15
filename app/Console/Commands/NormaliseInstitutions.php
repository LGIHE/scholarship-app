<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;

/**
 * Normalises free-text institution names in personal_info.institution
 * to the approved canonical list.
 *
 * Usage:
 *   php artisan app:normalise-institutions          # dry run (shows what would change)
 *   php artisan app:normalise-institutions --apply  # apply the changes
 */
class NormaliseInstitutions extends Command
{
    protected $signature   = 'app:normalise-institutions {--apply : Persist the changes to the database}';
    protected $description = 'Normalise free-text institution names to the approved canonical list';

    /**
     * Canonical institution names as stored in the database.
     * The application form saves these exact strings.
     */
    public const CANONICAL = [
        'Makerere University (All Campuses)',
        'Kyambogo University (All Campuses)',
        'Busitema University (All Campuses)',
        'Islamic University in Uganda (All Campuses)',
        'Gulu University (All Campuses)',
        'Muni University (All Campuses)',
        'Mountains of the Moon University',
        'Mbarara University of Science and Technology (All Campuses)',
        'Uganda Martyrs University (All Campuses)',
        'Kabale University (All Campuses)',
        'UNITE Kabale Campus',
        'UNITE Kaliro Campus',
        'UNITE Mubende Campus',
        'UNITE Muni Campus',
        'UNITE Unyama Campus',
    ];

    /**
     * Keyword → canonical institution mappings.
     * Keys are lowercase fragments found in free-text entries.
     * Order matters: more specific patterns first.
     */
    public const KEYWORD_MAP = [
        // Makerere — includes MUBS (Makerere University Business School) and "makarere" typo
        'makerere'                          => 'Makerere University (All Campuses)',
        'makarere'                          => 'Makerere University (All Campuses)',
        'mubs'                              => 'Makerere University (All Campuses)',

        // Kyambogo
        'kyambogo'                          => 'Kyambogo University (All Campuses)',
        'kyam'                              => 'Kyambogo University (All Campuses)',

        // Busitema
        'busitema'                          => 'Busitema University (All Campuses)',

        // Islamic University in Uganda
        // "kabojja" is a known campus variant — must come before generic iuiu/islamic
        'kabojja'                           => 'Islamic University in Uganda (All Campuses)',
        'females campus'                    => 'Islamic University in Uganda (All Campuses)',
        'islamic university in uganda'      => 'Islamic University in Uganda (All Campuses)',
        'islamic university'                => 'Islamic University in Uganda (All Campuses)',
        'iuiu'                              => 'Islamic University in Uganda (All Campuses)',

        // Gulu University — bare "gulu" as well as full name
        'gulu university'                   => 'Gulu University (All Campuses)',
        'gulu'                              => 'Gulu University (All Campuses)',

        // Mountains of the Moon — handle missing 's' and any mixed-case variant
        // (normalise() lowercases $raw before matching, so all keys must be lowercase)
        'mountains of the moon'             => 'Mountains of the Moon University',
        'mountain of the moon'              => 'Mountains of the Moon University',
        'mmu'                               => 'Mountains of the Moon University',

        // Mbarara University of Science and Technology
        // — bare "mbarara" catches "Mbarara school of science and technology" etc.
        'mbarara university of science'     => 'Mbarara University of Science and Technology (All Campuses)',
        'mbarara university'                => 'Mbarara University of Science and Technology (All Campuses)',
        'mbarara school of science'         => 'Mbarara University of Science and Technology (All Campuses)',
        'mbarara'                           => 'Mbarara University of Science and Technology (All Campuses)',
        'must'                              => 'Mbarara University of Science and Technology (All Campuses)',

        // Uganda Martyrs University — "marty's" / "martys" typo variants
        'uganda martyrs'                    => 'Uganda Martyrs University (All Campuses)',
        "uganda marty"                      => 'Uganda Martyrs University (All Campuses)',
        'umu'                               => 'Uganda Martyrs University (All Campuses)',

        // Kabale University — must come before bare "kabale" and "kabaale" (double-a typo)
        'kabale university'                 => 'Kabale University (All Campuses)',
        'kabaale university'                => 'Kabale University (All Campuses)',

        // UNITE campuses — all specific patterns before bare keywords
        // Reversed variant "mubende unite" and bare "mubende"
        'mubende unite'                     => 'UNITE Mubende Campus',
        'unite mubende'                     => 'UNITE Mubende Campus',
        'mubende'                           => 'UNITE Mubende Campus',

        // "unite campus (kabale)" and "unite-kabale" variants
        'unite-kabale'                      => 'UNITE Kabale Campus',
        'unite campus (kabale'              => 'UNITE Kabale Campus',
        'unite kabale'                      => 'UNITE Kabale Campus',

        'unite kaliro'                      => 'UNITE Kaliro Campus',
        'kaliro'                            => 'UNITE Kaliro Campus',

        'unite muni'                        => 'UNITE Muni Campus',

        'unite unyama'                      => 'UNITE Unyama Campus',
        'unyama'                            => 'UNITE Unyama Campus',

        // Bare "kabale" → UNITE Kabale Campus (after "kabale university" above)
        'kabale'                            => 'UNITE Kabale Campus',

        // Muni University — bare "muni" after "unite muni" to avoid false match
        'muni university'                   => 'Muni University (All Campuses)',
        'muni'                              => 'Muni University (All Campuses)',
    ];

    public function handle(): int
    {
        $apply    = $this->option('apply');
        $updated  = 0;
        $skipped  = 0;
        $unknown  = 0;

        $this->info($apply ? '⚡ Running in APPLY mode — changes will be saved.' : '🔍 Dry run — no changes will be saved. Pass --apply to persist.');
        $this->newLine();

        Application::whereNotNull('personal_info')->chunkById(100, function ($applications) use (&$updated, &$skipped, &$unknown, $apply) {
            foreach ($applications as $app) {
                $info = $app->personal_info ?? [];
                $raw  = trim($info['institution'] ?? '');

                if ($raw === '') {
                    $skipped++;
                    continue;
                }

                // Already canonical — skip
                if (in_array($raw, self::CANONICAL, true)) {
                    $skipped++;
                    continue;
                }

                $normalised = self::normalise($raw);

                if ($normalised === null) {
                    $this->line("  <comment>[ID {$app->id}] Could not match:</comment> {$raw}");
                    $unknown++;
                    continue;
                }

                $this->line("  <info>[ID {$app->id}]</info> \"{$raw}\" → \"{$normalised}\"");

                if ($apply) {
                    $info['institution'] = $normalised;
                    $app->update(['personal_info' => $info]);
                }

                $updated++;
            }
        });

        $this->newLine();
        $this->info('Summary:');
        $this->table(['Action', 'Count'], [
            [$apply ? 'Updated' : 'Would update', $updated],
            ['Already canonical / blank', $skipped],
            ['Could not match (manual review needed)', $unknown],
        ]);

        if (! $apply && ($updated > 0 || $unknown > 0)) {
            $this->newLine();
            $this->warn('Run with --apply to save changes.');
        }

        return self::SUCCESS;
    }

    /**
     * Attempt to map a raw institution string to a canonical name.
     * Returns null when no match is found.
     * Public static so the Filament page and other classes can reuse it.
     */
    public static function normalise(string $raw): ?string
    {
        $lower = mb_strtolower(trim($raw));

        foreach (self::KEYWORD_MAP as $keyword => $canonical) {
            if (str_contains($lower, $keyword)) {
                return $canonical;
            }
        }

        return null;
    }
}
