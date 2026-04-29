<?php

namespace App\Filament\Resources\ApplicantUserResource\Pages;

use App\Filament\Resources\ApplicantUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplicantUsers extends ListRecords
{
    protected static string $resource = ApplicantUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modal()
                ->modalWidth('4xl'),
        ];
    }
}
