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
        return [
            Stat::make('Applications in Progress', Application::whereIn('status', ['draft', 'under_review'])->count())
                ->description('Draft and under review')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->url(route('filament.admin.resources.applications.index')),

            Stat::make('Submitted Applications', Application::where('status', 'submitted')->count())
                ->description('Awaiting committee action')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning')
                ->url(route('filament.admin.resources.applications.index', ['tableFilters[status][value]' => 'submitted'])),

            Stat::make('Approved Scholars', Application::where('status', 'approved')->count())
                ->description('Successfully awarded')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url(route('filament.admin.resources.applications.index', ['tableFilters[status][value]' => 'approved'])),
        ];
    }
}
