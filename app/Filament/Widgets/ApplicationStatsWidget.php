<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ApplicationStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_stats');
    }

    protected function getStats(): array
    {
        // For avg score, parsing the JSON column generically
        $avgScore = Application::whereNotNull('scoring_breakdown')
            ->get()
            ->avg(function ($app) {
                return (float) ($app->scoring_breakdown['total'] ?? 0);
            });

        return [
            Stat::make('Total Applications', Application::count())
                ->description('Total submitted across all time')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Pending Review', Application::whereIn('status', ['submitted', 'under_review'])->count())
                ->description('Needs committee action')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('Approved Scholars', Application::where('status', 'approved')->count())
                ->description('Successfully awarded')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Average Score', number_format($avgScore, 1))
                ->description('Out of 100')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
