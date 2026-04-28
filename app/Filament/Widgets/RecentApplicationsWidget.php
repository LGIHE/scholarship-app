<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentApplicationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('dashboard.view_stats');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Application::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Applicant')
                    ->searchable(),
                Tables\Columns\TextColumn::make('personal_info.first_name')
                    ->label('First Name'),
                Tables\Columns\TextColumn::make('personal_info.last_name')
                    ->label('Last Name'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'submitted',
                        'warning' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'draft',
                    ]),
                Tables\Columns\TextColumn::make('scoring_breakdown.total')
                    ->label('Score')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state < 60 => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
