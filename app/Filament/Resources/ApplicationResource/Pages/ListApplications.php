<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Exports\ApplicationsExport;
use App\Filament\Resources\ApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn (): BinaryFileResponse => Excel::download(
                    new ApplicationsExport(),
                    'applications_' . now()->format('Y-m-d_His') . '.xlsx'
                )),
            Actions\CreateAction::make(),
        ];
    }
}
