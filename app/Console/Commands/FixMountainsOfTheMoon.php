<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;

/**
 * One-shot fix: finds every application whose institution field contains
 * "mountain of the moon" (case-insensitive) but is NOT already stored as
 * the correct canonical value, and updates it.
 *
 * Usage:
 *   php artisan app:fix-mountains          # dry run
 *   php artisan app:fix-mountains --apply  # persist changes
 */
class FixMountainsOfTheMoon extends Command
{
    protected $signature   = 'app:fix-mountains {--apply : Persist the changes to the database}';
    protected $description = 'Fix all case/spelling variants of Mountains of the Moon University';

    private const CORRECT = 'Mountains of the Moon University';

    public function handle(): int
    {
        $apply   = $this->option('apply');
        $fixed   = 0;
        $skipped = 0;

        $this->info($apply
            ? '⚡ APPLY mode — changes will be saved.'
            : '🔍 Dry run — no changes saved. Pass --apply to persist.');
        $this->newLine();

        Application::whereNotNull('personal_info')
            ->chunkById(100, function ($apps) use ($apply, &$fixed, &$skipped) {
                foreach ($apps as $app) {
                    $info = $app->personal_info ?? [];
                    $raw  = trim($info['institution'] ?? '');

                    if ($raw === '') {
                        $skipped++;
                        continue;
                    }

                    // Already correct — skip
                    if ($raw === self::CORRECT) {
                        $skipped++;
                        continue;
                    }

                    // Case-insensitive check for any "mountain(s) of the moon" variant
                    $lower = mb_strtolower($raw);
                    if (!str_contains($lower, 'mountain') || !str_contains($lower, 'moon')) {
                        $skipped++;
                        continue;
                    }

                    $this->line("  <info>[ID {$app->id}]</info> \"{$raw}\" → \"" . self::CORRECT . '"');

                    if ($apply) {
                        $info['institution'] = self::CORRECT;
                        $app->update(['personal_info' => $info]);
                    }

                    $fixed++;
                }
            });

        $this->newLine();
        $this->table(['Action', 'Count'], [
            [$apply ? 'Fixed' : 'Would fix', $fixed],
            ['Already correct / irrelevant', $skipped],
        ]);

        if (!$apply && $fixed > 0) {
            $this->newLine();
            $this->warn('Run with --apply to save changes.');
        }

        return self::SUCCESS;
    }
}
