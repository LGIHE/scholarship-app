<?php

namespace App\Filament\Resources\CohortResource\Pages;

use App\Filament\Resources\CohortResource;
use App\Models\Cohort;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCohort extends EditRecord
{
    protected static string $resource = CohortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * After saving, enforce the single-active-cohort rule if is_active was set.
     */
    protected function afterSave(): void
    {
        if ($this->record->is_active) {
            Cohort::activateOnly($this->record);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
