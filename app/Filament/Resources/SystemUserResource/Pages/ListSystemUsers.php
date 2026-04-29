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
                    // Generate a temporary password if creating new user
                    if (empty($data['password'])) {
                        $data['temporary_password'] = \Illuminate\Support\Str::random(12);
                        $data['password'] = $data['temporary_password'];
                    } else {
                        $data['temporary_password'] = $data['password'];
                    }
                    return $data;
                })
                ->after(function ($record, array $data) {
                    // Send welcome email with temporary password
                    if (isset($data['temporary_password'])) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($record)->send(
                                new \App\Mail\SystemUserCreated($record, $data['temporary_password'])
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send system user creation email: ' . $e->getMessage());
                        }
                    }
                }),
        ];
    }
}
