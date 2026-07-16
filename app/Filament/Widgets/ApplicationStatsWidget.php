<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Support\ApprovedCriteria;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicationStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_stats');
    }

    protected function getStats(): array
    {
        // Load non-draft applications and filter to eligible (approved criteria) only
        $all = Application::whereNotIn('status', ['draft'])
            ->get(['personal_info', 'status', 'id']);

        $eligible = Application::filterEligible($all);

        $inProgress = $eligible->whereIn('status', ['under_review'])->count();
        $submitted  = $eligible->where('status', 'submitted')->count();
        $approved   = $eligible->where('status', 'approved')->count();

        return [
            Stat::make('Applications in Progress', $inProgress)
                ->description('Under review (eligible applications)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->url(route('filament.admin.resources.applications.index')),

            Stat::make('Submitted Applications', $submitted)
                ->description('Awaiting committee action (eligible)')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning')
                ->url(route('filament.admin.resources.applications.index', ['tableFilters[status][value]' => 'submitted'])),

            Stat::make('Approved Scholars', $approved)
                ->description('Successfully awarded (eligible)')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url(route('filament.admin.resources.applications.index', ['tableFilters[status][value]' => 'approved'])),
        ];
    }
}
