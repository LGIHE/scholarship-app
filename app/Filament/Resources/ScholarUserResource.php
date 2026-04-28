<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class ScholarUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'Scholar Users';
    
    protected static ?string $modelLabel = 'Scholar User';
    
    protected static ?string $navigationGroup = null; // Hidden from navigation
    
    protected static ?int $navigationSort = 2;
    
    protected static bool $shouldRegisterNavigation = false; // Hide from sidebar

    public static function getEloquentQuery(): Builder
    {
        // Only show users with Scholar role (approved applicants)
        return parent::getEloquentQuery()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Scholar');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->label('Password')
                            ->helperText('Leave blank to keep current password'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Scholar Information')
                    ->schema([
                        Forms\Components\Placeholder::make('scholar_info')
                            ->label('')
                            ->content('This user has the Scholar role. Manage their academic details in the Scholars section.')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Additional Permissions')
                    ->description('Grant individual permissions beyond the Scholar role. These permissions are added to (not replacing) the role\'s permissions.')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                return \Spatie\Permission\Models\Permission::all()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->searchable()
                            ->helperText('Select additional permissions to grant this user beyond their role'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('scholar.university')
                    ->label('University')
                    ->searchable()
                    ->default('Not Set'),
                Tables\Columns\TextColumn::make('scholar.course')
                    ->label('Course')
                    ->searchable()
                    ->default('Not Set'),
                Tables\Columns\TextColumn::make('scholar.student_id')
                    ->label('Student ID')
                    ->searchable()
                    ->default('Not Set'),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Direct Permissions')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->toggleable()
                    ->limit(2)
                    ->tooltip(function (User $record): ?string {
                        $permissions = $record->permissions->pluck('name')->toArray();
                        return count($permissions) > 0 ? implode(', ', $permissions) : null;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->modal()
                    ->modalWidth('4xl'),
                Tables\Actions\Action::make('view_scholar')
                    ->label('View Scholar Details')
                    ->icon('heroicon-o-academic-cap')
                    ->url(fn (User $record): string => $record->scholar 
                        ? route('filament.admin.resources.scholars.edit', ['record' => $record->scholar->id])
                        : '#')
                    ->visible(fn (User $record): bool => $record->scholar !== null)
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholarUsers::route('/'),
        ];
    }
}
