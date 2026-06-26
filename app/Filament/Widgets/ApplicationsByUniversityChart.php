<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;

class ApplicationsByUniversityChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by University';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    protected function getData(): array
    {
        // Load personal_info via model cast, extract institution in PHP
        $grouped = [];
        Application::query()
            ->whereNotNull('personal_info')
            ->get(['personal_info'])
            ->each(function ($app) use (&$grouped) {
                $raw = trim((string) ($app->personal_info['institution'] ?? ''));
                if ($raw === '') return;
                $key = strtolower(preg_replace('/\s+/', ' ', $raw));
                $grouped[$key] = ($grouped[$key] ?? 0) + 1;
            });

        arsort($grouped);

        $labels = array_map(fn ($k) => mb_convert_case($k, MB_CASE_TITLE, 'UTF-8'), array_keys($grouped));
        $data   = array_values($grouped);

        $palette = [
            'rgb(59, 130, 246)',
            'rgb(34, 197, 94)',
            'rgb(251, 191, 36)',
            'rgb(239, 68, 68)',
            'rgb(168, 85, 247)',
            'rgb(249, 115, 22)',
            'rgb(20, 184, 166)',
            'rgb(236, 72, 153)',
            'rgb(99, 102, 241)',
            'rgb(156, 163, 175)',
        ];

        $colours = array_map(fn ($i) => $palette[$i % count($palette)], array_keys($data));

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => $data ?: [0],
                    'backgroundColor' => $colours ?: [$palette[0]],
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
