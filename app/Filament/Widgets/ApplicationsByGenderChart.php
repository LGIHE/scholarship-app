<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;

class ApplicationsByGenderChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by Gender';

    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    protected function getData(): array
    {
        $counts = ['Female' => 0, 'Male' => 0, 'Other' => 0];

        // Use model cast to read personal_info array directly — no DB::raw needed
        // Only count submitted applications (excludes drafts)
        Application::query()
            ->whereNotNull('personal_info')
            ->whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$counts) {
                $nin    = trim((string) ($app->personal_info['nin'] ?? ''));
                $prefix = strtoupper(substr($nin, 0, 2));

                if ($prefix === 'CF') {
                    $counts['Female']++;
                } elseif ($prefix === 'CM') {
                    $counts['Male']++;
                } else {
                    $counts['Other']++;
                }
            });

        // Remove zero-count categories for a cleaner chart
        $counts = array_filter($counts, fn ($v) => $v > 0);

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => array_values($counts),
                    'backgroundColor' => [
                        'rgb(236, 72, 153)',  // pink  – Female
                        'rgb(59, 130, 246)',  // blue  – Male
                        'rgb(156, 163, 175)', // gray  – Other
                    ],
                ],
            ],
            'labels' => array_keys($counts),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
