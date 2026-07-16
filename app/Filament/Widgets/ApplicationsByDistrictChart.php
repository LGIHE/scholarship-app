<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Support\ApprovedCriteria;
use App\Support\DistrictHelper;
use Filament\Widgets\ChartWidget;

class ApplicationsByDistrictChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by Subregion';

    protected static ?int $sort = 3;

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
                $info = $app->personal_info ?? [];

                // Skip ineligible applications
                if (!ApprovedCriteria::isEligible($info)) {
                    return;
                }

                $raw = trim((string) ($info['residence_district'] ?? ''));
                if ($raw === '') return;

                $subregion = DistrictHelper::subregion($raw);
                $grouped[$subregion] = ($grouped[$subregion] ?? 0) + 1;
            });

        // Sort descending, keep "Other" / "Unknown" last
        arsort($grouped);
        foreach (['Unknown', 'Other'] as $tail) {
            if (isset($grouped[$tail])) {
                $v = $grouped[$tail];
                unset($grouped[$tail]);
                $grouped[$tail] = $v;
            }
        }

        $labels = array_keys($grouped);
        $data   = array_values($grouped);

        $palette = [
            'rgb(59, 130, 246)',  'rgb(34, 197, 94)',  'rgb(251, 191, 36)',
            'rgb(239, 68, 68)',   'rgb(168, 85, 247)', 'rgb(249, 115, 22)',
            'rgb(20, 184, 166)',  'rgb(236, 72, 153)', 'rgb(99, 102, 241)',
            'rgb(156, 163, 175)', 'rgb(245, 158, 11)', 'rgb(16, 185, 129)',
            'rgb(139, 92, 246)',  'rgb(14, 165, 233)', 'rgb(244, 63, 94)',
            'rgb(107, 114, 128)',
        ];

        $colours = collect($labels)->map(fn ($_, $i) => $palette[$i % count($palette)])->toArray();

        return [
            'datasets' => [[
                'label'           => 'Applications',
                'data'            => $data,
                'backgroundColor' => $colours,
            ]],
            'labels' => $labels ?: ['No data'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
