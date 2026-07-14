<?php

namespace App\Filament\Pages;

use App\Console\Commands\NormaliseCountries as NormaliseCountriesCommand;
use App\Models\Application;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Admin screen for reviewing and applying country-field corrections.
 *
 * Shows a dry-run preview table of every affected application/field,
 * then lets an authorised user apply all corrections in one click.
 */
class NormaliseCountries extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = 'Fix Country Values';
    protected static ?string $title           = 'Normalise Country Fields';
    protected static ?string $navigationGroup = 'Data Tools';
    protected static ?int    $navigationSort  = 20;
    protected static string  $view            = 'filament.pages.normalise-countries';

    // ── Access control ────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin')
            || auth()->user()->can('report.view');
    }

    // ── State ─────────────────────────────────────────────────────────────────

    /** Rows to display in the preview table. Populated by scanAffected(). */
    public array $rows = [];

    /** Summary counts after the last scan. */
    public int $totalAffected = 0;
    public int $totalFields   = 0;

    /** Whether changes have already been applied in this session. */
    public bool $applied = false;

    /** Whether the scan has been run at least once. */
    public bool $scanned = false;

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->scan();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    /**
     * Scan the database and populate the preview table.
     * Does NOT write any changes.
     */
    public function scan(): void
    {
        $this->rows         = [];
        $this->totalAffected = 0;
        $this->totalFields   = 0;

        $fields = ['birth_country', 'origin_country', 'residence_country'];

        Application::whereNotNull('personal_info')
            ->orderBy('id')
            ->chunk(200, function ($apps) use ($fields) {
                foreach ($apps as $app) {
                    $info         = $app->personal_info ?? [];
                    $appName      = trim(($info['surname'] ?? '') . ' ' . ($info['other_names'] ?? ''));
                    if ($appName === '') {
                        $appName = $app->user?->name ?? '—';
                    }

                    foreach ($fields as $field) {
                        $raw = trim($info[$field] ?? '');

                        if ($raw === '' || $raw === 'Uganda') {
                            continue;
                        }

                        $corrected = NormaliseCountriesCommand::normalise($raw);

                        if ($corrected === null) {
                            continue; // Not a recognised bad value — skip
                        }

                        $this->rows[] = [
                            'id'        => $app->id,
                            'name'      => $appName,
                            'field'     => $this->fieldLabel($field),
                            'raw_field' => $field,
                            'current'   => $raw,
                            'corrected' => $corrected,
                        ];

                        $this->totalFields++;
                    }

                    // Count unique applications affected
                }
            });

        // Unique application IDs affected
        $this->totalAffected = count(array_unique(array_column($this->rows, 'id')));
        $this->scanned       = true;
        $this->applied       = false;
    }

    /**
     * Apply all corrections found in the last scan and refresh.
     */
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

        // Group rows by application ID so we save each app only once
        $byApp = [];
        foreach ($this->rows as $row) {
            $byApp[$row['id']][] = $row;
        }

        foreach ($byApp as $appId => $appRows) {
            $app  = Application::find($appId);
            if (! $app) continue;

            $info  = $app->personal_info ?? [];
            $dirty = false;

            foreach ($appRows as $row) {
                $field = $row['raw_field'];
                // Re-validate before writing — state could be stale
                $current = trim($info[$field] ?? '');
                if ($current === '' || $current === 'Uganda') continue;

                $corrected = NormaliseCountriesCommand::normalise($current);
                if ($corrected === null) continue;

                $info[$field] = $corrected;
                $dirty = true;
                $updated++;
            }

            if ($dirty) {
                $app->update(['personal_info' => $info]);
            }
        }

        $this->applied = true;

        Notification::make()
            ->title('Country values corrected')
            ->body("{$updated} field(s) updated across {$this->totalAffected} application(s).")
            ->success()
            ->send();

        // Re-scan to confirm the table is now empty
        $this->scan();
        $this->applied = true; // scan() resets applied; restore it
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function fieldLabel(string $field): string
    {
        return match ($field) {
            'birth_country'     => 'Birth Country',
            'origin_country'    => 'Origin Country',
            'residence_country' => 'Residence Country',
            default             => ucwords(str_replace('_', ' ', $field)),
        };
    }
}
