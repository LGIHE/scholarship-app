<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Support\ApprovedCriteria;
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
        // Collect eligible application IDs (PHP-level fuzzy matching on JSON fields)
        $eligibleIds = Application::whereNotIn('status', ['draft'])
            ->get(['id', 'personal_info'])
            ->filter(fn ($app) => ApprovedCriteria::isEligible($app->personal_info ?? []))
            ->pluck('id');

        return $table
            ->query(
                Application::query()
                    ->whereIn('id', $eligibleIds)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('applicant_full_name')
                    ->label('Applicant')
                    ->getStateUsing(fn ($record): string => trim(
                        ($record->personal_info['surname'] ?? '')
                        . ' ' .
                        ($record->personal_info['other_names'] ?? '')
                    ) ?: ($record->user->name ?? '—')),
                Tables\Columns\TextColumn::make('nationality')
                    ->label('Nationality')
                    ->getStateUsing(function ($record): string {
                        $info = $record->personal_info ?? [];
                        if (($info['is_ugandan'] ?? null) === 'yes') return 'Ugandan';
                        if (!empty($info['non_ugandan_explanation'])) return $info['non_ugandan_explanation'];
                        if (($info['is_ugandan'] ?? null) === 'no') return 'Non-Ugandan';
                        return '—';
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Ugandan' ? 'success' : ($state === '—' ? 'gray' : 'warning')),
                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->getStateUsing(function ($record): string {
                        $dob = $record->personal_info['date_of_birth'] ?? null;
                        if (!$dob) return '—';
                        try { return (string) \Carbon\Carbon::parse($dob)->age; }
                        catch (\Exception) { return '—'; }
                    }),
                Tables\Columns\IconColumn::make('disability')
                    ->label('Disability')
                    ->getStateUsing(fn ($record): bool => ($record->personal_info['has_disability'] ?? null) === 'yes')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'submitted',
                        'warning' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'draft',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
