<?php

namespace App\Filament\Pages;

use App\Models\Application;
use App\Support\ApprovedCriteria;
use Filament\Pages\Page;

/**
 * EligibilityObserver — read-only observation view.
 *
 * Shows all distinct raw values currently stored in the database for:
 *   • Teaching Subject 1 & 2
 *   • Academic Programme (course)
 *   • Gender (derived from NIN prefix)
 *
 * Each value is flagged as ✓ Approved or ✗ Not approved so the admin can
 * identify dirty data that needs to be cleaned up directly in the application
 * records before it can appear in reports or analytics.
 */
class EligibilityObserver extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-eye';
    protected static ?string $navigationLabel = 'Eligibility Observer';
    protected static ?string $title           = 'Eligibility Observer';
    protected static ?string $navigationGroup = 'Application Management';
    protected static ?int    $navigationSort  = 12;
    protected static string  $view            = 'filament.pages.eligibility-observer';

    // ── Access control ────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->can('report.view');
    }

    // ── Computed properties (read on every render) ────────────────────────────

    /**
     * Returns rows for distinct teaching subject values with match status.
     * Format: [ 'value' => string, 'count' => int, 'approved' => bool ]
     */
    public function getSubjectRowsProperty(): array
    {
        $tally = [];

        Application::whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$tally) {
                $info = $app->personal_info ?? [];
                foreach (['teaching_subjects_1', 'teaching_subjects_2'] as $field) {
                    $raw = trim((string) ($info[$field] ?? ''));
                    if ($raw === '') continue;
                    if (!isset($tally[$raw])) {
                        $tally[$raw] = ['count' => 0, 'approved' => ApprovedCriteria::subjectMatches($raw)];
                    }
                    $tally[$raw]['count']++;
                }
            });

        // Sort: approved first, then by count desc
        uasort($tally, fn ($a, $b) =>
            $b['approved'] <=> $a['approved'] ?: $b['count'] <=> $a['count']
        );

        $rows = [];
        foreach ($tally as $value => $meta) {
            $rows[] = ['value' => $value, 'count' => $meta['count'], 'approved' => $meta['approved']];
        }
        return $rows;
    }

    /**
     * Returns rows for distinct academic programme values with match status.
     * Format: [ 'value' => string, 'count' => int, 'approved' => bool ]
     */
    public function getCourseRowsProperty(): array
    {
        $tally = [];

        Application::whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$tally) {
                $info = $app->personal_info ?? [];
                $raw  = trim((string) ($info['academic_programme'] ?? ''));
                if ($raw === '') return;
                if (!isset($tally[$raw])) {
                    $tally[$raw] = ['count' => 0, 'approved' => ApprovedCriteria::courseMatches($raw)];
                }
                $tally[$raw]['count']++;
            });

        uasort($tally, fn ($a, $b) =>
            $b['approved'] <=> $a['approved'] ?: $b['count'] <=> $a['count']
        );

        $rows = [];
        foreach ($tally as $value => $meta) {
            $rows[] = ['value' => $value, 'count' => $meta['count'], 'approved' => $meta['approved']];
        }
        return $rows;
    }

    /**
     * Returns rows for distinct gender values (derived from NIN prefix) with match status.
     * Format: [ 'value' => string, 'count' => int, 'approved' => bool ]
     */
    public function getGenderRowsProperty(): array
    {
        $tally = [];

        Application::whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$tally) {
                $info   = $app->personal_info ?? [];
                $nin    = trim((string) ($info['nin'] ?? ''));
                $prefix = strtoupper(substr($nin, 0, 2));

                $label    = match ($prefix) {
                    'CF'    => 'Female (CF…)',
                    'CM'    => 'Male (CM…)',
                    default => $prefix !== '' ? 'Unknown prefix: ' . $prefix : 'No NIN entered',
                };
                $approved = ($prefix === 'CF');

                if (!isset($tally[$label])) {
                    $tally[$label] = ['count' => 0, 'approved' => $approved];
                }
                $tally[$label]['count']++;
            });

        uasort($tally, fn ($a, $b) =>
            $b['approved'] <=> $a['approved'] ?: $b['count'] <=> $a['count']
        );

        $rows = [];
        foreach ($tally as $value => $meta) {
            $rows[] = ['value' => $value, 'count' => $meta['count'], 'approved' => $meta['approved']];
        }
        return $rows;
    }

    /** Summary counts for the stats banner. */
    public function getSummaryProperty(): array
    {
        $total     = 0;
        $eligible  = 0;

        Application::whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$total, &$eligible) {
                $total++;
                if (ApprovedCriteria::isEligible($app->personal_info ?? [])) {
                    $eligible++;
                }
            });

        return [
            'total'       => $total,
            'eligible'    => $eligible,
            'ineligible'  => $total - $eligible,
        ];
    }
}
