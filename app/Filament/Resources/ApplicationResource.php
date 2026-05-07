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
                        Infolists\Components\TextEntry::make('personal_info.first_name')
                            ->label('First Name')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.middle_name')
                            ->label('Middle Name')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.last_name')
                            ->label('Last Name')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.date_of_birth')
                            ->label('Date of Birth')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.gender')
                            ->label('Gender')
                            ->placeholder('Not provided')
                            ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.phone')
                            ->label('Phone Number')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.address')
                            ->label('Address')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('personal_info.city')
                            ->label('City')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.state')
                            ->label('State/Province')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.country')
                            ->label('Country')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.postal_code')
                            ->label('Postal Code')
                            ->placeholder('Not provided'),
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
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
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
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
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
                
                Infolists\Components\Section::make('Academic Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('personal_info.current_education_level')
                            ->label('Current Education Level')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.institution_name')
                            ->label('Institution Name')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.field_of_study')
                            ->label('Field of Study')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.gpa')
                            ->label('GPA/Grade')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.intended_university')
                            ->label('Intended University')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.intended_course')
                            ->label('Intended Course')
                            ->placeholder('Not provided'),
                    ])->columns(3)->collapsible(),
                
                Infolists\Components\Section::make('Financial Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('financial_info.household_income')
                            ->label('Household Income')
                            ->money('usd')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('financial_info.number_of_dependents')
                            ->label('Number of Dependents')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('financial_info.employment_status')
                            ->label('Employment Status')
                            ->placeholder('Not provided')
                            ->formatStateUsing(fn ($state) => $state ? ucwords(str_replace('_', ' ', $state)) : 'Not provided'),
                        Infolists\Components\TextEntry::make('financial_info.other_scholarships')
                            ->label('Other Scholarships')
                            ->placeholder('Not provided')
                            ->formatStateUsing(fn ($state) => $state === 'yes' ? 'Yes' : ($state === 'no' ? 'No' : 'Not provided')),
                        Infolists\Components\TextEntry::make('financial_info.scholarship_details')
                            ->label('Scholarship Details')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('financial_info.financial_need_explanation')
                            ->label('Financial Need Explanation')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Guardian Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('guardian_info.guardian_name')
                            ->label('Guardian Name')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_phone')
                            ->label('Guardian Phone')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_email')
                            ->label('Guardian Email')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_relation')
                            ->label('Relation to Applicant')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_address')
                            ->label('Guardian Address')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Essays / Statements')
                    ->schema([
                        Infolists\Components\TextEntry::make('essay.personal_statement')
                            ->label('Personal Statement')
                            ->placeholder('Not provided')
                            ->columnSpanFull()
                            ->html()
                            ->formatStateUsing(fn ($state) => $state ? nl2br(e($state)) : '<em class="text-gray-400">Not provided</em>'),
                        Infolists\Components\TextEntry::make('essay.commitment')
                            ->label('Commitment Essay')
                            ->placeholder('Not provided')
                            ->columnSpanFull()
                            ->html()
                            ->formatStateUsing(fn ($state) => $state ? nl2br(e($state)) : '<em class="text-gray-400">Not provided</em>'),
                        Infolists\Components\TextEntry::make('essay.career_goals')
                            ->label('Career Goals')
                            ->placeholder('Not provided')
                            ->columnSpanFull()
                            ->html()
                            ->formatStateUsing(fn ($state) => $state ? nl2br(e($state)) : '<em class="text-gray-400">Not provided</em>'),
                    ])->collapsible(),

                Infolists\Components\Section::make('Uploaded Documents')
                    ->schema([
                        Infolists\Components\TextEntry::make('documents.academic_documents')
                            ->label('Academic Documents')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.national_id')
                            ->label('National ID')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.admission_form')
                            ->label('Admission Form')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.provisional_results')
                            ->label('Provisional Results')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.recommendation_letter')
                            ->label('Recommendation Letter')
                            ->formatStateUsing(fn ($state) => $state ? '<a href="'.asset('storage/'.$state).'" target="_blank" class="text-blue-600 hover:underline">View Document</a>' : '<em class="text-gray-400">Not uploaded</em>')
                            ->html(),
                        Infolists\Components\TextEntry::make('documents.proof_of_income')
                            ->label('Proof of Income')
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
