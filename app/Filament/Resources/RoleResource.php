<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = 'System Administration';
    
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('role.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('role.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('role.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('role.delete');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('role.view');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Role Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Role name (e.g., System Admin, Committee Member)'),
                        Forms\Components\Select::make('guard_name')
                            ->options([
                                'web' => 'Web',
                            ])
                            ->default('web')
                            ->required()
                            ->helperText('Guard name for the role'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Permissions')
                    ->schema([
                        Forms\Components\Tabs::make('Permissions')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Application Management')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('permissions')
                                            ->label('')
                                            ->relationship('permissions', 'name')
                                            ->options(function () {
                                                return Permission::where('name', 'like', 'application.%')
                                                    ->pluck('name', 'id')
                                                    ->mapWithKeys(fn ($name, $id) => [$id => ucwords(str_replace(['.', '_'], ' ', $name))]);
                                            })
                                            ->columns(2)
                                            ->gridDirection('row')
                                            ->bulkToggleable(),
                                    ]),
                                
                                Forms\Components\Tabs\Tab::make('Scholar Management')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('permissions')
                                            ->label('')
                                            ->relationship('permissions', 'name')
                                            ->options(function () {
                                                return Permission::where('name', 'like', 'scholar.%')
                                                    ->pluck('name', 'id')
                                                    ->mapWithKeys(fn ($name, $id) => [$id => ucwords(str_replace(['.', '_'], ' ', $name))]);
                                            })
                                            ->columns(2)
                                            ->gridDirection('row')
                                            ->bulkToggleable(),
                                    ]),
                                
                                Forms\Components\Tabs\Tab::make('User Management')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('permissions')
                                            ->label('')
                                            ->relationship('permissions', 'name')
                                            ->options(function () {
                                                return Permission::where('name', 'like', 'user.%')
                                                    ->pluck('name', 'id')
                                                    ->mapWithKeys(fn ($name, $id) => [$id => ucwords(str_replace(['.', '_'], ' ', $name))]);
                                            })
                                            ->columns(2)
                                            ->gridDirection('row')
                                            ->bulkToggleable(),
                                    ]),
                                
                                Forms\Components\Tabs\Tab::make('Role & Permission Management')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('permissions')
                                            ->label('')
                                            ->relationship('permissions', 'name')
                                            ->options(function () {
                                                return Permission::whereIn('name', [
                                                    'role.view', 'role.create', 'role.edit', 'role.delete',
                                                    'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
                                                ])
                                                    ->pluck('name', 'id')
                                                    ->mapWithKeys(fn ($name, $id) => [$id => ucwords(str_replace(['.', '_'], ' ', $name))]);
                                            })
                                            ->columns(2)
                                            ->gridDirection('row')
                                            ->bulkToggleable(),
                                    ]),
                                
                                Forms\Components\Tabs\Tab::make('Dashboard & Reports')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('permissions')
                                            ->label('')
                                            ->relationship('permissions', 'name')
                                            ->options(function () {
                                                return Permission::where(function ($query) {
                                                    $query->where('name', 'like', 'dashboard.%')
                                                        ->orWhere('name', 'like', 'report.%');
                                                })
                                                    ->pluck('name', 'id')
                                                    ->mapWithKeys(fn ($name, $id) => [$id => ucwords(str_replace(['.', '_'], ' ', $name))]);
                                            })
                                            ->columns(2)
                                            ->gridDirection('row')
                                            ->bulkToggleable(),
                                    ]),
                                
                                Forms\Components\Tabs\Tab::make('System Settings')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('permissions')
                                            ->label('')
                                            ->relationship('permissions', 'name')
                                            ->options(function () {
                                                return Permission::where('name', 'like', 'settings.%')
                                                    ->pluck('name', 'id')
                                                    ->mapWithKeys(fn ($name, $id) => [$id => ucwords(str_replace(['.', '_'], ' ', $name))]);
                                            })
                                            ->columns(2)
                                            ->gridDirection('row')
                                            ->bulkToggleable(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'System Admin' => 'danger',
                        'Committee Member' => 'warning',
                        'Scholar' => 'success',
                        'Applicant' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to delete this role? This action cannot be undone.'),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
