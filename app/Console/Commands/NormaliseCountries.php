<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;

/**
 * Normalises free-text country values that are clearly Ugandan place names
 * (districts, municipalities, divisions, etc.) to "Uganda" across all three
 * address country fields.
 *
 * Affected fields (all inside personal_info JSON column):
 *   birth_country, origin_country, residence_country
 *
 * Usage:
 *   php artisan app:normalise-countries          # dry run (shows what would change)
 *   php artisan app:normalise-countries --apply  # persist the changes
 */
class NormaliseCountries extends Command
{
    protected $signature   = 'app:normalise-countries {--apply : Persist the changes to the database}';
    protected $description = 'Normalise country fields that contain Ugandan place names to "Uganda"';

    /**
     * Values that map to "Uganda".
     * Keys are lowercase; values are the replacement string.
     * This list is built from known bad values found in the data plus a set of
     * common Ugandan place-name patterns that should never appear as a country.
     */
    public const CORRECTIONS = [
        // Known bad values reported from the breakdown report
        'buyamba'                    => 'Uganda',
        'jinja northern division'    => 'Uganda',
        'njeru municipality'         => 'Uganda',
        'kiboga east'                => 'Uganda',
        'masaka municipality'        => 'Uganda',
        'mbale'                      => 'Uganda',
        'nyendo-mukungwe'            => 'Uganda',
        'bulambuli'                  => 'Uganda',
        'omoro'                      => 'Uganda',
        'bugabula north'             => 'Uganda',
        'kigulu south'               => 'Uganda',
        'amuru'                      => 'Uganda',
        'ugandan'                    => 'Uganda',
        'northern city division'     => 'Uganda',
        // Common patterns — blank / placeholder values
        'ug'                         => 'Uganda',
        'uganda.'                    => 'Uganda',
        'uganda republic'            => 'Uganda',
        'republic of uganda'         => 'Uganda',
        // District / municipality / division suffix patterns handled in normalise()
    ];

    /** Country fields inside personal_info to check. */
    private const COUNTRY_FIELDS = [
        'birth_country',
        'origin_country',
        'residence_country',
    ];

    public function handle(): int
    {
        $apply   = $this->option('apply');
        $changed = 0;
        $skipped = 0;

        $this->info($apply
            ? '⚡ Running in APPLY mode — changes will be saved.'
            : '🔍 Dry run — no changes will be saved. Pass --apply to persist.');
        $this->newLine();

        Application::whereNotNull('personal_info')->chunkById(100, function ($apps) use (&$changed, &$skipped, $apply) {
            foreach ($apps as $app) {
                $info  = $app->personal_info ?? [];
                $dirty = false;

                foreach (self::COUNTRY_FIELDS as $field) {
                    $raw = trim($info[$field] ?? '');

                    if ($raw === '' || $raw === 'Uganda') {
                        $skipped++;
                        continue;
                    }

                    $corrected = $this->normalise($raw);

                    if ($corrected === null) {
                        // Not a recognised bad value — leave as-is
                        $skipped++;
                        continue;
                    }

                    if ($corrected !== $raw) {
                        $this->line(
                            "  <info>[ID {$app->id}][{$field}]</info> \"{$raw}\" → \"{$corrected}\""
                        );
                        $info[$field] = $corrected;
                        $dirty = true;
                        $changed++;
                    } else {
                        $skipped++;
                    }
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
            ['Already correct / blank / unrecognised', $skipped],
        ]);

        if (! $apply && $changed > 0) {
            $this->newLine();
            $this->warn('Run with --apply to save changes.');
        }

        return self::SUCCESS;
    }

    /**
     * Attempt to map a raw country string to "Uganda".
     * Returns the corrected value, or null if this value should be left alone.
     */
    public static function normalise(string $raw): ?string
    {
        if ($raw === 'Uganda') {
            return 'Uganda';
        }

        $lower = mb_strtolower(trim($raw));

        // Direct lookup in corrections map
        if (isset(self::CORRECTIONS[$lower])) {
            return self::CORRECTIONS[$lower];
        }

        // Any value ending with a typical Ugandan administrative suffix
        // is almost certainly a place name entered by mistake
        $ugandanSuffixes = [
            ' division',
            ' municipality',
            ' town council',
            ' sub-county',
            ' county',
            ' district',
            ' city',
        ];

        foreach ($ugandanSuffixes as $suffix) {
            if (str_ends_with($lower, $suffix)) {
                return 'Uganda';
            }
        }

        return null; // Not recognised — leave unchanged
    }
}
