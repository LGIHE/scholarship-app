<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarResource\Pages;
use App\Filament\Resources\ScholarResource\RelationManagers;
use App\Models\Scholar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScholarResource extends Resource
{
    protected static ?string $model = Scholar::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationGroup = 'Scholar Management';
    
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('scholar.view_any');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('scholar.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('scholar.delete');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('scholar.view');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Scholar Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('User'),
                        Forms\Components\Select::make('application_id')
                            ->relationship('application', 'id')
                            ->label('Application')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Academic Details')
                    ->schema([
                        Forms\Components\TextInput::make('university')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('course')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('student_id')
                            ->label('Student ID')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('graduation_date')
                            ->label('Expected Graduation Date'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Scholar Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('university')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('student_id')
                    ->label('Student ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicProgress_count')
                    ->counts('academicProgress')
                    ->label('Progress Records')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn () => auth()->user()->can('scholar.view')),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->can('scholar.edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AcademicProgressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholars::route('/'),
            'view' => Pages\ViewScholar::route('/{record}'),
            'edit' => Pages\EditScholar::route('/{record}/edit'),
        ];
    }
}
