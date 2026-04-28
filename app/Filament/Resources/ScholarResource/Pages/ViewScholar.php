<?php

namespace App\Filament\Resources\ScholarResource\Pages;

use App\Filament\Resources\ScholarResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewScholar extends ViewRecord
{
    protected static string $resource = ScholarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Scholar Information')
                    ->tabs([
                        // Bio Tab
                        Infolists\Components\Tabs\Tab::make('Bio')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Infolists\Components\Section::make('Personal Information')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('user.name')
                                            ->label('Full Name')
                                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                            ->weight('bold'),
                                        Infolists\Components\TextEntry::make('user.email')
                                            ->label('Email Address')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable(),
                                        Infolists\Components\TextEntry::make('user.created_at')
                                            ->label('Account Created')
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar'),
                                    ])->columns(3),
                                
                                Infolists\Components\Section::make('Academic Information')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('university')
                                            ->label('University')
                                            ->icon('heroicon-o-building-library')
                                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                        Infolists\Components\TextEntry::make('course')
                                            ->label('Course/Program')
                                            ->icon('heroicon-o-academic-cap'),
                                        Infolists\Components\TextEntry::make('student_id')
                                            ->label('Student ID')
                                            ->icon('heroicon-o-identification')
                                            ->placeholder('Not Set'),
                                        Infolists\Components\TextEntry::make('graduation_date')
                                            ->label('Expected Graduation')
                                            ->date()
                                            ->icon('heroicon-o-calendar-days')
                                            ->placeholder('Not Set'),
                                    ])->columns(2),
                                
                                Infolists\Components\Section::make('Scholarship Details')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label('Scholarship Start Date')
                                            ->date()
                                            ->icon('heroicon-o-calendar'),
                                        Infolists\Components\TextEntry::make('user.roles.0.name')
                                            ->label('Current Status')
                                            ->badge()
                                            ->color('success'),
                                    ])->columns(2),
                            ]),
                        
                        // Applications Tab
                        Infolists\Components\Tabs\Tab::make('Applications')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Infolists\Components\Section::make('Original Application')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('application.id')
                                            ->label('Application ID')
                                            ->badge()
                                            ->color('info')
                                            ->visible(fn ($record) => $record->application !== null),
                                        Infolists\Components\TextEntry::make('application.status')
                                            ->label('Application Status')
                                            ->badge()
                                            ->color(fn (string $state = null): string => match ($state) {
                                                'approved' => 'success',
                                                'pending' => 'warning',
                                                'rejected' => 'danger',
                                                default => 'gray',
                                            })
                                            ->visible(fn ($record) => $record->application !== null),
                                        Infolists\Components\TextEntry::make('application.created_at')
                                            ->label('Application Date')
                                            ->dateTime()
                                            ->visible(fn ($record) => $record->application !== null),
                                    ])->columns(3)
                                    ->visible(fn ($record) => $record->application !== null),
                                
                                Infolists\Components\Section::make('Personal Information')
                                    ->schema([
                                        Infolists\Components\KeyValueEntry::make('application.personal_info')
                                            ->label('')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn ($record) => $record->application !== null && !empty($record->application->personal_info)),
                                
                                Infolists\Components\Section::make('Financial Information')
                                    ->schema([
                                        Infolists\Components\KeyValueEntry::make('application.financial_info')
                                            ->label('')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn ($record) => $record->application !== null && !empty($record->application->financial_info)),
                                
                                Infolists\Components\Section::make('Guardian Information')
                                    ->schema([
                                        Infolists\Components\KeyValueEntry::make('application.guardian_info')
                                            ->label('')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn ($record) => $record->application !== null && !empty($record->application->guardian_info)),
                                
                                Infolists\Components\Section::make('Essay')
                                    ->schema([
                                        Infolists\Components\KeyValueEntry::make('application.essay')
                                            ->label('')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn ($record) => $record->application !== null && !empty($record->application->essay)),
                                
                                Infolists\Components\Section::make('No Application')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('no_application')
                                            ->label('')
                                            ->default('No application record linked to this scholar.')
                                            ->color('warning'),
                                    ])
                                    ->visible(fn ($record) => $record->application === null),
                            ]),
                        
                        // Progress Tab
                        Infolists\Components\Tabs\Tab::make('Academic Progress')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Infolists\Components\Section::make('Progress Records')
                                    ->schema([
                                        Infolists\Components\RepeatableEntry::make('academicProgress')
                                            ->label('')
                                            ->schema([
                                                Infolists\Components\TextEntry::make('semester')
                                                    ->label('Semester')
                                                    ->badge()
                                                    ->color('info'),
                                                Infolists\Components\TextEntry::make('year')
                                                    ->label('Year')
                                                    ->badge(),
                                                Infolists\Components\TextEntry::make('cgpa')
                                                    ->label('CGPA')
                                                    ->badge()
                                                    ->color(fn ($state): string => match (true) {
                                                        $state >= 3.5 => 'success',
                                                        $state >= 3.0 => 'info',
                                                        $state >= 2.5 => 'warning',
                                                        default => 'danger',
                                                    }),
                                                Infolists\Components\TextEntry::make('transcript_path')
                                                    ->label('Transcript')
                                                    ->placeholder('Not uploaded')
                                                    ->formatStateUsing(fn ($state) => $state ? 'View Document' : 'Not uploaded')
                                                    ->url(fn ($state) => $state ? asset('storage/' . $state) : null)
                                                    ->openUrlInNewTab()
                                                    ->color('primary'),
                                                Infolists\Components\TextEntry::make('created_at')
                                                    ->label('Recorded On')
                                                    ->dateTime(),
                                            ])
                                            ->columns(5)
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn ($record) => $record->academicProgress->count() > 0),
                                
                                Infolists\Components\Section::make('No Progress Records')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('no_progress')
                                            ->label('')
                                            ->default('No academic progress records have been added yet.')
                                            ->color('warning'),
                                    ])
                                    ->visible(fn ($record) => $record->academicProgress->count() === 0),
                            ]),
                        
                        // Documents Tab
                        Infolists\Components\Tabs\Tab::make('Documents')
                            ->icon('heroicon-o-document-duplicate')
                            ->schema([
                                Infolists\Components\Section::make('Transcripts')
                                    ->schema([
                                        Infolists\Components\RepeatableEntry::make('academicProgress')
                                            ->label('')
                                            ->schema([
                                                Infolists\Components\TextEntry::make('semester')
                                                    ->label('Semester'),
                                                Infolists\Components\TextEntry::make('year')
                                                    ->label('Year'),
                                                Infolists\Components\TextEntry::make('transcript_path')
                                                    ->label('Document')
                                                    ->placeholder('Not uploaded')
                                                    ->formatStateUsing(fn ($state) => $state ? basename($state) : 'Not uploaded')
                                                    ->url(fn ($state) => $state ? asset('storage/' . $state) : null)
                                                    ->openUrlInNewTab()
                                                    ->color('primary'),
                                            ])
                                            ->columns(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn ($record) => $record->academicProgress->whereNotNull('transcript_path')->count() > 0),
                                
                                Infolists\Components\Section::make('No Documents')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('no_documents')
                                            ->label('')
                                            ->default('No documents have been uploaded yet.')
                                            ->color('warning'),
                                    ])
                                    ->visible(fn ($record) => $record->academicProgress->whereNotNull('transcript_path')->count() === 0),
                            ]),
                        
                        // Activity Tab
                        Infolists\Components\Tabs\Tab::make('Activity')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Infolists\Components\Section::make('Timeline')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label('Scholar Record Created')
                                            ->dateTime()
                                            ->icon('heroicon-o-plus-circle')
                                            ->color('success'),
                                        Infolists\Components\TextEntry::make('updated_at')
                                            ->label('Last Updated')
                                            ->dateTime()
                                            ->icon('heroicon-o-pencil')
                                            ->color('info'),
                                        Infolists\Components\TextEntry::make('user.email_verified_at')
                                            ->label('Email Verified')
                                            ->dateTime()
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success')
                                            ->placeholder('Not verified'),
                                    ])->columns(3),
                                
                                Infolists\Components\Section::make('Statistics')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('academicProgress_count')
                                            ->label('Total Progress Records')
                                            ->state(fn ($record) => $record->academicProgress->count())
                                            ->badge()
                                            ->color('info'),
                                        Infolists\Components\TextEntry::make('average_cgpa')
                                            ->label('Average CGPA')
                                            ->state(fn ($record) => $record->academicProgress->count() > 0 
                                                ? number_format($record->academicProgress->avg('cgpa'), 2) 
                                                : 'N/A')
                                            ->badge()
                                            ->color(function ($record): string {
                                                if ($record->academicProgress->count() === 0) {
                                                    return 'gray';
                                                }
                                                $avg = $record->academicProgress->avg('cgpa');
                                                if ($avg >= 3.5) return 'success';
                                                if ($avg >= 3.0) return 'info';
                                                if ($avg >= 2.5) return 'warning';
                                                return 'gray';
                                            }),
                                        Infolists\Components\TextEntry::make('documents_count')
                                            ->label('Uploaded Documents')
                                            ->state(fn ($record) => $record->academicProgress->whereNotNull('transcript_path')->count())
                                            ->badge()
                                            ->color('primary'),
                                    ])->columns(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
