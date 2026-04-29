<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class SystemUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'System Users';
    
    protected static ?string $modelLabel = 'System User';
    
    protected static ?string $navigationGroup = 'System Administration';
    
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('user.manage_system_users') || auth()->user()->can('user.view_any');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('user.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('user.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('user.delete');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('user.view');
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show users with System Admin or Committee Member roles
        return parent::getEloquentQuery()
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['System Admin', 'Committee Member']);
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
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->label('Password')
                            ->helperText(fn (string $context): string => 
                                $context === 'create' 
                                    ? 'Leave blank to send a password setup link via email. The user will set their own password.' 
                                    : 'Leave blank to keep current password'
                            ),
                    ])->columns(2),
                
                Forms\Components\Section::make('Role Assignment')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                return \Spatie\Permission\Models\Role::whereIn('name', ['System Admin', 'Committee Member'])
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                            ->helperText('Primary role determines base permissions')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Show role permissions info when role changes
                            }),
                    ]),
                
                Forms\Components\Section::make('Additional Permissions')
                    ->description('Grant individual permissions beyond the user\'s role. These permissions are added to (not replacing) the role\'s permissions.')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                return \Spatie\Permission\Models\Permission::all()
                                    ->pluck('name', 'id')
                                    ->map(fn ($name) => ucwords(str_replace(['.', '_'], ' ', $name)));
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
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->colors([
                        'danger' => 'System Admin',
                        'warning' => 'Committee Member',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Direct Permissions')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable()
                    ->limit(3)
                    ->tooltip(function (User $record): ?string {
                        $permissions = $record->permissions->pluck('name')->toArray();
                        return count($permissions) > 0 ? implode(', ', $permissions) : null;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->options([
                        'System Admin' => 'System Admin',
                        'Committee Member' => 'Committee Member',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modal()
                    ->modalWidth('4xl'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSystemUsers::route('/'),
        ];
    }
}
