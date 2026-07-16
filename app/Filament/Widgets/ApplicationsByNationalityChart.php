<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Support\ApprovedCriteria;
use Filament\Widgets\ChartWidget;

class ApplicationsByNationalityChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by Nationality';

    protected static ?int $sort = 7;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    protected function getData(): array
    {
        $grouped = [];

        Application::query()
            ->whereNotNull('personal_info')
            ->whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$grouped) {
                $info      = $app->personal_info ?? [];
                $isUgandan = strtolower(trim((string) ($info['is_ugandan'] ?? '')));

                // Skip ineligible applications
                if (!ApprovedCriteria::isEligible($info)) {
                    return;
                }

                if ($isUgandan === 'yes') {
                    $nationality = 'Ugandan';
                } elseif ($isUgandan === 'no') {
                    // Use the free-text explanation, fall back to "Other"
                    $raw = trim((string) ($info['non_ugandan_explanation'] ?? ''));
                    $nationality = $raw !== '' ? mb_convert_case($raw, MB_CASE_TITLE, 'UTF-8') : 'Other';
                } else {
                    // is_ugandan not filled in yet (draft applications)
                    $nationality = 'Unknown';
                }

                $grouped[$nationality] = ($grouped[$nationality] ?? 0) + 1;
            });

        // Sort by count descending; keep "Unknown" last
        arsort($grouped);

        foreach (['Other', 'Unknown'] as $tail) {
            if (isset($grouped[$tail])) {
                $val = $grouped[$tail];
                unset($grouped[$tail]);
                $grouped[$tail] = $val;
            }
        }

        $labels = array_keys($grouped);
        $data   = array_values($grouped);

        $palette = [
            'rgb(59, 130, 246)',   // blue      – Ugandan (usually the largest slice)
            'rgb(34, 197, 94)',    // green
            'rgb(251, 191, 36)',   // yellow
            'rgb(239, 68, 68)',    // red
            'rgb(168, 85, 247)',   // purple
            'rgb(249, 115, 22)',   // orange
            'rgb(20, 184, 166)',   // teal
            'rgb(236, 72, 153)',   // pink
            'rgb(99, 102, 241)',   // indigo
            'rgb(156, 163, 175)',  // gray
        ];

        $colours = collect($labels)->map(fn ($_, $i) => $palette[$i % count($palette)])->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => $data,
                    'backgroundColor' => $colours,
                ],
            ],
            'labels' => $labels ?: ['No data'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
