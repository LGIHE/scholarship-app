<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

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
        $results = Application::query()
            ->whereNotNull(DB::raw("json_extract(personal_info, '$.residence_district')"))
            ->where(DB::raw("json_extract(personal_info, '$.residence_district')"), '!=', '')
            ->select(
                DB::raw("json_extract(personal_info, '$.residence_district') as district"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('district')
            ->orderByDesc('total')
            ->get();

        $labels = $results->pluck('district')->toArray();
        $data   = $results->pluck('total')->toArray();

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
