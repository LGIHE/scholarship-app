<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class ApplicantUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Applicants';
    
    protected static ?string $modelLabel = 'Applicant';
    
    protected static ?string $navigationGroup = 'Application Management';
    
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('user.manage_applicants') || auth()->user()->can('user.view_any');
    }

    public static function canCreate(): bool
    {
        return false;
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
        // Only show users with Applicant role (not yet approved)
        return parent::getEloquentQuery()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Applicant');
            })
            ->whereDoesntHave('roles', function ($query) {
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
                
                Forms\Components\Section::make('Role Assignment')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                return \Spatie\Permission\Models\Role::where('name', 'Applicant')
                                    ->pluck('name', 'id');
                            })
                            ->default(function () {
                                return \Spatie\Permission\Models\Role::where('name', 'Applicant')->first()?->id;
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                            ->helperText('Primary role determines base permissions'),
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
                        'info' => 'Applicant',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Direct Permissions')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->toggleable()
                    ->limit(3)
                    ->tooltip(function (User $record): ?string {
                        $permissions = $record->permissions->pluck('name')->toArray();
                        return count($permissions) > 0 ? implode(', ', $permissions) : null;
                    }),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Applications')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->options([
                        'Applicant' => 'Applicant',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->modal()
                    ->modalWidth('4xl'),
                Tables\Actions\Action::make('view_applications')
                    ->label('View Applications')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (User $record): string => route('filament.admin.resources.applications.index', [
                        'tableFilters' => [
                            'user' => ['value' => $record->id]
                        ]
                    ]))
                    ->visible(fn (User $record): bool => $record->applications()->count() > 0)
                    ->color('primary'),
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
            'index' => Pages\ListApplicantUsers::route('/'),
        ];
    }
}
