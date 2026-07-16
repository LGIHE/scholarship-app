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
        // "Applications in Progress" counts ALL draft + under_review applications
        // regardless of eligibility — these have not been submitted yet so their
        // data may still be incomplete / uncleaned. Filtering them out would give
        // a misleading zero while work is still ongoing.
        $inProgress = Application::whereIn('status', ['draft', 'under_review'])->count();

        // Submitted and Approved counts are restricted to eligible applications only
        // (approved gender + course + subject) because those appear in reports.
        $eligible  = Application::filterEligible(
            Application::whereNotIn('status', ['draft'])
                ->get(['personal_info', 'status', 'id'])
        );

        $submitted = $eligible->where('status', 'submitted')->count();
        $approved  = $eligible->where('status', 'approved')->count();

        return [
            Stat::make('Applications in Progress', $inProgress)
                ->description('Draft and under review')
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
