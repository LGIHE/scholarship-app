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
            Stat::make('Total Applications', Application::count())
                ->description('Total submitted across all time')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->url(route('filament.admin.resources.applications.index')),

            Stat::make('Pending Review', Application::whereIn('status', ['submitted', 'under_review'])->count())
                ->description('Needs committee action')
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
