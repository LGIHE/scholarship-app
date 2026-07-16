<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Support\ApprovedCriteria;
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
        // Only approved criteria: Female only in this scholarship
        $counts = ['Female' => 0];

        Application::query()
            ->whereNotNull('personal_info')
            ->whereNotIn('status', ['draft'])
            ->get(['personal_info'])
            ->each(function ($app) use (&$counts) {
                $info = $app->personal_info ?? [];

                // Must meet all eligibility criteria before counting
                if (!ApprovedCriteria::isEligible($info)) {
                    return;
                }

                // Approved gender is Female only
                if (ApprovedCriteria::isFemale($info)) {
                    $counts['Female']++;
                }
            });

        $counts = array_filter($counts, fn ($v) => $v > 0);

        if (empty($counts)) {
            return [
                'datasets' => [['label' => 'Applications', 'data' => [0], 'backgroundColor' => ['rgb(156, 163, 175)']]],
                'labels'   => ['No eligible data'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Applications',
                    'data'            => array_values($counts),
                    'backgroundColor' => [
                        'rgb(236, 72, 153)',  // pink – Female
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
