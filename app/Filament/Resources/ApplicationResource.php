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
                Infolists\Components\Section::make('Application Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Current Status')
                            ->badge()
                            ->color(fn ($state): string => match ($state) {
                                'draft' => 'gray',
                                'submitted' => 'primary',
                                'under_review' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Submitted On')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make('Applicant Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Account Name')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email Address')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.surname')
                            ->label('Surname')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.other_names')
                            ->label('Other Name(s)')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.date_of_birth')
                            ->label('Date of Birth')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.nin')
                            ->label('NIN')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.phone')
                            ->label('Telephone')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.email')
                            ->label('Email')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.marital_status')
                            ->label('Marital Status')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.is_ugandan')
                            ->label('Ugandan National')
                            ->formatStateUsing(fn ($state) => $state === 'yes' ? 'Yes' : ($state === 'no' ? 'No' : 'Not specified'))
                            ->badge()
                            ->color(fn ($state) => $state === 'yes' ? 'success' : 'warning'),
                        Infolists\Components\TextEntry::make('personal_info.has_disability')
                            ->label('Has Disability')
                            ->formatStateUsing(fn ($state) => $state === 'yes' ? 'Yes' : ($state === 'no' ? 'No' : 'Not specified'))
                            ->badge()
                            ->color(fn ($state) => $state === 'yes' ? 'warning' : 'gray'),
                        Infolists\Components\TextEntry::make('personal_info.disability_specify')
                            ->label('Disability Specified')
                            ->placeholder('Not provided'),
                    ])->columns(3),

                Infolists\Components\Section::make('Academic Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('personal_info.academic_programme')
                            ->label('Academic Programme')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.institution')
                            ->label('Institution')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.teaching_subjects_1')
                            ->label('Teaching Subject 1')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.teaching_subjects_2')
                            ->label('Teaching Subject 2')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.student_admission_number')
                            ->label('Student Admission Number')
                            ->placeholder('Not provided'),
                    ])->columns(3)->collapsible(),

                Infolists\Components\Section::make('Disability Information (Section B2)')
                    ->schema([
                        Infolists\Components\TextEntry::make('disability_info.functionality_level')
                            ->label('Functionality Level')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('disability_info.assistive_support')
                            ->label('Assistive Support Needed')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Dependants Information (Section B3)')
                    ->schema([
                        Infolists\Components\TextEntry::make('dependants_info.num_children')
                            ->label('Number of Children')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.oldest_child_age')
                            ->label('Age of Oldest Child')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.youngest_child_age')
                            ->label('Age of Youngest Child')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.childcare_plan')
                            ->label('Childcare Plan')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                    ])->columns(3)->collapsible(),

                Infolists\Components\Section::make('Guardian Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('guardian_info.guardian_surname')
                            ->label('Guardian Surname')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_other_names')
                            ->label('Guardian Other Name(s)')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_telephone')
                            ->label('Guardian Telephone')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_relation')
                            ->label('Relation to Applicant')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_occupation')
                            ->label('Occupation')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_district')
                            ->label('District of Residence')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_region')
                            ->label('Region of Residence')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_address')
                            ->label('Address')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Motivation Statement (Section B6)')
                    ->schema([
                        Infolists\Components\TextEntry::make('essay.motivation')
                            ->label('Motivation (250-word essay)')
                            ->placeholder('Not provided')
                            ->columnSpanFull()
                            ->html()
                            ->formatStateUsing(fn ($state) => $state ? nl2br(e($state)) : '<em class="text-gray-400">Not provided</em>'),
                    ])->collapsible(),

                Infolists\Components\Section::make('Uploaded Documents')
                    ->schema([
                        Infolists\Components\TextEntry::make('documents.exam_results')
                            ->label('Examination Results')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.national_id')
                            ->label('National ID')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.birth_certificate')
                            ->label('Birth Certificate')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.admission_letter')
                            ->label('Admission Letter')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.recommendation_lc1')
                            ->label('Recommendation Letter (LC1)')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.recommendation_school')
                            ->label('Recommendation Letter (School)')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.refugee_number')
                            ->label('Refugee Number')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Scoring Breakdown')
                    ->description('Use the "Edit Scoring" button above to modify scores')
                    ->schema([
                        Infolists\Components\TextEntry::make('scoring_breakdown.financial_need')
                            ->label('Financial Need (Max 30)')
                            ->placeholder('0')
                            ->formatStateUsing(fn ($state) => $state ?? '0')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.academic_merit')
                            ->label('Academic Merit (Max 25)')
                            ->placeholder('0')
                            ->formatStateUsing(fn ($state) => $state ?? '0')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.demographics')
                            ->label('Demographics (Max 15)')
                            ->placeholder('0')
                            ->formatStateUsing(fn ($state) => $state ?? '0')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.commitment')
                            ->label('Commitment (Max 15)')
                            ->placeholder('0')
                            ->formatStateUsing(fn ($state) => $state ?? '0')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.essay_quality')
                            ->label('Essay Quality (Max 15)')
                            ->placeholder('0')
                            ->formatStateUsing(fn ($state) => $state ?? '0')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('scoring_breakdown.total')
                            ->label('Total Score (Max 100)')
                            ->placeholder('0')
                            ->formatStateUsing(fn ($state) => ($state ?? '0') . ' / 100')
                            ->weight('bold')
                            ->size('lg')
                            ->badge()
                            ->color(fn ($state): string => match (true) {
                                $state >= 80 => 'success',
                                $state >= 60 => 'warning',
                                $state < 60 => 'danger',
                                default => 'secondary',
                            }),
                    ])->columns(3),
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
                    ->action(function (Application $record) {
                        $oldStatus = $record->status;
                        $record->update(['status' => 'under_review']);
                        
                        // Send status update email
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->user)->send(
                                new \App\Mail\ApplicationStatusUpdated($record, $oldStatus, 'under_review')
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send status update email: ' . $e->getMessage());
                        }
                    })
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

                        // Send approval email
                        try {
                            \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\ApplicationApproved($record));
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send approval email: ' . $e->getMessage());
                        }

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
                    ->action(function (Application $record) {
                        $record->update(['status' => 'rejected']);
                        
                        // Send rejection email
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->user)->send(
                                new \App\Mail\ApplicationRejected($record)
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send rejection email: ' . $e->getMessage());
                        }
                    })
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
