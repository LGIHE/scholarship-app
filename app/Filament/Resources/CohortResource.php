<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CohortResource\Pages;
use App\Models\Cohort;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CohortResource extends Resource
{
    protected static ?string $model = Cohort::class;

    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Application Management';
    protected static ?string $navigationLabel = 'Scholarship Cohorts';
    protected static ?int    $navigationSort  = 0;   // appears first in the group

    // ── Access control ────────────────────────────────────────────────────────

    public static function canViewAny(): bool
    {
        return auth()->user()->can('cohort.view_any');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('cohort.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('cohort.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('cohort.delete');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('cohort.view');
    }

    // ── Form ──────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cohort Identity')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Cohort Name')
                            ->placeholder('e.g. Cohort 1 — 2026/2027')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('academic_year')
                            ->label('Academic Year')
                            ->placeholder('e.g. 2026/2027')
                            ->required()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->placeholder('e.g. 2026-2027')
                            ->helperText('Used in /scholarships/{slug}. Lowercase, hyphens only.')
                            ->required()
                            ->unique(Cohort::class, 'slug', ignoreRecord: true)
                            ->maxLength(50)
                            ->regex('/^[a-z0-9\-]+$/'),

                        Forms\Components\TextInput::make('scholarships_available')
                            ->label('Scholarships Available')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Application Window')
                    ->description('Set the date and time when applications open and when they close (deadline). Times are interpreted in the server\'s timezone.')
                    ->schema([
                        Forms\Components\DateTimePicker::make('opens_at')
                            ->label('Applications Open')
                            ->nullable()
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->helperText('Leave blank if applications open immediately.'),

                        Forms\Components\DateTimePicker::make('closes_at')
                            ->label('Application Deadline')
                            ->nullable()
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->helperText('Applications are locked at 23:59:59 on this date.')
                            ->after('opens_at'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Cohort')
                            ->helperText('Only one cohort can be active at a time. Enabling this will deactivate all other cohorts.')
                            ->onColor('success')
                            ->offColor('gray')
                            ->live(),
                    ]),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Public Description')
                            ->placeholder('Optional. Shown on the public scholarship call page.')
                            ->rows(4)
                            ->maxLength(2000),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    // ── Infolist (view page) ──────────────────────────────────────────────────

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Cohort Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Cohort Name'),

                        Infolists\Components\TextEntry::make('academic_year')
                            ->label('Academic Year'),

                        Infolists\Components\TextEntry::make('slug')
                            ->label('URL Slug')
                            ->formatStateUsing(fn ($state) => '/scholarships/' . $state),

                        Infolists\Components\TextEntry::make('scholarships_available')
                            ->label('Scholarships Available'),

                        Infolists\Components\TextEntry::make('opens_at')
                            ->label('Opens At')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not set'),

                        Infolists\Components\TextEntry::make('closes_at')
                            ->label('Deadline')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not set'),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),

                        Infolists\Components\TextEntry::make('applications_count')
                            ->label('Total Applications')
                            ->getStateUsing(fn ($record) => $record->applications()->count()),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('')
                            ->placeholder('No description provided.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cohort')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Academic Year')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('scholarships_available')
                    ->label('Slots')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('opens_at')
                    ->label('Opens')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('closes_at')
                    ->label('Deadline')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Applications')
                    ->counts('applications')
                    ->badge()
                    ->color('primary'),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn () => auth()->user()->can('cohort.view')),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->can('cohort.edit')),

                // Quick-activate action (deactivates all others automatically)
                Tables\Actions\Action::make('activate')
                    ->label('Set as Active')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Cohort $record) => ! $record->is_active && auth()->user()->can('cohort.edit'))
                    ->requiresConfirmation()
                    ->modalHeading('Activate this cohort?')
                    ->modalDescription('This will deactivate all other cohorts. The application deadline will switch to this cohort\'s closing date immediately.')
                    ->action(function (Cohort $record) {
                        Cohort::activateOnly($record);
                        Notification::make()
                            ->title('Cohort activated')
                            ->body("{$record->name} is now the active cohort.")
                            ->success()
                            ->send();
                    }),

                // Quick-deactivate (closes all applications)
                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Cohort $record) => $record->is_active && auth()->user()->can('cohort.edit'))
                    ->requiresConfirmation()
                    ->modalHeading('Deactivate this cohort?')
                    ->modalDescription('No cohort will be active. Applications will be treated as closed until another cohort is activated.')
                    ->action(function (Cohort $record) {
                        $record->update(['is_active' => false]);
                        Notification::make()
                            ->title('Cohort deactivated')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([]);   // no bulk delete — too destructive
    }

    // ── Pages ─────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCohorts::route('/'),
            'create' => Pages\CreateCohort::route('/create'),
            'view'   => Pages\ViewCohort::route('/{record}'),
            'edit'   => Pages\EditCohort::route('/{record}/edit'),
        ];
    }
}
