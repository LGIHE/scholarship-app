<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        return auth()->user()->can('dashboard.view');
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\ApplicationStatsWidget::class,
            \App\Filament\Widgets\ScholarStatsWidget::class,
            \App\Filament\Widgets\ApplicationsByStatusChart::class,
            \App\Filament\Widgets\RecentApplicationsWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return 2;
    }
}
