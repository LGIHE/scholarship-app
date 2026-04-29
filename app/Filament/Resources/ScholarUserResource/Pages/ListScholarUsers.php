<?php

namespace App\Filament\Resources\ScholarUserResource\Pages;

use App\Filament\Resources\ScholarUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScholarUsers extends ListRecords
{
    protected static string $resource = ScholarUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modal()
                ->modalWidth('4xl'),
        ];
    }
}
