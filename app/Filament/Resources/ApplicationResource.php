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
        $docEntry = function (string $field, string $label) {
            return Infolists\Components\TextEntry::make("documents.{$field}")
                ->label($label)
                ->formatStateUsing(function ($state) use ($field) {
                    if (!$state) {
                        return '<em class="text-gray-400">Not uploaded</em>';
                    }
                    $url      = asset('storage/' . $state);
                    $download = route('admin.documents.download', ['path' => base64_encode($state)]);
                    $ext      = strtolower(pathinfo($state, PATHINFO_EXTENSION));

                    // Pass data via HTML attributes so Livewire DOM morphing cannot
                    // strip or encode them. Alpine's x-on:click is preserved by
                    // Livewire whereas plain onclick attributes are sanitized away.
                    $attrUrl   = htmlspecialchars($url,   ENT_QUOTES, 'UTF-8');
                    $attrExt   = htmlspecialchars($ext,   ENT_QUOTES, 'UTF-8');
                    $attrLabel = htmlspecialchars($field, ENT_QUOTES, 'UTF-8');

                    return <<<HTML
                        <span class="inline-flex items-center gap-2">
                            <button
                                type="button"
                                x-data
                                data-url="{$attrUrl}"
                                data-ext="{$attrExt}"
                                data-label="{$attrLabel}"
                                x-on:click="\$dispatch('open-document-viewer', { url: \$el.dataset.url, ext: \$el.dataset.ext, label: \$el.dataset.label })"
                                class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700 hover:underline"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </button>
                            <a
                                href="{$download}"
                                class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-gray-800 hover:underline"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                        </span>
                    HTML;
                })
                ->html();
        };

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
                            ->label('Account Name')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Account Email')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.surname')
                            ->label('Surname')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.other_names')
                            ->label('Other Name(s)')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.date_of_birth')
                            ->label('Date of Birth')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.phone')
                            ->label('Telephone')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.email')
                            ->label('Personal Email')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.marital_status')
                            ->label('Marital Status')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.is_ugandan')
                            ->label('Ugandan National')
                            ->formatStateUsing(fn ($state) => $state === 'yes' ? 'Yes' : ($state === 'no' ? 'No' : 'Not specified'))
                            ->badge()
                            ->color(fn ($state) => $state === 'yes' ? 'success' : 'warning'),

                        // Ugandan — NIN only
                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('personal_info.nin')
                                ->label('National Identification Number (NIN)')
                                ->placeholder('Not provided'),
                        ])
                            ->visible(fn ($record) => ($record->personal_info['is_ugandan'] ?? null) === 'yes'),

                        // Non-Ugandan — alternative ID fields
                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('personal_info.passport_number')
                                ->label('Passport Number')
                                ->placeholder('Not provided'),
                            Infolists\Components\TextEntry::make('personal_info.foreign_id_number')
                                ->label('National ID No. (Country of Origin)')
                                ->placeholder('Not provided'),
                            Infolists\Components\TextEntry::make('personal_info.refugee_card_number')
                                ->label('Refugee Card Number')
                                ->placeholder('Not provided'),
                            Infolists\Components\TextEntry::make('personal_info.non_ugandan_explanation')
                                ->label('Nationality')
                                ->placeholder('Not provided'),
                        ])
                            ->visible(fn ($record) => ($record->personal_info['is_ugandan'] ?? null) === 'no'),
                        Infolists\Components\TextEntry::make('personal_info.has_disability')
                            ->label('Has Disability')
                            ->formatStateUsing(fn ($state) => $state === 'yes' ? 'Yes' : ($state === 'no' ? 'No' : 'Not specified'))
                            ->badge()
                            ->color(fn ($state) => $state === 'yes' ? 'warning' : 'gray'),
                        Infolists\Components\TextEntry::make('personal_info.disability_specify')
                            ->label('Disability Details')->placeholder('Not provided'),
                    ])->columns(3),

                Infolists\Components\Section::make('Next of Kin')
                    ->schema([
                        Infolists\Components\ViewEntry::make('personal_info.next_of_kin')
                            ->label('')
                            ->columnSpanFull()
                            ->view('filament.infolists.next-of-kin-table'),
                    ])->collapsible(),

                Infolists\Components\Section::make('Place of Birth / Origin / Residence')
                    ->schema([
                        Infolists\Components\TextEntry::make('personal_info.birth_village')->label('Birth Village/Parish')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.birth_district')->label('Birth District')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.birth_region')->label('Birth Region')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.birth_country')->label('Birth Country')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.origin_village')->label('Origin Village/Parish')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.origin_district')->label('Origin District')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.origin_region')->label('Origin Region')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.origin_country')->label('Origin Country')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.residence_village')->label('Residence Village/Parish')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.residence_district')->label('Residence District')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.residence_region')->label('Residence Region')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.residence_country')->label('Residence Country')->placeholder('Not provided'),
                    ])->columns(4)->collapsible(),

                Infolists\Components\Section::make('Academic Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('personal_info.academic_programme')
                            ->label('Academic Programme')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.institution')
                            ->label('Institution')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.teaching_subjects_1')
                            ->label('Teaching Subject 1')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.teaching_subjects_2')
                            ->label('Teaching Subject 2')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('personal_info.student_admission_number')
                            ->label('Student Admission Number')->placeholder('Not provided'),
                    ])->columns(3)->collapsible(),

                Infolists\Components\Section::make('Schools Attended')
                    ->schema([
                        Infolists\Components\ViewEntry::make('personal_info')
                            ->label('')
                            ->columnSpanFull()
                            ->view('filament.infolists.schools-attended-table'),
                    ])->columns(1)->collapsible(),

                Infolists\Components\Section::make('Mode of Admission to University')
                    ->schema([
                        Infolists\Components\ViewEntry::make('personal_info')
                            ->label('')
                            ->columnSpanFull()
                            ->view('filament.infolists.admission-mode-table'),
                    ])->columns(1)->collapsible(),

                Infolists\Components\Section::make('Disability Information (Section B2)')
                    ->schema([
                        Infolists\Components\TextEntry::make('disability_info.functionality_level')
                            ->label('Functionality Level')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('disability_info.assistive_support')
                            ->label('Assistive Support Needed')->placeholder('Not provided')
                            ->columnSpanFull(),
                        Infolists\Components\IconEntry::make('disability_info.difficulty_walking')->label('Difficulty Walking')->boolean(),
                        Infolists\Components\IconEntry::make('disability_info.difficulty_seeing')->label('Difficulty Seeing')->boolean(),
                        Infolists\Components\IconEntry::make('disability_info.difficulty_hearing')->label('Difficulty Hearing')->boolean(),
                        Infolists\Components\IconEntry::make('disability_info.difficulty_communicating')->label('Difficulty Communicating')->boolean(),
                        Infolists\Components\IconEntry::make('disability_info.difficulty_picking')->label('Difficulty Picking Objects')->boolean(),
                        Infolists\Components\IconEntry::make('disability_info.difficulty_self_care')->label('Difficulty Self-Care')->boolean(),
                        Infolists\Components\IconEntry::make('disability_info.difficulty_emotions')->label('Difficulty Controlling Emotions')->boolean(),
                    ])->columns(4)->collapsible(),

                Infolists\Components\Section::make('Dependants Information (Section B3)')
                    ->schema([
                        Infolists\Components\TextEntry::make('dependants_info.spouse_surname')->label('Spouse Surname')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.spouse_other_names')->label('Spouse Other Names')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.spouse_education_level')->label('Spouse Education Level')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.spouse_occupation')->label('Spouse Occupation')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.num_children')->label('Number of Children')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.oldest_child_age')->label('Age of Oldest Child')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.youngest_child_age')->label('Age of Youngest Child')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('dependants_info.marriage_balance_plan')->label('Marriage/Studies Balance Plan')->placeholder('Not provided')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('dependants_info.childcare_plan')->label('Childcare Plan')->placeholder('Not provided')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('dependants_info.spouse_support')->label('Support from Spouse')->placeholder('Not provided')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('dependants_info.non_financial_support_needed')->label('Non-Financial Support Needed')->placeholder('Not provided')->columnSpanFull(),
                    ])->columns(4)->collapsible(),

                Infolists\Components\Section::make('Guardian / Parent Information (Section C)')
                    ->schema([
                        Infolists\Components\TextEntry::make('guardian_info.guardian_surname')->label('Surname')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_other_names')->label('Other Name(s)')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_telephone')->label('Telephone')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_relation')->label('Relation to Applicant')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_occupation')->label('Occupation')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_country')->label('Country of Residence')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_district')->label('District of Residence')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_region')->label('Region of Residence')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('guardian_info.guardian_address')->label('Village/Address')->placeholder('Not provided')->columnSpanFull(),
                    ])->columns(3)->collapsible(),

                Infolists\Components\Section::make('Criminal Offence Declaration (Section D)')
                    ->schema([
                        Infolists\Components\TextEntry::make('declaration_info.criminal_offence')
                            ->label('Ever charged/convicted of a criminal offence?')
                            ->formatStateUsing(fn ($state) => $state === 'yes' ? 'Yes' : ($state === 'no' ? 'No' : 'Not answered'))
                            ->badge()
                            ->color(fn ($state) => $state === 'yes' ? 'danger' : 'success'),
                        Infolists\Components\TextEntry::make('declaration_info.criminal_details')
                            ->label('Details')->placeholder('N/A')->columnSpanFull(),
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
                        $docEntry('exam_results',         'Examination Results'),
                        $docEntry('national_id',          'National ID'),
                        $docEntry('birth_certificate',    'Birth Certificate'),
                        $docEntry('admission_letter',     'Admission Letter'),
                        $docEntry('recommendation_lc1',   'Recommendation Letter (LC1)'),
                        $docEntry('recommendation_school','Recommendation Letter (School)'),
                        $docEntry('refugee_number',       'Refugee Number'),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Financial Information (Section B5)')
                    ->schema([
                        Infolists\Components\TextEntry::make('financial_info.household_income')
                            ->label('Annual Household Income (UGX)')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('financial_info.number_of_dependents')
                            ->label('Number of Dependents')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('financial_info.income_source')
                            ->label('Primary Income Source')->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('financial_info.other_financial_support')
                            ->label('Other Financial Support')->placeholder('Not provided'),
                    ])->columns(2)->collapsible(),

                Infolists\Components\Section::make('Source of Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('personal_info.hearing_source')
                            ->label('How They Heard About the Scholarship')
                            ->placeholder('Not provided')
                            ->formatStateUsing(function ($state) {
                                $labels = [
                                    'organization_website' => 'Organization website',
                                    'social_media'         => 'Social media (WhatsApp, Facebook, Twitter, Instagram)',
                                    'referral'             => 'Referral from a friend or colleague',
                                    'advertisement'        => 'Advertisement (TV, radio, newspaper)',
                                    'professional_network' => 'Professional network or industry contacts',
                                    'email_newsletter'     => 'Email newsletter or scholarship alert',
                                    'walk_in'              => 'Walk-in / Direct visit to the organization',
                                    'other'                => 'Other',
                                ];
                                return $labels[$state] ?? $state;
                            })
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('personal_info.hearing_source_other')
                            ->label('Other (Specified)')
                            ->placeholder('N/A')
                            ->visible(fn ($record) => ($record->personal_info['hearing_source'] ?? null) === 'other'),
                    ])->columns(2)->collapsible(),
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
                Tables\Columns\TextColumn::make('applicant_full_name')
                    ->label('Applicant')
                    ->getStateUsing(fn ($record): string => trim(
                        ($record->personal_info['surname'] ?? '')
                        . ' ' .
                        ($record->personal_info['other_names'] ?? '')
                    ) ?: ($record->user->name ?? '—'))
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('personal_info->surname', 'like', "%{$search}%")
                              ->orWhere('personal_info->other_names', 'like', "%{$search}%")
                              ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
                        });
                    }),

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

                Tables\Columns\IconColumn::make('refugee')
                    ->label('Refugee')
                    ->getStateUsing(function ($record): bool {
                        $info = $record->personal_info ?? [];
                        return ($info['is_ugandan'] ?? null) === 'no'
                            && !empty($info['refugee_card_number']);
                    })
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle'),

                Tables\Columns\TextColumn::make('district_of_origin')
                    ->label('District of Origin')
                    ->getStateUsing(fn ($record): string =>
                        $record->personal_info['origin_district'] ?? '—'
                    ),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary'   => 'submitted',
                        'warning'   => 'under_review',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                        'secondary' => 'draft',
                    ])
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state ?? ''))),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'        => 'Draft',
                        'submitted'    => 'Submitted',
                        'under_review' => 'Under Review',
                        'approved'     => 'Approved',
                        'rejected'     => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn () => auth()->user()->can('application.view')),

                Tables\Actions\Action::make('change_status')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->can('application.review')
                        || auth()->user()->can('application.approve')
                        || auth()->user()->can('application.reject'))
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('New Status')
                            ->options([
                                'submitted'    => 'Submitted',
                                'under_review' => 'Under Review',
                                'approved'     => 'Approved',
                                'rejected'     => 'Rejected',
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn (Application $record): array => [
                        'status' => $record->status,
                    ])
                    ->modalHeading('Change Application Status')
                    ->modalSubmitActionLabel('Update Status')
                    ->action(function (Application $record, array $data): void {
                        $newStatus = $data['status'];
                        $oldStatus = $record->status;

                        if ($newStatus === $oldStatus) {
                            return;
                        }

                        $record->update(['status' => $newStatus]);

                        if ($newStatus === 'approved') {
                            $user = $record->user;
                            if (!$user->hasRole('Scholar')) {
                                $user->assignRole('Scholar');
                            }
                            \App\Models\Scholar::firstOrCreate(
                                ['user_id' => $user->id],
                                ['application_id' => $record->id, 'university' => 'Pending Entry', 'course' => 'Pending Entry', 'student_id' => 'TBD']
                            );
                        }

                        try {
                            match ($newStatus) {
                                'approved' => \Illuminate\Support\Facades\Mail::to($record->user)
                                    ->send(new \App\Mail\ApplicationApproved($record)),
                                'rejected' => \Illuminate\Support\Facades\Mail::to($record->user)
                                    ->send(new \App\Mail\ApplicationRejected($record)),
                                default    => \Illuminate\Support\Facades\Mail::to($record->user)
                                    ->send(new \App\Mail\ApplicationStatusUpdated($record, $oldStatus, $newStatus)),
                            };

                            activity('email')
                                ->causedBy(auth()->user())
                                ->performedOn($record)
                                ->withProperties([
                                    'recipient' => $record->user->email,
                                    'from'      => $oldStatus,
                                    'to'        => $newStatus,
                                ])
                                ->log('Email sent: Application status changed');
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send status change email: ' . $e->getMessage());
                            activity('email')
                                ->causedBy(auth()->user())
                                ->performedOn($record)
                                ->withProperties(['error' => $e->getMessage()])
                                ->log('Email failed: Application status change notification');
                        }

                        Notification::make()
                            ->title('Status Updated')
                            ->success()
                            ->body('Application status changed to ' . ucwords(str_replace('_', ' ', $newStatus)) . '.')
                            ->send();
                    }),
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
