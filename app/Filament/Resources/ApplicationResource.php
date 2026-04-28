<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use App\Models\Scholar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Application Management';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('application.view_any');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('application.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('application.delete');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('application.view');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Applicant Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')->label('Account Name'),
                        Infolists\Components\TextEntry::make('user.email')->label('Email Address'),
                        Infolists\Components\TextEntry::make('personal_info.first_name')->label('First Name'),
                        Infolists\Components\TextEntry::make('personal_info.last_name')->label('Last Name'),
                        Infolists\Components\TextEntry::make('personal_info.phone')->label('Phone Number'),
                        Infolists\Components\TextEntry::make('personal_info.address')->label('Address'),
                        Infolists\Components\TextEntry::make('personal_info.has_disability')
                            ->label('Person with Disability')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'yes' => 'Yes',
                                'no' => 'No',
                                'prefer_not_to_answer' => 'Prefer Not to Answer',
                                default => 'Not Specified'
                            })
                            ->badge()
                            ->color(fn ($state) => $state === 'yes' ? 'warning' : 'gray'),
                        Infolists\Components\TextEntry::make('personal_info.disability_details')
                            ->label('Disability Details')
                            ->visible(fn ($record) => ($record->personal_info['has_disability'] ?? '') === 'yes'),
                        Infolists\Components\TextEntry::make('personal_info.refugee_or_displaced')
                            ->label('Refugee or Displaced Person')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'yes' => 'Yes',
                                'no' => 'No',
                                'prefer_not_to_answer' => 'Prefer Not to Answer',
                                default => 'Not Specified'
                            })
                            ->badge()
                            ->color(fn ($state) => $state === 'yes' ? 'warning' : 'gray'),
                        Infolists\Components\TextEntry::make('personal_info.refugee_details')
                            ->label('Refugee/Displaced Details')
                            ->visible(fn ($record) => ($record->personal_info['refugee_or_displaced'] ?? '') === 'yes'),
                        Infolists\Components\TextEntry::make('personal_info.residence_area')
                            ->label('Residence Area')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'rural' => 'Rural Area',
                                'urban' => 'Urban Area',
                                default => 'Not Specified'
                            })
                            ->badge()
                            ->color(fn ($state) => $state === 'rural' ? 'success' : 'info'),
                    ])->columns(3),
                
                Infolists\Components\Section::make('Financial Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('financial_info.household_income')->label('Household Income')->money('usd'),
                        Infolists\Components\TextEntry::make('financial_info.number_of_dependents')->label('Dependents'),
                    ])->columns(2),

                Infolists\Components\Section::make('Guardian Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('guardian_info.guardian_name')->label('Name'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_phone')->label('Phone'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_relation')->label('Relation'),
                    ])->columns(3),

                Infolists\Components\Section::make('Essays / Statements')
                    ->schema([
                        Infolists\Components\TextEntry::make('essay.personal_statement')->label('Personal Statement')->columnSpanFull()->html(),
                        Infolists\Components\TextEntry::make('essay.commitment')->label('Commitment Essay')->columnSpanFull()->html(),
                    ])->collapsible(),

                Infolists\Components\Section::make('Uploaded Documents')
                    ->schema([
                        Infolists\Components\TextEntry::make('documents.academic_documents')
                            ->label('Academic Documents')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : 'Not uploaded')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.national_id')
                            ->label('National ID')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : 'Not uploaded')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.admission_form')
                            ->label('Admission Form')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : 'Not uploaded')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.provisional_results')
                            ->label('Provisional Results')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : 'Not uploaded')
                            ->html(),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Scoring Breakdown')
                    ->schema([
                        Infolists\Components\TextEntry::make('scoring_breakdown.financial_need')->label('Financial Need (Max 30)'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.academic_merit')->label('Academic Merit (Max 25)'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.demographics')->label('Demographics (Max 15)'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.commitment')->label('Commitment (Max 15)'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.essay_quality')->label('Essay Quality (Max 15)'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.total')->label('Total Score')->weight('bold')->size('lg')->color('primary'),
                    ])->columns(3)->collapsed(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('personal_info.first_name')
                    ->label('First Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('personal_info.last_name')
                    ->label('Last Name')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'submitted',
                        'warning' => 'under_review',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'draft',
                    ]),
                Tables\Columns\TextColumn::make('scoring_breakdown.total')
                    ->label('Total Score')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state < 60 => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn () => auth()->user()->can('application.view')),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->can('application.edit')),
                Tables\Actions\Action::make('Under Review')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Application $record) => $record->update(['status' => 'under_review']))
                    ->hidden(fn (Application $record) => $record->status !== 'submitted')
                    ->visible(fn () => auth()->user()->can('application.review')),

                Tables\Actions\Action::make('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Application $record) {
                        $record->update(['status' => 'approved']);
                        
                        // Assign Scholar role
                        $user = $record->user;
                        if (!$user->hasRole('Scholar')) {
                            $user->assignRole('Scholar');
                        }

                        \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\ApplicationApproved($record));

                        // Create Scholar record
                        \App\Models\Scholar::firstOrCreate(
                            ['user_id' => $user->id],
                            ['application_id' => $record->id, 'university' => 'Pending Entry', 'course' => 'Pending Entry', 'student_id' => 'TBD']
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Application Approved')
                            ->success()
                            ->body("{$user->name} has been marked as a Scholar.")
                            ->send();
                    })
                    ->hidden(fn (Application $record) => in_array($record->status, ['approved', 'draft']))
                    ->visible(fn () => auth()->user()->can('application.approve')),

                Tables\Actions\Action::make('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Application $record) => $record->update(['status' => 'rejected']))
                    ->hidden(fn (Application $record) => in_array($record->status, ['rejected', 'approved']))
                    ->visible(fn () => auth()->user()->can('application.reject')),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'view' => Pages\ViewApplication::route('/{record}'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
