<?php

namespace App\Filament\Pages;

use App\Console\Commands\NormaliseInstitutions as NormaliseInstitutionsCommand;
use App\Models\Application;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Admin screen for reviewing and applying institution name corrections.
 *
 * Shows a dry-run preview table of every affected application,
 * then lets an authorised user apply all corrections in one click.
 *
 * Also surfaces any institution values that cannot be matched to the
 * canonical list so they can be reviewed manually.
 */
class NormaliseInstitutions extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Fix Institution Names';
    protected static ?string $title           = 'Normalise Institution Names';
    protected static ?string $navigationGroup = 'Data Tools';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.normalise-institutions';

    // ── Access control ────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin')
            || auth()->user()->can('report.view');
    }

    // ── State ─────────────────────────────────────────────────────────────────

    /** Rows that will be corrected. */
    public array $rows = [];

    /** Rows whose institution could not be matched (shown for manual review). */
    public array $unknownRows = [];

    /** Summary counts. */
    public int $totalAffected = 0;
    public int $totalUnknown  = 0;

    /** UI state flags. */
    public bool $applied = false;
    public bool $scanned = false;

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->scan();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function scan(): void
    {
        $this->rows        = [];
        $this->unknownRows = [];
        $this->totalAffected = 0;
        $this->totalUnknown  = 0;

        Application::whereNotNull('personal_info')
            ->orderBy('id')
            ->chunk(200, function ($apps) {
                foreach ($apps as $app) {
                    $info    = $app->personal_info ?? [];
                    $raw     = trim($info['institution'] ?? '');

                    if ($raw === '') {
                        continue;
                    }

                    $appName = trim(($info['surname'] ?? '') . ' ' . ($info['other_names'] ?? ''));
                    if ($appName === '') {
                        $appName = $app->user?->name ?? '—';
                    }

                    // Already canonical — nothing to do
                    if (in_array($raw, NormaliseInstitutionsCommand::CANONICAL, true)) {
                        continue;
                    }

                    $corrected = NormaliseInstitutionsCommand::normalise($raw);

                    if ($corrected !== null) {
                        $this->rows[] = [
                            'id'        => $app->id,
                            'name'      => $appName,
                            'current'   => $raw,
                            'corrected' => $corrected,
                        ];
                        $this->totalAffected++;
                    } else {
                        // Could not match — surface for manual review
                        $this->unknownRows[] = [
                            'id'      => $app->id,
                            'name'    => $appName,
                            'current' => $raw,
                        ];
                        $this->totalUnknown++;
                    }
                }
            });

        $this->scanned = true;
        $this->applied = false;
    }

    public function apply(): void
    {
        if (empty($this->rows)) {
            Notification::make()
                ->title('Nothing to fix')
                ->body('No corrections are pending.')
                ->warning()
                ->send();
            return;
        }

        $updated = 0;

        foreach ($this->rows as $row) {
            $app = Application::find($row['id']);
            if (! $app) continue;

            $info = $app->personal_info ?? [];
            $current = trim($info['institution'] ?? '');

            // Re-validate — skip if already correct or state is stale
            if ($current === '' || in_array($current, NormaliseInstitutionsCommand::CANONICAL, true)) {
                continue;
            }

            $corrected = NormaliseInstitutionsCommand::normalise($current);
            if ($corrected === null) continue;

            $info['institution'] = $corrected;
            $app->update(['personal_info' => $info]);
            $updated++;
        }

        $this->applied = true;

        Notification::make()
            ->title('Institution names corrected')
            ->body("{$updated} application(s) updated.")
            ->success()
            ->send();

        // Re-scan to confirm the table is now empty
        $this->scan();
        $this->applied = true; // scan() resets applied; restore it
    }
}
