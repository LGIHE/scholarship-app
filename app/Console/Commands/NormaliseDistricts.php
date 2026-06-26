<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;

/**
 * Normalises free-text district names to the canonical Uganda district list
 * across all address fields and the guardian location.
 *
 * Affected fields:
 *  personal_info  → birth_district, origin_district, residence_district
 *  guardian_info  → guardian_district
 *
 * Usage:
 *   php artisan app:normalise-districts          # dry run
 *   php artisan app:normalise-districts --apply  # persist changes
 */
class NormaliseDistricts extends Command
{
    protected $signature   = 'app:normalise-districts {--apply : Persist the changes to the database}';
    protected $description = 'Normalise free-text district names to the canonical Uganda district list';

    /**
     * Canonical Uganda districts grouped by region.
     * Each district appears exactly as it should be stored.
     */
    private const DISTRICTS = [
        // Central
        'Buikwe', 'Bukomansimbi', 'Buvuma', 'Gomba', 'Kalangala', 'Kalungu',
        'Kampala', 'Kayunga', 'Kiboga', 'Kyankwanzi', 'Kyotera', 'Luweero',
        'Lwengo', 'Lyantonde', 'Masaka', 'Mityana', 'Mpigi', 'Mubende',
        'Mukono', 'Nakaseke', 'Nakasongola', 'Rakai', 'Sembabule', 'Wakiso',
        // Eastern
        'Amuria', 'Budaka', 'Bududa', 'Bugiri', 'Bugweri', 'Bukedea', 'Bukwa',
        'Bulambuli', 'Busia', 'Butebo', 'Buyende', 'Iganga', 'Jinja',
        'Kaberamaido', 'Kaliro', 'Kamuli', 'Kapchorwa', 'Katakwi', 'Kibuku',
        'Kumi', 'Kween', 'Luuka', 'Manafwa', 'Mayuge', 'Mbale', 'Namayingo',
        'Namisindwa', 'Namutumba', 'Ngora', 'Pallisa', 'Serere', 'Sironko',
        'Soroti', 'Tororo',
        // Northern
        'Abim', 'Adjumani', 'Agago', 'Alebtong', 'Amolatar', 'Amudat', 'Amuru',
        'Apac', 'Arua', 'Dokolo', 'Gulu', 'Kaabong', 'Kitgum', 'Koboko', 'Kole',
        'Kotido', 'Lamwo', 'Lira', 'Maracha', 'Moroto', 'Moyo', 'Madi-Okollo',
        'Napak', 'Nebbi', 'Nwoya', 'Obongi', 'Omoro', 'Otuke', 'Oyam', 'Pader',
        'Pakwach', 'Terego', 'Zombo',
        // Western
        'Buhweju', 'Buliisa', 'Bundibugyo', 'Bushenyi', 'Hoima', 'Ibanda',
        'Isingiro', 'Kabale', 'Kabarole', 'Kagadi', 'Kakumiro', 'Kamwenge',
        'Kanungu', 'Kasese', 'Kibaale', 'Kikuube', 'Kiruhura', 'Kiryandongo',
        'Kisoro', 'Kyegegwa', 'Kyenjojo', 'Masindi', 'Mbarara', 'Mitooma',
        'Ntoroko', 'Ntungamo', 'Rubanda', 'Rubirizi', 'Rukiga', 'Rukungiri',
        'Rwampara', 'Sheema',
    ];

    /**
     * Alias map: lowercase variants → canonical name.
     * "district" suffix variants are handled automatically in normalise().
     */
    private const ALIASES = [
        'kampala city'                => 'Kampala',
        'kla'                         => 'Kampala',
        'entebbe'                     => 'Wakiso',   // town in Wakiso district
        'luwero'                      => 'Luweero',
        'luweero district'            => 'Luweero',
        'luwero district'             => 'Luweero',
        'mbarara city'                => 'Mbarara',
        'gulu city'                   => 'Gulu',
        'jinja city'                  => 'Jinja',
        'fort portal'                 => 'Kabarole', // Fort Portal city is in Kabarole
        'fort portal city'            => 'Kabarole',
        'hoima city'                  => 'Hoima',
        'lira city'                   => 'Lira',
        'arua city'                   => 'Arua',
        'masaka city'                 => 'Masaka',
        'mbale city'                  => 'Mbale',
        'soroti city'                 => 'Soroti',
    ];

    /**
     * Personal info address field prefixes to process.
     */
    private const PERSONAL_PREFIXES = ['birth', 'origin', 'residence'];

    public function handle(): int
    {
        $apply   = $this->option('apply');
        $changed = 0;
        $skipped = 0;
        $unknown = 0;

        // Build a lowercase lookup: 'kampala' => 'Kampala'
        $lookup = [];
        foreach (self::DISTRICTS as $d) {
            $lookup[mb_strtolower($d)] = $d;
        }

        $this->info($apply
            ? '⚡ Running in APPLY mode — changes will be saved.'
            : '🔍 Dry run — no changes will be saved. Pass --apply to persist.');
        $this->newLine();

        Application::whereNotNull('personal_info')->chunkById(100, function ($apps) use (&$changed, &$skipped, &$unknown, $apply, $lookup) {
            foreach ($apps as $app) {
                $personalInfo = $app->personal_info ?? [];
                $guardianInfo = $app->guardian_info ?? [];

                $piDirty = false;
                $giDirty = false;

                // ── personal_info address districts ───────────────────────
                foreach (self::PERSONAL_PREFIXES as $prefix) {
                    $field = "{$prefix}_district";
                    $raw   = trim($personalInfo[$field] ?? '');
                    if ($raw === '') { $skipped++; continue; }

                    $normalised = $this->normalise($raw, $lookup);

                    if ($normalised === null) {
                        $this->line("  <comment>[ID {$app->id}][personal.{$field}] No match:</comment> {$raw}");
                        $unknown++;
                        continue;
                    }

                    if ($normalised !== $raw) {
                        $this->line("  <info>[ID {$app->id}][personal.{$field}]</info> \"{$raw}\" → \"{$normalised}\"");
                        $personalInfo[$field] = $normalised;
                        $piDirty = true;
                        $changed++;
                    } else {
                        $skipped++;
                    }
                }

                // ── guardian_info district ────────────────────────────────
                $rawGd = trim($guardianInfo['guardian_district'] ?? '');
                if ($rawGd !== '') {
                    $normGd = $this->normalise($rawGd, $lookup);
                    if ($normGd === null) {
                        $this->line("  <comment>[ID {$app->id}][guardian.guardian_district] No match:</comment> {$rawGd}");
                        $unknown++;
                    } elseif ($normGd !== $rawGd) {
                        $this->line("  <info>[ID {$app->id}][guardian.guardian_district]</info> \"{$rawGd}\" → \"{$normGd}\"");
                        $guardianInfo['guardian_district'] = $normGd;
                        $giDirty = true;
                        $changed++;
                    } else {
                        $skipped++;
                    }
                }

                if ($apply) {
                    $updateData = [];
                    if ($piDirty) $updateData['personal_info'] = $personalInfo;
                    if ($giDirty) $updateData['guardian_info'] = $guardianInfo;
                    if ($updateData) $app->update($updateData);
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

    /**
     * Attempt to map a raw district string to a canonical name.
     *
     * Strategy:
     * 1. Strip trailing " district" / " city" suffixes.
     * 2. Check alias map.
     * 3. Check canonical lookup (case-insensitive exact match).
     * 4. Fuzzy: check if any canonical district name is contained in the raw string.
     *
     * Returns null if no match found.
     */
    private function normalise(string $raw, array $lookup): ?string
    {
        // Already canonical?
        if (in_array($raw, self::DISTRICTS, true)) {
            return $raw;
        }

        $lower = mb_strtolower(trim($raw));

        // Check alias map first
        if (isset(self::ALIASES[$lower])) {
            return self::ALIASES[$lower];
        }

        // Strip common suffixes and re-check
        $stripped = preg_replace('/\s+(district|city|municipality|town council|sub-county|county|region)\s*$/i', '', $lower);
        $stripped = trim($stripped ?? $lower);

        if (isset(self::ALIASES[$stripped])) {
            return self::ALIASES[$stripped];
        }

        if (isset($lookup[$stripped])) {
            return $lookup[$stripped];
        }

        // Direct lower-case match with original
        if (isset($lookup[$lower])) {
            return $lookup[$lower];
        }

        // Fuzzy: canonical name is a substring of the raw input
        foreach ($lookup as $lcCanon => $canon) {
            if (str_contains($lower, $lcCanon)) {
                return $canon;
            }
        }

        return null;
    }
}
