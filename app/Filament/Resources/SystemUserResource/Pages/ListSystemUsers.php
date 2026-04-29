<?php

namespace App\Filament\Resources\SystemUserResource\Pages;

use App\Filament\Resources\SystemUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemUsers extends ListRecords
{
    protected static string $resource = SystemUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modal()
                ->modalWidth('4xl')
                ->mutateFormDataUsing(function (array $data): array {
                    // If no password provided, we'll create the user without a password
                    // and send them a setup link
                    if (empty($data['password'])) {
                        // Don't set a password - leave it null
                        unset($data['password']);
                        $data['send_setup_email'] = true;
                    } else {
                        $data['send_setup_email'] = false;
                    }
                    return $data;
                })
                ->after(function ($record, array $data) {
                    // Send setup email if password was not provided
                    if ($data['send_setup_email'] ?? false) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($record)->send(
                                new \App\Mail\SystemUserCreated($record)
                            );
                            
                            // Show success notification
                            \Filament\Notifications\Notification::make()
                                ->title('User Created Successfully')
                                ->body('Password setup email sent to ' . $record->email)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send system user creation email: ' . $e->getMessage());
                            
                            // Show notification to admin
                            \Filament\Notifications\Notification::make()
                                ->title('User Created')
                                ->body('User created successfully, but failed to send setup email. Please manually send password reset link.')
                                ->warning()
                                ->send();
                        }
                    } else {
                        // Show success notification for users with password
                        \Filament\Notifications\Notification::make()
                            ->title('User Created Successfully')
                            ->body('User can now log in with the provided password.')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
