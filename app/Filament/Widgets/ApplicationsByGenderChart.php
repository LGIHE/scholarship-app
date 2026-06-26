<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

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

        // Pull NIN values; CF = Female, CM = Male, anything else = Other
        $nins = Application::query()
            ->whereNotNull(DB::raw("json_extract(personal_info, '$.nin')"))
            ->pluck(DB::raw("json_extract(personal_info, '$.nin')"));

        foreach ($nins as $nin) {
            $prefix = strtoupper(substr(trim((string) $nin), 0, 2));
            if ($prefix === 'CF') {
                $counts['Female']++;
            } elseif ($prefix === 'CM') {
                $counts['Male']++;
            } else {
                $counts['Other']++;
            }
        }

        // Applications with no NIN at all count as Other
        $noNin = Application::query()
            ->where(function ($q) {
                $q->whereNull(DB::raw("json_extract(personal_info, '$.nin')"))
                  ->orWhere(DB::raw("json_extract(personal_info, '$.nin')"), '=', '');
            })
            ->count();

        $counts['Other'] += $noNin;

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
