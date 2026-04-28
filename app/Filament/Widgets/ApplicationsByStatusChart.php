<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;

class ApplicationsByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by Status';
    
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_charts');
    }

    protected function getData(): array
    {
        $statuses = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        $data = [];
        foreach ($statuses as $key => $label) {
            $data[] = Application::where('status', $key)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(156, 163, 175)', // gray for draft
                        'rgb(59, 130, 246)',  // blue for submitted
                        'rgb(251, 191, 36)',  // yellow for under review
                        'rgb(34, 197, 94)',   // green for approved
                        'rgb(239, 68, 68)',   // red for rejected
                    ],
                ],
            ],
            'labels' => array_values($statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
