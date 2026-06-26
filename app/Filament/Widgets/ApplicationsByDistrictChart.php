<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;

class ApplicationsByDistrictChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by District';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    protected function getData(): array
    {
        // Load personal_info as array via the model cast, extract district in PHP
        $grouped = [];
        Application::query()
            ->whereNotNull('personal_info')
            ->get(['personal_info'])
            ->each(function ($app) use (&$grouped) {
                $raw = trim((string) ($app->personal_info['residence_district'] ?? ''));
                if ($raw === '') return;
                $key = strtolower(preg_replace('/\s+/', ' ', $raw));
                $grouped[$key] = ($grouped[$key] ?? 0) + 1;
            });

        arsort($grouped);

        $labels = array_map(fn ($k) => mb_convert_case($k, MB_CASE_TITLE, 'UTF-8'), array_keys($grouped));
        $data   = array_values($grouped);

        // Colour palette — cycles if there are more districts than colours
        $palette = [
            'rgb(59, 130, 246)',   // blue
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
