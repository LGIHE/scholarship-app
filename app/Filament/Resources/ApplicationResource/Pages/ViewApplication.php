<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Notifications\Notification;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            // Action to edit scoring breakdown
            Actions\Action::make('editScoring')
                ->label('Edit Scoring')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->visible(fn () => auth()->user()->can('application.review'))
                ->form([
                    Forms\Components\Section::make('Scoring Breakdown')
                        ->description('Modify the scores for each criterion. Total will be calculated automatically.')
                        ->schema([
                            Forms\Components\TextInput::make('financial_need')
                                ->label('Financial Need (Max 30)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(30)
                                ->default(fn ($record) => $record->scoring_breakdown['financial_need'] ?? 0)
                                ->required(),
                            Forms\Components\TextInput::make('academic_merit')
                                ->label('Academic Merit (Max 25)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(25)
                                ->default(fn ($record) => $record->scoring_breakdown['academic_merit'] ?? 0)
                                ->required(),
                            Forms\Components\TextInput::make('demographics')
                                ->label('Demographics (Max 15)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(15)
                                ->default(fn ($record) => $record->scoring_breakdown['demographics'] ?? 0)
                                ->required(),
                            Forms\Components\TextInput::make('commitment')
                                ->label('Commitment (Max 15)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(15)
                                ->default(fn ($record) => $record->scoring_breakdown['commitment'] ?? 0)
                                ->required(),
                            Forms\Components\TextInput::make('essay_quality')
                                ->label('Essay Quality (Max 15)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(15)
                                ->default(fn ($record) => $record->scoring_breakdown['essay_quality'] ?? 0)
                                ->required(),
                        ])->columns(2),
                ])
                ->action(function (Application $record, array $data) {
                    $total = $data['financial_need'] + $data['academic_merit'] + 
                             $data['demographics'] + $data['commitment'] + $data['essay_quality'];
                    
                    $scoringBreakdown = [
                        'financial_need' => $data['financial_need'],
                        'academic_merit' => $data['academic_merit'],
                        'demographics' => $data['demographics'],
                        'commitment' => $data['commitment'],
                        'essay_quality' => $data['essay_quality'],
                        'total' => $total,
                    ];
                    
                    $record->update(['scoring_breakdown' => $scoringBreakdown]);
                    
                    Notification::make()
                        ->title('Scoring Updated')
                        ->success()
                        ->body("Total score: {$total}/100")
                        ->send();
                }),
            
            // Action to change application status
            Actions\Action::make('changeStatus')
                ->label('Change Status')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->visible(fn () => auth()->user()->can('application.review'))
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('Application Status')
                        ->options([
                            'draft' => 'Draft',
                            'submitted' => 'Submitted',
                            'under_review' => 'Under Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default(fn ($record) => $record->status)
                        ->required()
                        ->native(false),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes (Optional)')
                        ->placeholder('Add any notes about this status change...')
                        ->rows(3),
                ])
                ->action(function (Application $record, array $data) {
                    $oldStatus = $record->status;
                    $newStatus = $data['status'];
                    
                    if ($oldStatus === $newStatus) {
                        Notification::make()
                            ->title('No Change')
                            ->warning()
                            ->body('The status is already set to ' . ucfirst($newStatus))
                            ->send();
                        return;
                    }
                    
                    $record->update(['status' => $newStatus]);
                    
                    // Handle special status changes
                    if ($newStatus === 'approved') {
                        $user = $record->user;
                        if (!$user->hasRole('Scholar')) {
                            $user->assignRole('Scholar');
                        }
                        
                        // Create Scholar record
                        \App\Models\Scholar::firstOrCreate(
                            ['user_id' => $user->id],
                            [
                                'application_id' => $record->id,
                                'university' => 'Pending Entry',
                                'course' => 'Pending Entry',
                                'student_id' => 'TBD'
                            ]
                        );
                        
                        // Send approval email
                        try {
                            \Illuminate\Support\Facades\Mail::to($user)->send(
                                new \App\Mail\ApplicationApproved($record)
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send approval email: ' . $e->getMessage());
                        }
                    } elseif ($newStatus === 'rejected') {
                        // Send rejection email
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->user)->send(
                                new \App\Mail\ApplicationRejected($record)
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send rejection email: ' . $e->getMessage());
                        }
                    } else {
                        // Send general status update email
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->user)->send(
                                new \App\Mail\ApplicationStatusUpdated($record, $oldStatus, $newStatus)
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send status update email: ' . $e->getMessage());
                        }
                    }
                    
                    Notification::make()
                        ->title('Status Updated')
                        ->success()
                        ->body("Application status changed from {$oldStatus} to {$newStatus}")
                        ->send();
                }),
        ];
    }
}
