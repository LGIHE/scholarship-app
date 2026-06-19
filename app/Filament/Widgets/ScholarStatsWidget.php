<?php

namespace App\Filament\Widgets;

use App\Models\Scholar;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScholarStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_stats');
    }

    protected function getStats(): array
    {
        $totalScholars = Scholar::count();
        $totalApplicants = User::whereHas('roles', function ($query) {
            $query->where('name', 'Applicant');
        })->count();
        
        $recentScholars = Scholar::where('created_at', '>=', now()->subMonth())->count();
        
        // Count scholars with complete academic info
        $scholarsWithCompleteInfo = Scholar::whereNotNull('university')
            ->where('university', '!=', 'Pending Entry')
            ->whereNotNull('course')
            ->where('course', '!=', 'Pending Entry')
            ->count();

        return [
            Stat::make('Active Scholars', $totalScholars)
                ->description('Currently enrolled scholars')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->url(route('filament.admin.resources.scholars.index')),
            
            Stat::make('Total Applicants', $totalApplicants)
                ->description('All registered applicants')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->url(route('filament.admin.resources.applicant-users.index')),
            
            Stat::make('Complete Profiles', $scholarsWithCompleteInfo)
                ->description('Scholars with full academic details')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->url(route('filament.admin.resources.scholars.index')),
        ];
    }
}
